<template>
    <div>
        <div class="mb-4 channel-header-area">
            <div>
                <div class="channel-header">channel header</div>
                <div class="channel-description">This is Channel header</div>
            </div>
        </div>
        <div class="separator mb-2" />
    </div>
</template>

<script>
import {
    mapGetters
} from 'vuex'

export default {
    data() {
        return {
            onMembersShow: false
        }
    },
    computed: {
        ...mapGetters({
            currentUser: "auth/user",
            managers: 'chat/managers',
            selectedUser: 'chat/selectedUser'
        }),
    },
}
</script>

<style lang="css">
    .header-icon {
        font-size: 20px;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .cursor-pointer em {
        font-weight: 900;
    }
</style>