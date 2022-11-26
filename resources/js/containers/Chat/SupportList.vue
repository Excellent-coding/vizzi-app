<template>
    <div class="bg" :style="getStyle()">
        <list-header :logo="logo" :title="title" :user="currentUser" />
        <div class="list-part" v-if="users">
            <div v-for="contact in users" :key="contact.id">
                <template v-if="!(currentUser.role == 5 && contact.role == 2)">
                    <div class="list" :class="isActive(contact.id)" @click="setSelected(contact.id)">
                        <div class="d-flex">
                            <img :src="'/assets/img/avatar/' + contact.avatar" class="contact-avatar mr-3" />
                            <div class="my-auto">{{contact.name}}</div>
                        </div>
                        <i class="simple-icon-login my-auto" />
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
  
<script>
import {
    mapGetters,
    mapActions
} from 'vuex'

import ChatSidebarHeader from '../../components/ChatApp/ChatSidebarHeader'

export default {
    components: {
        'list-header': ChatSidebarHeader
    },
    props: ['height', 'logo', 'title'],
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            currentUser: "auth/user",
            users: 'chat/users',
            selectedUser: 'chat/selectedUser'
        })
    },
    methods: {
        ...mapActions({
            setSelectedUser: 'chat/setSelectedUser'
        }),
        setSelected(id) {
            this.setSelectedUser({userId: id})
        },
        getStyle() {
            return {
                'width': '260px',
                'height': 'calc(100vh - ' + this.height + 'px)'
            }
        },
        isActive(id) {
            if (id == this.selectedUser) {
                return 'active'
            } else {
                return ''
            }
        }
    }
}
</script>