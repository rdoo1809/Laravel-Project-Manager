<script>
import {Head} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "ProjectEditor",
    components: {Head, AuthenticatedLayout, PrimaryButton},
    props: {
        projects: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            projectList: this.projects,
            selectedProject: null,
            projectTasks: null,
            selectedTask: null,
            taskAssignees: null
        };
    },
    methods: {
        async deleteProject(project) {
            try {
                await axios.delete(route('projects.destroy', project.id))
                window.location.reload();
            } catch (e) {
                alert(e);
            }
        },
        async selectProject(project) {
            this.selectedProject = project;
            try {
                let taskResponse = await axios.get(route('projects.tasks.show', this.selectedProject))
                this.projectTasks = taskResponse.data.project_tasks;
            } catch (e) {
                alert(e);
            }
        },
        async selectTask(task) {
            this.selectedTask = task;
            try {
                let taskResponse = await axios.get(route('projects.tasks.assignees', this.selectedTask))
                // this.taskAssignees = taskResponse
                console.log(taskResponse);
            } catch (e) {
                alert(e);
            }
        }
    }
}
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project Management Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">My Projects</div>
                        <table class="border-2 w-full">
                            <tr>
                                <td class="font-bold">Project</td>
                                <td class="font-bold">Description</td>
                                <td class="font-bold">Actions</td>
                            </tr>
                            <tbody v-for="p in projectList" :key="p.id">
                            <tr :class="{'bg-blue-200': selectedProject?.id === p.id, 'cursor-pointer': true}"
                                @click="selectProject(p)">
                                <td>
                                    {{ p.title }}
                                </td>
                                <td>
                                    {{ p.description }}
                                </td>
                                <td><a :href="route('projects.edit', p.id)"
                                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">EDIT</a>
                                    <a class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                       @click="deleteProject(p)">TRASH</a>
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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">Active Tasks For Project - {{ selectedProject?.title }}</div>
                        <table class="border-2 w-full">
                            <tr>
                                <td class="font-bold">Task</td>
                                <td class="font-bold">Project</td>
                                <td class="font-bold">Actions</td>
                            </tr>

                            <tbody v-for="task in this.projectTasks" :key="task.id">
                            <tr :class="{'bg-blue-200': selectedTask?.id === task.id, 'cursor-pointer': true}"
                                @click="selectTask(task)">
                                <td>{{ task.task }}</td>
                                <td>{{ task.project_id }}</td>
                                <td>View History</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">Task {{ }}</div>
                        <table class="border-2 w-full">
                            <tr>
                                <td class="font-bold">Employee Name</td>
                                <td class="font-bold">Email</td>
                                <td class="font-bold">Actions</td>
                            </tr>
                            <tbody v-for="member in this.taskAssignees" :key="member.id">
                            <tr>
                                <td>{{ member.name }}</td>
                                <td>{{ member.email }}</td>
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
