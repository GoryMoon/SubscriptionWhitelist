<template>
    <form @submit="checkForm" :action="route('broadcaster.list.add')" method="POST">
        <slot></slot>
        <b-alert :show="error" variant="danger">
            You need to enter at least one name.
        </b-alert>
        <div class="form-row" v-for="(name, index) in nameList">
            <b-form-group class="form-group col-10 col-md-8">
                <b-form-input name="usernames[]"
                              aria-label="Username"
                              :id="'add-name-' + index"
                              placeholder="Username"
                              v-bind:value="name.name"
                              @input="name.name = update(index, $event)">
                </b-form-input>
            </b-form-group>
            <b-form-group v-if="index" class="col-1">
                <b-button variant="danger" tabindex="-1" @click="nameList.splice(index, 1)"><fa icon="times"></fa></b-button>
            </b-form-group>
            <b-form-group v-else class="col-1">
                <b-button variant="success" tabindex="-1" @click="addInput"><fa icon="plus"></fa></b-button>
            </b-form-group>
        </div>
        <b-button type="submit" variant="primary"><fa icon="plus"></fa> Add</b-button>
    </form>
</template>

<script>
export default {
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
