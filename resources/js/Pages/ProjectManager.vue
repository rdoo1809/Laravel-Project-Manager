<script>
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    name: "ProjectManager",
    components: {PrimaryButton},
    data() {
        return {
            form: useForm({
                title: '',
                description: '',
                members: 'no assignees currently',
            }),
        };
    },
    methods: {
        submitForm() {
            this.form.post(route('project.store'))
            console.log(this.form);
        }
    }
}
</script>


<template>
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
                Assign to:
                <input placeholder="dropdown? available team members?" type="text"/>
            </label>

            <PrimaryButton class="mt-4">Create Project</PrimaryButton>
        </form>
    </div>
</template>




