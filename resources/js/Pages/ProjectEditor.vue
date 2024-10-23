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
            type: Object,
            required: true
        },
        taskList: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            form: useForm({
                title: this.selectedProject.title,
                description: this.selectedProject.description,
                members: [],
                removeMembers: []
            }),
            task: null,
            taskErrors: null,
        };
    },
    methods: {
        async submitForm() {
            try {
                // await axios.put(route('projects.update'), this.form)
                //todo make axios request - render errors on screen
                this.form.put(route('projects.update', this.selectedProject))
            } catch (e) {
                alert('whoops!');
                console.log(e);
            }
        },
        async submitTask() {
            try {
                console.log(this.task)
                await axios.post(route('projects.tasks.store', this.selectedProject), {
                    'task': this.task
                })
                window.location.reload();
            } catch (e) {
                console.log(e)
                this.taskErrors = e.response.data.message
            }
        }
    },
}
</script>

<template>
    <Head title="Project Editor"/>
    <AuthenticatedLayout>
        <div class="p-24">
            <h1>Project Details - #{{ selectedProject.id }}</h1>
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
                            </form>
                        </div>
                    </div>
                </div>

                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <h4>Project Members</h4>
                            <form class="py-12 flex flex-col space-y-4 items-center">
                                <label>
                                    <strong>Project Manager:</strong>
                                    {{ selectedProject.assignees[0].name }}
                                </label>


                                <p><strong>Project Team:</strong> Select checkbox to remove from project</p>
                                <label>
                                    <ul>
                                        <li v-for="regular in selectedProject.assignees.filter(e => e.is_manager === 0)"
                                            :key="regular.id">
                                            {{ regular.name }}
                                            <input v-model="form.removeMembers" :value="regular.id"
                                                   class="mx-2" type="checkbox"/>
                                        </li>
                                    </ul>
                                </label>

                                <label>
                                    <strong>Add Members to This Project</strong>
                                    <ul>
                                        <li v-for="available in this.allEmployees">
                                            {{ available.name }}
                                            <input v-model="form.members"
                                                   :value="available.id"
                                                   class="mx-2" type="checkbox"/>
                                        </li>
                                    </ul>
                                </label>
                            </form>
                        </div>
                    </div>
                    <PrimaryButton class="mt-4" @click="submitForm">Update Project</PrimaryButton>
                </div>
            </div>
            <hr>
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <h4>Task Management</h4>
                            <form class="py-12 flex flex-col space-y-4 items-center"
                                  @submit.prevent="submitTask">
                                <label>
                                    Task Description:
                                    <input v-model="task" type="text"/>
                                </label>
                                <p>{{ this.taskErrors }}</p>
                                <PrimaryButton class="mt-4">Add Task</PrimaryButton>
                            </form>

                            <strong><p>Tasks for Project</p></strong>
                            <ul>
                                <li v-for="task in this.taskList">
                                    <p>{{ task.id }} - {{ task.task }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>




