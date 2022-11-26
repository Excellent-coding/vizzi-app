<template>
    <b-button
        v-b-modal.add_modal
        variant="primary"
        size="lg"
        class="top-right-button"
        @click="initData"
        :style="getStyle()"
    >{{ $t('add-new') }}</b-button>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
    props: [
        'initData'
    ],
    computed: {
        ...mapGetters({
            user: 'auth/user'
        }),
    },
    methods: {
        getStyle() {
            if (this.user && this.user.role != 1) {
                return {
                    'width': '100%'
                }
            }
        }
    }
}
</script>