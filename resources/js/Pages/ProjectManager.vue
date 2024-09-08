<script>
export default {
    name: "ProjectManager"
}
</script>


<template>
    <div class="p-24">
        <h1>Add New Projects to your Team</h1>
        <hr>
        <form class="py-12 flex flex-col space-y-4 items-center">
            <label>
                Project Name:
                <input type="text"/>
            </label>

            <label>
                Description:
                <input type="text"/>
            </label>

            <label>
                Assign to:
                <input placeholder="dropdown? available team members?" type="text"/>
            </label>

            <input type="button" class="bg-blue-500 hover:bg-blue-700 text-white rounded" value="Submit"/>
        </form>
    </div>
</template>




