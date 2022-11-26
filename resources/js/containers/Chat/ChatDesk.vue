<template>
    <div class="chat-box">
        <div class="init-form" v-if="!isChat">
            <div class="mt-4">
                <i class="simple-icon-rocket chat-box-icon"></i>
            </div>
            <div class="mt-3">Looking for information or need assistance?</div>
            <div class="">Connect with us</div>
            <div class="mt-5">
                <img src="/assets/img/chat-box.png" class="position-absolute chat-bg" />
                <div class="des">Our team of experts is ready to help</div>
                <div class="d-flex justify-content-end align-items-center mt-4">
                    <div v-for="(chatUser, index) in chatUsers" class="position-relative" :class="getClass(index)" :key="chatUser.id">
                        <img
                            :src="'/assets/img/avatar/' + chatUser.avatar"
                            class="chat-avatar" 
                            :key="chatUser.id" 
                        />
                        <div class="status"></div>
                    </div>
                </div>
                <button class="chat-conversation btn mt-4" @click="isChat = true">New Conversation</button>
            </div>
        </div>
        <div class="chat-form" v-if="isChat">
            <div class="float-right">
                <i class="iconsminds-arrow-down-3 float-right" @click="isChat = !isChat, $emit('chat-close')"></i>
            </div>
            <div class="d-flex justify-content-end align-items-center p-2">
                <div class="position-relative mr-3">
                    <img :src="'/assets/img/avatar/' + chatUsers[0].avatar" class="chat-avatar"  alt="" v-if="chatUsers.length">
                    <div class="status"></div>
                </div>
                <div class="ml-1 mr-auto my-auto">
                    <h6 v-if="chatUsers.length">{{chatUsers[0].name}}</h6>
                    <p class="mb-0" v-if="type">Help Desk</p>
                    <p class="mb-0" v-else>Info Desk</p>
                </div>
            </div>
            <div class="chat-container">
                <div class="chat-message">
                    <div class="d-flex mb-1 message" v-for="msg in contentData" :key="msg.timetoken" :class="getChatClass(msg)">
                        <div v-if="user.id != msg.meta.id">
                            <img :src="'/assets/img/avatar/' + msg.meta.avatar" alt="" class="chat-avatar-sm">
                            <div class="chat-user-name text-center">{{msg.meta.userName}}</div>
                        </div>
                        <div class="ml-2">
                            <div class="chat-content" v-html="msg.message" />
                            <div class="chat-time">
                                {{new Date(Number(msg.timetoken) / 10000) | moment('h:mm')}}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mb-1 message" v-for="msg in currentData" :key="msg.timetoken" :class="getNowChatClass(msg)">
                        <div v-if="user.id != msg.userMetadata.id">
                            <img :src="'/assets/img/avatar/' + msg.userMetadata.avatar" alt="" class="chat-avatar-sm">
                            <div class="chat-user-name text-center">{{msg.userMetadata.userName}}</div>
                        </div>
                        <div class="ml-2">
                            <div class="chat-content" v-html="msg.message" />
                            <div class="chat-time">
                                {{new Date(Number(msg.timetoken) / 10000) | moment('h:mm')}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chat-input d-flex mt-auto">
                    <input type="text" v-model="text" />
                    <i class="simple-icon-paper-plane p-2" @click="submit" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'
import PubNubVue from 'pubnub-vue'

export default {
    props: ['item', 'type'],
    data() {
        return {
            isChat: false,
            apiBase: '/site/',
            chatUsers: [],
            text: '',
            channel: '',
            contentData: [],
            currentData: []
        }
    },
    computed: {
        ...mapGetters({
            user: 'auth/user',
            siteId: 'site/getSiteId'
        }),
        host() {
            return window.location.host.split('.')[0]
        }
    },
    async mounted() {
        const res = await Axios.post(this.apiBase + 'help-user', {id: this.siteId})
        if (this.type == 1) {
            this.chatUsers = res.data.infoData
        } else {
            this.chatUsers = res.data.helpData
        }
        this.setChannel()
    },
    methods: {
        getClass(index) {
            if (this.chatUsers.length == 1) {
                return 'mx-auto'
            } else {
                if (index == 0) {
                    return 'ml-auto mr-1'
                } else if (index > 0 && index < this.chatUsers.length - 1) {
                    return 'mx-1'
                } else {
                    return 'mr-auto ml-1'
                }
            }
        },
        setChannel() {
            if (this.chatUsers[0]) {
                this.channel = this.host + '-' + 'help-' + this.type + '-' + this.user.id
                this.$pnSubscribe({  
                    channels: [this.channel]
                })
                const pubnub = PubNubVue.getInstance()

                pubnub.fetchMessages({
                    channels: [this.channel],
                    reverse: true,
                    count: 100,
                    stringifiedTimeToken: true,  
                    includeMeta: true
                }).then((res) => {
                    this.contentData = res.channels[this.channel]
                }).catch((err) => {
                    this.contentData = []
                })
            }
        },
        timeDiff(past) {
            const msPerMinute = 60 * 1000;
            const msPerHour = msPerMinute * 60;
            const msPerDay = msPerHour * 24;
            const msPerMonth = msPerDay * 30;
            const msPerYear = msPerDay * 365;
            const elapsed = new Date().getTime() - Math.floor(past / 10000);

            if (elapsed < msPerMinute) {
                return 'Just before';   
            }

            else if (elapsed < msPerHour) {
                return Math.round(elapsed/msPerMinute) + ' minutes ago';   
            }

            else if (elapsed < msPerDay ) {
                return Math.round(elapsed/msPerHour ) + ' hours ago';   
            }

            else if (elapsed < msPerMonth) {
                return 'approximately ' + Math.round(elapsed/msPerDay) + ' days ago';   
            }

            else if (elapsed < msPerYear) {
                return 'approximately ' + Math.round(elapsed/msPerMonth) + ' months ago';   
            }

            else {
                return 'approximately ' + Math.round(elapsed/msPerYear ) + ' years ago';   
            }
        },
        submit() {
            this.$pnPublish({
                channel: this.channel,
                message: this.text,
                meta: {
                    'userName': this.user.name,
                    'avatar': this.user.avatar,
                    'id': this.user.id
                }
            });
            this.text = ''
            this.currentData = this.$pnGetMessage(this.channel)
            var type = 'help'
            if (this.type) {
                type = 'info'
            } 
            Axios.post(this.apiBase + 'chat-set', {id: this.siteId, type: type})
        },
        getChatClass(msg) {
            if (msg.meta.id != this.user.id) {
                return 'mr-auto'
            } else {
                return 'sended ml-auto mr-1'
            }
        },
        getNowChatClass(msg) {
            if (msg.userMetadata.id != this.user.id) {
                return 'mr-auto'
            } else {
                return 'sended ml-auto mr-1'
            }
        }
    }
}
</script>