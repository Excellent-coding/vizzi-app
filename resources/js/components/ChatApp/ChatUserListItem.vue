<template>
    <div class="mb-auto">
        <div class="chat-user-list-item" v-for="user in userList" :key="user.id">
            <div class="d-flex mt-3">
                <img :src="'/assets/img/avatar/' + user.avatar" alt="" class="chat-avatar mr-3 ml-2">
                <div>
                    <div class="d-flex mt-2">
                        <div class="chat-user-name">{{user.userName}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapGetters
} from 'vuex'

export default {
    props: ['userList', 'userList'],
    computed: {
        ...mapGetters({
            currentUser: "auth/user"
        })
    },
    methods: {
        getClass(msg) {
            return 'mr-auto ml-2'
        }
    }
}
</script>
