<template>
    <form ref="form" @submit="validName" class="form-inline" id="delete_form" :action="url" method="POST">
        <slot></slot>
        <label for="login_name" class="sr-only">Enter username to remove</label>
        <input v-model="verifyName" type="text" class="form-control mb-2 mr-sm-2 border-danger" id="login_name" placeholder="Login name">
        <button type="submit" @click="onClick" class="btn btn-danger mb-2 mr-sm-2" :disabled="isDisabled">Delete Account</button>
    </form>
</template>

<script>
export default {
    props: {
        url: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            verifyName: "",
            clicked: false
        }
    },
    computed: {
        isDisabled() {
            return !(this.validName) || this.clicked;
        },
        validName() {
            return this.verifyName === this.name;
        }
    },
    methods: {
        onClick() {
            this.clicked = true;
            this.$refs.form.submit();
        }
    }
}
</script>
