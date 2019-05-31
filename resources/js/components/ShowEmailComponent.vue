<template>
    <div>
        <button v-if="!show" @click="show = true" class="btn btn-primary btn-sm">Show email</button>
        <span v-else><a :href="'mailto:' + email">{{ email }}</a></span>
    </div>
</template>
<script>
export default {
    data() {
        return {
            show: false,
            b_email: "d2hpdGVsaXN0QGdvcnltb29uLnNl" //Base64 encoded email
        }
    },
    computed: {
        email() {
            return atob(this.b_email);
        }
    }
}
</script>