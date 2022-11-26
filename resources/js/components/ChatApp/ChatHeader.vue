<template>
    <div>
        <div class="mb-4 channel-header-area">
            <div>
                <div class="channel-header">{{header}}</div>
                <div class="channel-description">This is Channel header</div>
            </div>
            <div class="d-flex cursor-pointer" @click="isMembersShow" :style="getColor()"  v-if="currentUser && currentUser.role < 4 && selectedUser == 0">
                <div class="my-auto mr-2">{{managers.length}}</div>
                <i class="simple-icon-people header-icon my-auto"/>
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
    props: ['header'],
    data() {
        return {
            options: [
                {value: 1, text: 'Mark as Read'},
                {value: 0, text: 'Follow Back'}
            ],
            optionText: 'Mark as Read',
            members: [],
            name: ''
        }
    },
    computed: {
        ...mapGetters({
            currentUser: "auth/user",
            managers: 'chat/managers',
            selectedUser: 'chat/selectedUser'
        }),
    },
    methods: {
        // isMembersShow() {
        //     this.onMembersShow = !this.onMembersShow
        //     this.$emit('onMembersShow', this.onMembersShow)
        // },
        // getColor() {
        //     let color = ''
        //     if (this.onMembersShow) {
        //         color = 'rgb(142, 45, 226)'
        //     } else {
        //         color = '#2a2a2a'
        //     }

        //     return {
        //         'color': color
        //     }
        // }

    }
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