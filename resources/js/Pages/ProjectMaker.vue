<script>
import {Head, useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
    name: "ProjectMaker",
    components: {Checkbox, Head, AuthenticatedLayout, PrimaryButton},
    props: {
        employees: Array
    },
    data() {
        return {
            form: useForm({
                title: '',
                description: '',
                members: 'no assignees currently',
            }),
        };
    },
    mounted() {
        this.load();
    },
    methods: {
        load() {

        },
        submitForm() {
            this.form.post(route('projects.store'))
            console.log(this.form);
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




