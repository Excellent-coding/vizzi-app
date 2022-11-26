<template>
    <div class="bg-white position-fixed" :style="getStyle()">
        <list-header :logo="logo" :title="title" :user="currentUser" />
        <template v-if="data.length">
            <div class="list-part">
                <div v-for="contact in data" :key="contact.id">
                    <div class="list text-dark" :class="isActive(contact)" @click="setSelected(contact, contact.user.id)">
                        <div class="d-flex" v-if="contact.user">
                            <img :src="'/assets/img/avatar/' + contact.user.avatar" class="contact-avatar mr-3" />
                            <div class="my-auto">{{contact.user.name}}</div>
                        </div>
                        <div v-else class="text-center">
                            User doesn't exist!
                        </div>
                        <i class="simple-icon-login my-auto" />
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
  
<script>
import {
    mapGetters,
    mapActions
} from 'vuex'

import ChatSidebarHeader from '../../components/ChatApp/ChatSidebarHeader'
import Axios from 'axios'

export default {
    components: {
        'list-header': ChatSidebarHeader
    },
    props: ['height', 'logo', 'title', 'chat-topic', 'chat-user'],
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            currentUser: "auth/user",
            managers: 'chat/managers',
            users: 'chat/users',
            selectedUser: 'chat/selectedUser',
            adminUser: 'chat/admin',
            channel: 'chat/channel',
            channelType: 'chat/channelType'
        }),
        host() {
            return window.location.host.split('.')[0]
        }
    },
    data() {
        return {
            data: [],
        }
    },
    mounted() {
        Axios.post('/site/chat-admin-get', {id: this.siteId}).then(res => {
            this.data = res.data
        })
    },
    methods: {
        ...mapActions({
            setSelectedUser: 'chat/setSelectedUser',
            setChannel: 'chat/setChannel'
        }),
        setSelected(item, id) {
            this.setChannel({channel: item.id, channelType: item.type})
            this.setSelectedUser({userId: id})
        },
        selectTeam() {
            this.setSelectedUser({userId: 0})
        },
        getStyle() {
            return {
                'width': '260px',
                'height': 'calc(100vh - ' + this.height + 'px)',
                'left': '120px'
            }
        },
        isActive(item) {
            if (this.channel == item.id && this.channelType == item.type) {
                if (item.type == 2) {
                    this.$emit('chat-topic', item.poster.title)
                } else {
                    this.$emit('chat-topic', item.booth.title)
                }
                this.$emit('chat-user', item.user)
                return 'active'
            } else {
                return ''
            }
        }
    }
}
</script>