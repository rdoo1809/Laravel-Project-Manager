<script>
import {Head, useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "ProjectEditor",
    components: {Head, AuthenticatedLayout, PrimaryButton},
    props: {
        selectedProject: {
            type: Object,
            required: true
        },
        allEmployees: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            form: useForm({
                title: this.selectedProject.title,
                description: this.selectedProject.description,
                members: this.selectedProject.assignees,
            }),
        };
    },
    methods: {
        submitForm() {
            this.form.put(route('projects.update', this.selectedProject))
            console.log(this.form);
        },
        logger() {
            console.log(this.form.members)
        }
    },
}
</script>


<template>
    <Head title="Project Editor"/>
    <AuthenticatedLayout>
        <div class="p-24">
            <h1>Edit Your Project - #{{ selectedProject.id }}</h1>
            <hr>
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <h4>Project Details</h4>
                            <form class="py-12 flex flex-col space-y-4 items-center"
                                  @submit.prevent="submitForm">
                                <label>
                                    Project Name:
                                    <input v-model="form.title" type="text"/>
                                </label>

                                <label>
                                    Description:
                                    <textarea v-model="form.description" type="text"/>
                                </label>
                                <!--                                <PrimaryButton class="mt-4">Update Project</PrimaryButton>-->
                            </form>

                        </div>
                    </div>
                </div>

                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <h4>Project Members</h4>
                            <form class="py-12 flex flex-col space-y-4 items-center"
                                  @submit.prevent="submitForm">
                                <label>
                                    <strong>Project Manager:</strong>
                                    {{ selectedProject.assignees[0].name }}
                                </label>

                                <strong>Project Team:</strong>
                                <label>
                                    <ul>
                                        <li v-for="regular in selectedProject.assignees.filter(e => e.is_manager === 0)"
                                            :key="regular.id">
                                            {{ regular.name }}
                                            <input v-model="form.members" :value="regular"
                                                   class="mx-2" type="checkbox" @click="logger()"/>
                                        </li>
                                    </ul>
                                </label>

                                <label>
                                    <strong>Add Members to This Project</strong>
                                    <ul>
                                        <li v-for="available in this.allEmployees">
                                            {{ available.name }}
                                            <input v-model="form.members"
                                                   :value="available"
                                                   class="mx-2" type="checkbox" @click="logger()"/>
                                        </li>
                                    </ul>
                                </label>
                            </form>
                        </div>
                    </div>
                    <PrimaryButton class="mt-4" @click="submitForm">Update Project</PrimaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>




