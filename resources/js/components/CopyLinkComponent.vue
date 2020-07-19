<template>
    <b-input-group>
        <b-form-input
            type="text"
            v-model="link"
            class="selectable"
            readonly>
        </b-form-input>
        <b-input-group-append id="copy-wrapper" class="d-inline-block" tabindex="0">
            <b-button
                v-clipboard:copy="link"
                v-clipboard:success="onCopy"
                v-clipboard:error="onError"
                v-b-tooltip.hover
                title="Copy link to clipboard"
                variant="outline-primary">
                <fa icon="copy"></fa> Copy
            </b-button>
        </b-input-group-append>
    </b-input-group>
</template>

<script>
    export default {
        props: [
            'link'
        ],
        methods:{
            onCopy(e) {
                this.$bvToast.toast("Copied link", {
                    title: 'Clipboard',
                    variant: 'success',
                    solid: true,
                    autoHideDelay: 1000
                });
            },
            onError() {
                this.$bvToast.toast("Something went wrong when copying the link", {
                    title: 'Failed to copy link',
                    variant: 'error',
                    solid: true,
                    autoHideDelay: 1000
                });
            }
        }
    }
</script>

<style scoped>

</style>
