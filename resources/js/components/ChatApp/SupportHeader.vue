<template>
    <div>
        <div class="mb-4 channel-header-area">
            <b-dropdown
                :text="'Assgned to: ' + name"
                class="float-md-left btn-group"
                variant="outline-dark"
            >
                <b-dropdown-item
                    v-for="user in members"
                    :key="user.id"
                    @click="name = user.name"
                >
                    <img :src="'/assets/img/avatar/' + user.avatar" class="user-avatar" />
                    {{user.name}}
                </b-dropdown-item>
            </b-dropdown>
            <b-dropdown
              right
              :text="optionText"
              variant="outline-success"
              class="float-md-right d-inline-block"
            >
                <b-dropdown-item
                    v-for="option in options"
                    :key="option.value"
                    @click="optionText = option.text"
                >{{ option.text }}</b-dropdown-item>
            </b-dropdown>
        </div>
        <!-- <div class="separator mb-2" /> -->
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