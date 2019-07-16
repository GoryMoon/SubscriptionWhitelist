<template>
    <form ref="form" method="POST" :action="route('subscriber.save', {channel: id})">
        <slot name="csrf"></slot>
        <input ref="hiddenInput" type="hidden" name="_method" value="PUT">
        <div class="form-group">
            <label :for="getId">Username:</label>
            <img v-if="hasMcName" class="minecraft_logo" src="/images/minecraft_logo_success.png" data-toggle="tooltip" data-placement="top" :title="'Minecraft name: ' + mc_name">
            <img v-else class="minecraft_logo" src="/images/minecraft_logo_error.png" data-toggle="tooltip" data-placement="top" title="No Minecraft name found for this username">
            <input type="text"
                   :disabled="!isValid"
                   :class="getClasses"
                   :id="getId"
                   :name="getId"
                   v-model="input_name"
            >
            <slot name="error-alert"></slot>
        </div>
        <div class="form-group">
            <button v-if="isValid" @click="save" :disabled="unChanged" class="subscribe-edit btn btn-primary mb-2"><fa icon="save"></fa> Save</button>
            <button @click="remove" class="subscribe-edit ml-1 btn btn-danger mb-2"><fa icon="trash"></fa> Delete</button>
        </div>
    </form>
</template>

<script>
export default {
    props: {
        id: {
            type: String,
            required: true
        },
        uid: {
            type: String,
            required: true
        },
        minecraft: {
            type: String,
            required: false
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
    data() {
        return {
            mc_name: this.minecraft,
            input_name: this.username,
            orig_name: this.username
        }
    },
    created() {
        Echo.private(`users.${this.uid}`)
            .notification((notification) => {
                if (notification.type === "App\\Notifications\\MCUserSyncDone") {
                    this.mc_name = notification.name;
                    refreshTooltips();
                }
            });
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
        },
        hasMcName() {
            return this.mc_name !== '';
        },
        unChanged() {
            return this.orig_name === this.input_name;
        }
    },
    methods: {
        save() {
            if (!this.unChanged()) {
                this.submit()
            }
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