<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NavLink from "@/Components/NavLink.vue";
import axios from "axios";

defineProps(['projects', 'project'])
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
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
                            <tbody v-for="p in projects">
                            <tr>
                                <td>
                                    {{ p.title }}
                                </td>
                                <td>
                                    {{ p.description }}
                                </td>
                                <td><a :href="route('projects.edit', p.id)"
                                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">EDIT</a>
                                    | <a v-on:click="axios.delete(route('projects.destroy', p.id))"
                                         class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">TRASH</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">Tasks For Project</div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
