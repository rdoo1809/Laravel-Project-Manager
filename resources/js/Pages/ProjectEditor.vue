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
                members: 'no assignees currently',
            }),
        };
    },
    methods: {
        submitForm() {
             this.form.put(route('projects.update', this.selectedProject))
             console.log(this.form);
        }
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
                    <input v-model="form.description" type="text"/>
                </label>

                <label>
                    Assign to:
                    <input placeholder="dropdown? available team members?" type="text"/>
                </label>

                <PrimaryButton class="mt-4">Update Project</PrimaryButton>
            </form>
        </div>
    </AuthenticatedLayout>
</template>




