<script>
import {Head} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "Dashboard",
    components: {Head, AuthenticatedLayout, PrimaryButton},
    props: {
        projects: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            selectedProject: null,
            selectedTask: null,
            taskAssignees: null
        };
    },
    methods: {
        async deleteProject(projectId) {
            try {
                await axios.delete(route('projects.destroy', projectId))
                window.location.reload();
            } catch (e) {
                alert(e);
            }
        },
        selectProject(project) {
            this.selectedProject = project;
        },
        selectTask(task) {
            this.selectedTask = task;
            // try {
            //     let taskResponse = await axios.get(route('projects.tasks.assignees', this.selectedTask))
            //     // this.taskAssignees = taskResponse
            //     console.log(taskResponse);
            // } catch (e) {
            //     alert(e);
            // }
        }
    }
}
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight dark:text-white">Project Manager 5001</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-blue-900">
                    <div class="p-6 text-gray-900 dark:text-white">{{ $page.props.auth.user.name }} - You're Logged in!
                        Now Get to
                        Work!
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6 text-gray-900 dark:text-white">My Projects</div>
                        <table class="border-2 w-full">
                            <tr>
                                <td class="font-bold">Project</td>
                                <td class="font-bold">Description</td>
                                <td class="font-bold">Actions</td>
                            </tr>
                            <tbody v-for="p in projects.data" :key="p.project_id">
                            <tr :class="{'bg-blue-900': selectedProject?.project_id === p.project_id, 'cursor-pointer': true}"
                                @click="selectProject(p)">
                                <td>
                                    {{ p.project_name }}
                                </td>
                                <td>
                                    {{ p.project_description }}
                                </td>
                                <td><a :href="route('projects.edit', p.project_id)"
                                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">EDIT</a>
                                    <a class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                       @click="deleteProject(p.project_id)">TRASH</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6 text-gray-900 dark:text-white">{{ this.selectedProject?.title }} Members</div>
                        <table class="border-2 w-full">
                            <tr>
                                <td class="font-bold">Member Name</td>
                                <td class="font-bold">Contact</td>
                                <td class="font-bold">Role</td>
                            </tr>
                            <tbody v-for="a in this.selectedProject?.project_members" :key="a.id">
                            <tr>
                                <td>
                                    {{ a.member_name }}
                                </td>
                                <td>
                                    {{ a.member_email }}
                                </td>
                                <td>
                                    {{ a.is_manager ? 'Manager' : 'Regular' }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6 text-gray-900 dark:text-white">Active Tasks For Project - {{
                                selectedProject?.project_name
                            }}
                        </div>
                        <table class="border-2 w-full">
                            <tr>
                                <td class="font-bold">Task</td>
                                <td class="font-bold">Actions</td>
                            </tr>
                            <tbody v-for="task in this.selectedProject?.project_tasks" :key="task.task_id">
                            <tr :class="{'bg-blue-900': selectedTask?.task_id === task.task_id, 'cursor-pointer': true}"
                                @click="selectTask(task)">
                                <td>{{ task.task }}</td>
                                <td>View History</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6 text-gray-900 dark:text-white">Task #{{ this.selectedTask?.task_id }}
                            Assignees
                        </div>
                        <table class="border-2 w-full dark:text-white">
                            <tr>
                                <td class="font-bold">Employee Name</td>
                                <td class="font-bold">Email</td>
                                <td class="font-bold">Actions</td>
                            </tr>
                            <tbody v-for="member in this.selectedTask?.assignees" :key="member.id">
                            <tr>
                                <td>{{ member.assignee_name }}</td>
                                <td>{{ member.assignee_email }}</td>
                                <td>View Profile</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
