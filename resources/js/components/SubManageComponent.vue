<template>
    <div>
        <h4>
            Generic username/Minecraft username
        </h4>

        <b-form ref="form" method="POST" :action="route('subscriber.save', {channel: channel_name})">
            <slot name="csrf"></slot>
            <input ref="hiddenInput" type="hidden" name="_method" value="PUT">
            <b-form-group>
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
            </b-form-group>
            <b-form-group>
                <b-button v-if="isValid" @click="save" :disabled="unChanged" variant="primary" class="subscribe-edit mb-2"><fa icon="save"></fa> Save</b-button>
            </b-form-group>
        </b-form>
        <hr>
        <h4>
            <fa :icon="['fab', 'steam']"></fa> Steam
        </h4>
        <div v-if="steam_connected">
            Linking Steam to a whitelist allow the list to use your public SteamID
            <p class="font-weight-bold">
                Status:
                <span
                    v-if="steam_linked"
                    class="text-success">
                    Linked
                </span>
                <span
                    v-else
                    class="text-danger">
                    Not Linked
                </span>
            </p>
            <b-form ref="steam_form" method="POST" :action="route('subscriber.steam.link', {channel: channel_name})">
                <slot name="csrf"></slot>
                <input ref="steamHiddenInput" type="hidden" name="_method" value="POST">
                <b-button @click="steamUnlink" v-if="steam_linked" variant="primary">Unlink Steam from Whitelist</b-button>
                <b-button @click="steamLink" v-else variant="primary">Link Steam to Whitelist</b-button>
            </b-form>
        </div>
        <div v-else>
            <p>You need to link Steam to your account to use it with a whitelist</p>
            <b-button :href="route('profile')" variant="primary"><fa icon="link"></fa> Link Steam</b-button>
        </div>
        <hr>
        <b-button @click="remove" variant="danger" class="subscribe-edit ml-1 mb-2"><fa icon="trash"></fa> Delete</b-button>
    </div>
</template>

<script>
export default {
    props: {
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
        channel_name: {
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
        steam_connected: {
            type: Boolean,
            required: true
        },
        steam_linked: {
            type: Boolean,
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
            return 'username-' + this.channel_name;
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
            if (!this.unChanged) {
                this.submit()
            }
        },
        remove() {
            this.$refs.hiddenInput.value = 'DELETE';
            this.$nextTick(() => this.submit());
        },
        submit() {
            this.$refs.form.submit();
        },
        steamLink() {
            this.steamSubmit()
        },
        steamUnlink() {
            this.$refs.steamHiddenInput.value = 'DELETE';
            this.$nextTick(() => this.steamSubmit());
        },
        steamSubmit() {
            this.$refs.steam_form.submit();
        }
    }
}
</script>
