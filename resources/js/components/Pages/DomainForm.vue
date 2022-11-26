<template>
    <b-form>
        <b-form-group label="Event Title">
            <b-form-input v-model="newItem.title" />
        </b-form-group>
        <b-form-group :label="$t('menu.domain-name')">
            <b-input-group>
                <b-input-group-prepend><b-input-group-text>Https://</b-input-group-text></b-input-group-prepend>
                <b-form-input v-model="newItem.domain" />
                <b-input-group-append><b-input-group-text>.{{host}}</b-input-group-text></b-input-group-append>
            </b-input-group>
        </b-form-group>
    </b-form>
</template>

<script>
export default {
    props: [
        'newItem'
    ],
    computed: {
        host() {
            return window.location.host;
        }
    },
}
</script>

<style lang="css">
    .input-group-text {
        background: #f4f6f8;
    }
</style>