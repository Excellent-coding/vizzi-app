<template>
    <div class="bg" :style="getStyle()">
        <list-header :logo="logo" :title="title" :user="currentUser" />
        <template v-if="boothData.length">
            <div class="conv">Booth Group</div>
            <div class="list-part">
                <div v-for="contact in boothData" :key="contact.id">
                    <div class="list" :class="isActive(contact)" @click="setSelected(contact, contact.booth.user.id)">
                        <div class="d-flex" v-if="contact.booth.user">
                            <img :src="'/assets/img/avatar/' + contact.booth.user.avatar" class="contact-avatar mr-3" />
                            <div class="my-auto">{{contact.booth.user.name}}</div>
                        </div>
                        <div v-else class="text-center">
                            User doesn't exist!
                        </div>
                        <i class="simple-icon-login my-auto" />
                    </div>
                </div>
            </div>
        </template>
        <template v-if="sponsorData.length">
            <div class="conv">Sponsor Group</div>
            <div class="list-part">
                <div v-for="contact in sponsorData" :key="contact.id">
                    <div class="list" :class="isActive(contact)" @click="setSelected(contact, contact.booth.user.id)">
                        <div class="d-flex" v-if="contact.booth.user">
                            <img :src="'/assets/img/avatar/' + contact.booth.user.avatar" class="contact-avatar mr-3" />
                            <div class="my-auto">{{contact.booth.user.name}}</div>
                        </div>
                        <div v-else class="text-center">
                            User doesn't exist!
                        </div>
                        <i class="simple-icon-login my-auto" />
                    </div>
                </div>
            </div>
        </template>
        <template v-if="posterData.length">
            <div class="conv">Poster Group</div>
            <div class="list-part">
                <div v-for="contact in posterData" :key="contact.id">
                    <div class="list" :class="isActive(contact)" @click="setSelected(contact, contact.poster.user.id)">
                        <div class="d-flex" v-if="contact.poster.user">
                            <img :src="'/assets/img/avatar/' + contact.poster.user.avatar" class="contact-avatar mr-3" />
                            <div class="my-auto">{{contact.poster.user.name}}</div>
                        </div>
                        <div v-else class="text-center">
                            User doesn't exist!
                        </div>
                        <i class="simple-icon-login my-auto" />
                    </div>
                </div>
            </div>
        </template>
        <template v-if="helpData.length && currentUser.role == 1">
            <div class="conv">Help Desk</div>
            <div class="list-part">
                <div v-for="contact in helpData" :key="contact.id">
                    <div class="list" :class="isActive(contact)" @click="setSelected(contact, contact.user.id)">
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
        <template v-if="infoData.length && (currentUser.role == 6 || currentUser.role == 2 || currentUser.role == 1)">
            <div class="conv">Help Desk</div>
            <div class="list-part">
                <div v-for="contact in infoData" :key="contact.id">
                    <div class="list" :class="isActive(contact)" @click="setSelected(contact, contact.user.id)">
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
    props: ['height', 'logo', 'title', 'chat-topic'],
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
            boothData: [],
            sponsorData: [],
            posterData: [],
            helpData: [],
            infoData: []
        }
    },
    mounted() {
            Axios.post('/site/chat-get', {id: this.siteId, type: this.channelType}).then(res => {
                console.log(res.data)
                this.boothData = res.data.booth
                this.sponsorData = res.data.sponsor
                this.posterData = res.data.poster
                this.helpData = res.data.helpData
                this.infoData = res.data.infoData
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
            var height = this.height + 64
            return {
                'width': '260px',
                'height': 'calc(100vh - ' + height + 'px)'
            }
        },
        isActive(item) {
            if (this.channel == item.id && this.channelType == item.type) {
                if (item.type == 2) {
                    this.$emit('chat-topic', item.poster.title)
                } else {
                    this.$emit('chat-topic', item.booth.title)
                }
                return 'active'
            } else {
                return ''
            }
        }
    }
}
</script>