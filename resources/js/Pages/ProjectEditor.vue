<script>
import {Head, useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";

export default {
    name: "ProjectEditor",
    components: {Head, AuthenticatedLayout, PrimaryButton},
    props: {
        selectedProject: {
            type: Object,
            required: true
        },
        allEmployees: {
            type: Array,
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
            projectManager: this.manager(this.selectedProject.assignees),
            unassignedEmployees: this.availableEmployees(this.allEmployees)
        };
    },
    methods: {
        axios,
        submitForm() {
            // this.form.put(route('projects.update', this.selectedProject))
            console.log(this.form);
        },
        //todo - possible refactor? one method vs three to decide this data
        regularMembers(members) {
            return members.filter(member => member.is_manager === 0);
        },
        manager(members) {
            return members.filter(member => member.is_manager === 1);
        },
        availableEmployees() {

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
                                    {{ projectManager[0].name }}
                                </label>

                                <strong>Project Team:</strong>
                                <label>
                                    <ul>
                                        <li v-for="regular in form.members">{{ regular.name }}</li>
                                    </ul>
                                </label>

                                <label>
                                    Add Members to This Project
                                    <ul>
                                        <li v-for="availble in this.allEmployees">{{ availble.name }}</li>
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




