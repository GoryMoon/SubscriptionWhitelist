<template>
    <form @submit="checkForm" :action="this.route" method="POST">
        <slot></slot>
        <div v-if="error" class="alert alert-danger" role="alert">
            You need to enter at least one name.
        </div>
        <div class="form-row" v-for="(name, index) in nameList">
            <div class="form-group col-10 col-md-8">
                <input type="text" class="form-control" name="usernames[]" placeholder="Username" v-bind:value="name.name" v-on:input="name.name = update(index, $event.target.value)">
            </div>
            <div v-if="index" class="form-group col-1">
                <button class="btn btn-danger" @click.prevent v-on:click="nameList.splice(index, 1)"><fa icon="times"></fa></button>
            </div>
            <div v-else class="form-group col-1">
                <button class="btn btn-success" @click.prevent v-on:click="addInput"><fa icon="plus"></fa></button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><fa icon="plus"></fa> Add</button>
    </form>
</template>

<script>
export default {
    props: {
        route: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            error: false,
            nameList: [
                {
                    name: ""
                }
            ]
        }
    },
    methods: {
        update: function (index, data) {
            if (data !== "" && this.nameList[index].name === "" && this.nameList[index + 1] === undefined) {
                this.addInput();
            }
            return data;
        },
        addInput() {
            this.nameList.push({name:''});
        },
        checkForm(e) {
            this.error = this.nameList.filter(value => value.name === "").length === this.nameList.length;
            if (!this.error) {
                return true;
            }
            e.preventDefault();
        }
    }
}
</script>
