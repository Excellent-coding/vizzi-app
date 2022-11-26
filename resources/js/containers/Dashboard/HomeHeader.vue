<template>
    <b-card no-body>
        <div class="d-block d-md-flex justify-content-end align-items-center p-3">
            <h6 class="mr-auto my-auto">From {{start | moment('MMMM Do YYYY')}} To {{end | moment('MMMM Do YYYY')}} ({{timezone}})</h6>
            <b-button variant="light default" class="mr-2 d-none d-lg-block">
                View Registration Page
            </b-button>
            <b-input-group class="home-header mt-2 mt-md-0 mx-md-0 mx-auto">
                <b-form-input ref="copyPart" class="form-control copy" v-model="host" disabled="" />
                <b-input-group-append>
                    <b-button variant="light default" @click="copy">Copy</b-button>
                </b-input-group-append>
            </b-input-group>
        </div>
    </b-card>
</template>

<script>
export default {
    props: [
        'timezone', 'start', 'end'
    ],
    computed: {
        host() {
            return 'https://' + window.location.host.split('.')[0] + '.vizzi.live'
        }
    },
    methods: {
        copy() {
            navigator.clipboard.writeText(this.host);
        }
    }
}
</script>

<style lang="css">
    .copy {
        font-size: 16px;
        text-align: center;
    }
    .home-header.input-group {
        max-width: 320px;
        width: 100%
    }
</style>