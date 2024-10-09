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
        }
    },
    data() {
        return {
            form: useForm({
                title: this.selectedProject.title,
                description: this.selectedProject.description,
                members: this.regularMembers(this.selectedProject.assignees),
            }),
            projectManager: this.manager(this.selectedProject.assignees)
        };
    },
    methods: {
        submitForm() {
            this.form.put(route('projects.update', this.selectedProject))
            console.log(this.form);
        },
        regularMembers(members) {
            return members.filter(member => member.is_manager === 0);
        },
        manager(members) {
            return members.filter(member => member.is_manager === 1);
        }
    },
    computed: {

    }
}
</script>


<template>
    <Head title="Project Editor"/>
    <AuthenticatedLayout>

        <div class="p-24">
            <h1>Edit Your Project - #{{ selectedProject.id }}</h1>
            <hr>
            <form @submit.prevent="submitForm"
                  class="py-12 flex flex-col space-y-4 items-center">
                <label>
                    Project Name:
                    <input v-model="form.title" type="text"/>
                </label>

                <label>
                    Description:
                    <textarea v-model="form.description" type="text"/>
                </label>

                <label>
                    Project Manager:
                    {{ projectManager[0].name }}
                </label>

                <label>
                    Project Team:
                    <ul>
                        <li v-for="regular in form.members">{{ regular.name }}</li>
                    </ul>
                </label>

                <PrimaryButton class="mt-4">Update Project</PrimaryButton>
            </form>
        </div>
    </AuthenticatedLayout>
</template>




