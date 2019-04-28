<template>
    <form ref="form" method="POST" :action="route">
        <slot name="csrf"></slot>
        <input ref="hiddenInput" type="hidden" name="_method" value="PUT">
        <div class="form-group">
            <label :for="getId">Username:</label>
            <input type="text"
                   :disabled="!isValid"
                   :class="getClasses"
                   :id="getId"
                   :name="getId"
                   :value="username"
            >
            <slot name="error-alert"></slot>
        </div>
        <div class="form-group">
            <button v-if="isValid" @click="save" class="subscribe-edit btn btn-primary mb-2"><fa icon="save"></fa> Save</button>
            <button @click="remove" class="subscribe-edit ml-1 btn btn-danger mb-2"><fa icon="trash"></fa> Delete</button>
        </div>
    </form>
</template>

<script>
export default {
    props: {
        route: {
            type: String,
            required: true
        },
        id: {
            type: String,
            required: true
        },
        username: {
            type: String,
            required: true
        },
        valid: {
            type: String,
            required: true
        },
        index: {
            type: String,
            required: true
        },
        errorClasses: {
            type: String,
            required: false,
            default: ''
        }
    },
    computed: {
        isValid() {
            return this.valid === '1';
        },
        getId() {
            return 'username-' + this.id;
        },
        getClasses() {
            return 'form-control mr-sm-2 mb-2 ' + this.errorClasses;
        }
    },
    methods: {
        save() {
            this.submit();
        },
        remove() {
            this.$refs.hiddenInput.value = 'DELETE';
            this.$nextTick(() => this.submit());
        },
        submit() {
            this.$refs.form.submit();
        }
    }
}
</script>