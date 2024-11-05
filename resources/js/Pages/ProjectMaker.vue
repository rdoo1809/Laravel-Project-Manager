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
    },
    data() {
        return {
            errors: null,
            form: useForm({
                title: '',
                description: '',
                members: [],
            }),
        };
    },
    methods: {
        async submitForm() {
            try {
                console.log(this.form)
                await axios.post(route('projects.store'), this.form)
                // window.location.assign('/dashboard');
            } catch (e) {
                console.log(e.response)
                this.errors = e.response.data.errors
            }
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
                <label>
                    Project Title:
                    <input v-model="form.title" class="dark:text-black" type="text"/>
                    <h4 class="text-red-600">{{ errors?.title }}</h4>
                </label>
                <!--                todo render without []?-->
                <label>
                    Description:
                    <input v-model="form.description" class="dark:text-black" type="text"/>
                    <h4 class="text-red-600">{{ errors?.description }}</h4>
                </label>

                <label>
                    Available Employees:
                    <hr>
                    <ul>
                        <li v-for="regular in employees">{{ regular.name }}
                            <input v-model="form.members" :value="regular.id" class="mx-2" type="checkbox"/>
                        </li>
                    </ul>
                </label>

                <PrimaryButton class="mt-4">Create Project</PrimaryButton>
            </form>
        </div>
    </AuthenticatedLayout>
</template>




