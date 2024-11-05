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
        nonProjectEmployees: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            form: useForm({
                title: this.selectedProject.title,
                description: this.selectedProject.description,
                members: [],
                removeMembers: []
            }),
            taskToAdd: null,
            taskErrors: null,
            modalTask: null,
            modalTaskNonMembers: null,
            addTaskMembers: [],
            modalHidden: true,

        };
    },
    methods: {
        async patchProject() {
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
                console.log(this.taskToAdd)
                await axios.post(route('projects.tasks.store', this.selectedProject), {
                    'task': this.taskToAdd
                })
                window.location.reload();
            } catch (e) {
                console.log(e)
                this.taskErrors = e.response.data.message
            }
        },
        async assignTaskMembers() {
            try {
                console.log(this.addTaskMembers);
                await axios.post(route('projects.tasks.assign', this.modalTask.id), {
                    'addTaskMembers': this.addTaskMembers
                })
            } catch (e) {
                console.log(e);
            }
            this.addTaskMembers = [];
        },
        async showTaskModal(task) {
            try {
                let taskResponse = await axios.get(route('projects.tasks.assignees', [this.selectedProject, task]))
                this.modalTask = taskResponse.data.selectedTask;
                this.modalTaskNonMembers = taskResponse.data.nonAssignees
            } catch (e) {
                alert(e);
            }
            this.modalHidden = false;
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
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <h4>Project Details</h4>
                            <form class="py-12 flex flex-col space-y-4 items-center"
                                  @submit.prevent="patchProject">
                                <label>
                                    Project Name:
                                    <input v-model="form.title" class="dark:text-black" type="text"/>
                                </label>

                                <label>
                                    Description:
                                    <textarea v-model="form.description" class="dark:text-black" type="text"/>
                                </label>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
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
                                        <li v-for="available in this.nonProjectEmployees">
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
                    <PrimaryButton class="mt-4" @click="patchProject">Update Project</PrimaryButton>
                </div>
            </div>

            <hr>
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <h4>Task Management</h4>
                            <form class="py-12 flex flex-col space-y-4 items-center"
                                  @submit.prevent="submitTask">
                                <label>
                                    Task Description:
                                    <input v-model="taskToAdd" class="dark:text-black" type="text"/>
                                </label>
                                <p>{{ this.taskErrors }}</p>
                                <PrimaryButton class="mt-4">Add Task</PrimaryButton>
                            </form>

                            <strong><p>Tasks for Project</p></strong>
                            <ul>
                                <li v-for="task in this.selectedProject.tasks">
                                    {{ task.id }} - {{ task.task }} -
                                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                            @click="showTaskModal(task)">
                                        Assign Team Members
                                    </button>
                                    <hr>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="grid gap-6 lg:gap-8">
                    <div class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                                <div :hidden="this.modalHidden">
                                    <p>Assign Members to Task - #{{ this.modalTask?.id }} -
                                        {{ this.modalTask?.task }}</p>
                                    <ul>
                                        <li v-for="employee in this.modalTaskNonMembers">
                                            {{ employee.name }}
                                            <input v-model="addTaskMembers" :value="employee.id"
                                                   class="mx-2" type="checkbox"/>
                                        </li>
                                    </ul>
                                    <PrimaryButton class="mx-40" @click="assignTaskMembers()">Assign Task
                                        To Selected Members
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>




