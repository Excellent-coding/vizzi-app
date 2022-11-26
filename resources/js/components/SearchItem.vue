<template>
    <div class="search-sm d-inline-block float-md-left mr-1 align-top">
        <b-input v-model="value" placeholder="User Search"  @input="$emit('set-status', value)" />
    </div>
</template>

<script>
export default {
    data() {
        return {
            value: ''
        }
    }
}
</script>