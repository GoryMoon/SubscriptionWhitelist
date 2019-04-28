<template>
<div class="form-inline mb-2 mt-3">
    <label for="search-bar" class="mt-1 mr-2">Search for:</label>
    <input id="search-bar" type="text" v-model="filterText" class="form-control mt-1 mr-2" @keyup.enter="search" placeholder="Username">
    <button class="btn btn-primary mt-1 mr-2" @click="search"><fa icon="search"></fa> Search</button>
    <button class="btn btn-secondary mt-1" @click="resetFilter"><fa icon="times"></fa> Reset</button>
</div>
</template>

<script>
    export default {
        data () {
            return {
                filterText: ''
            }
        },
        methods: {
            search() {
                if (this.filterText === "") {
                    this.resetFilter();
                } else {
                    this.$emit('set', this.filterText)
                }
            },
            resetFilter () {
                this.filterText = '';
                this.$emit('reset');
            }
        }
    }
</script>