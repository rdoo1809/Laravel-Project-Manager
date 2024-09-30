<script>
import {Head, useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Checkbox from "@/Components/Checkbox.vue";
import axios from "axios";

export default {
    name: "ProjectMaker",
    components: {Checkbox, Head, AuthenticatedLayout, PrimaryButton},
    props: {
        employees: Array,
        errors: Object
    },
    data() {
        return {
            form: useForm({
                title: '',
                description: '',
                members: [],
            }),
        };
    },
    mounted() {
        this.load();
    },
    methods: {
        load() {

        },
        async submitForm() {
            try {
                await axios.post(route('projects.store'), this.form)
                window.location.assign('/dashboard');
                // redirect to the dashboard
            } catch (e) {
                alert('Something went wrong!');
            }

            // this.form.post(route('projects.store'))
        }
    }
}
</script>


<template>
    <Head title="Project Maker"/>
    <AuthenticatedLayout>
        <div class="p-24">
            <h1>Add New Projects to your Team</h1>
            <hr>
            <form @submit.prevent="submitForm"
                  class="py-12 flex flex-col space-y-4 items-center">
                <h4>{{ errors }}</h4>
                <label>
                    Project Name:
                    <input v-model="form.title" type="text"/>
                </label>

                <label>
                    Description:
                    <input v-model="form.description" type="text"/>
                </label>

                <label>
                    Available Employees:
                    <hr>
                    <ul>
                        <li v-for="regular in employees">{{ regular.name }}
                            <input class="mx-2" type="checkbox"/>
                        </li>
                    </ul>
                </label>

                <PrimaryButton class="mt-4">Create Project</PrimaryButton>
            </form>
        </div>
    </AuthenticatedLayout>
</template>




