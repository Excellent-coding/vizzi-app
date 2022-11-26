<template>
    <div class="msg-container" :style="getSpace()">
        <chat-header :header="header" />
        <div class="chat-body" :style="getBodySize()">
            <chat-list-item
                :contentData="contentData"
                :currentData="currentData"
            />
        </div>
        <chat-input
            @changed="resize"
            @submitted="submit"
        />
    </div>
</template>

<script>
import {
    mapGetters,
    mapActions
} from 'vuex'

import PubNubVue from 'pubnub-vue';

import ChatListItem from '../../components/ChatApp/ChatListItem'
import ChatInput from '../../components/ChatApp/ChatInput'
import ChatHeader from '../../components/ChatApp/ChatHeader'

export default {
    props: ['header'],
    components: {
        'chat-list-item': ChatListItem,
        'chat-input': ChatInput,
        'chat-header': ChatHeader
    },
    data() {
        return {
            onMembersShow: false,
            inputHeight: 270,
            containerRightSpace: 30,
            channel: '',
            contentData: [],
            currentData: []
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            currentUser: "auth/user",
            managers: 'chat/managers',
            selectedUser: 'chat/selectedUser',
            headerStyle: 'site/getStyle',
            channelType: 'chat/channelType'
        }),
        host() {
            return window.location.host.split('.')[0]
        }
    },
    watch: {
        selectedUser(second) {
            if (second == 0) {
                this.channel = this.host + '-' + 'group' + '-' + '-' + second
            } else {
                let first = this.currentUser.id
                if (first > second) {
                    const third = second
                    second = first
                    first = third
                }
                this.channel = this.host + '-' + this.channelType + '-' + first + '-' + second
            }

            this.getChat()
        }
    },
    mounted() {
        // this.initChat()
        // this.getChat()
    },
    updated() {
        const el = this.$el.getElementsByClassName('chat-body')[0]
        el.scrollTop = el.scrollHeight;
    },
    methods: {
        ...mapActions({
            setSelectedUser: 'chat/setSelectedUser',
        }),
        initChat() {
            let first = this.currentUser.id
            if (this.selectedUser) {
                let second = this.selectedUser
                if (first > second) {
                    const third = second
                    second = first
                    first = third
                }
                this.channel = this.host + '-' + 'personal' + '-' + first + '-' + second
            } else {
                let second = this.managers[0].id
                if (this.managers[0].role < 3) {
                    if (this.currentUser.id != this.managers[1].id) {
                        second = this.managers[1].id
                    } else {
                        second = this.managers[2].id
                    }
                }
                this.setSelectedUser({userId: second})
                if (first > second) {
                    const third = second
                    second = first
                    first = third
                }
                this.channel = this.host + '-' + 'personal' + '-' + first + '-' + second
            }
        },
        submit(val) {
            this.$pnPublish({
                channel: this.channel,
                message: val,
                meta: {
                    'userName': this.currentUser.name,
                    'avatar': this.currentUser.avatar,
                    'id': this.currentUser.id
                }
            });
        },
        getChat() {
            this.$pnSubscribe({
				channels: [this.channel]
			});

            const pubnub = PubNubVue.getInstance()
            pubnub.fetchMessages({
                channels: [this.channel],
                reverse: true,
                count: 10,
                stringifiedTimeToken: true,  
                includeMeta: true
            }).then((res) => {
                this.contentData = res.channels[this.channel]
            }).catch((err) => {
                this.contentData = []
            })

            this.currentData = this.$pnGetMessage(this.channel)
        },

        resize(val) {
            this.inputHeight = val + 225
        },
        getBodySize() {
            let height = this.inputHeight
            if (this.headerStyle) {
                height = height + JSON.parse(this.headerStyle).style.height
            }
            return {
                'height': 'calc(100vh - '+ height +'px)'
            }
        },
        isMembersShow(val) {
            this.onMembersShow = val
            if (val) {
                this.containerRightSpace = 300
            } else {
                this.containerRightSpace = 40
            }
            this.$emit('onMembersShow', val)
        },
        getSpace() {
            return {
                'margin-right': this.containerRightSpace + 'px'
            }
        }
    }
}
</script>
