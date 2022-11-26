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
import SupportHeader from '../../components/ChatApp/SupportHeader'

export default {
    props: ['header'],
    components: {
        'chat-list-item': ChatListItem,
        'chat-input': ChatInput,
        'chat-header': SupportHeader
    },
    data() {
        return {
            onMembersShow: false,
            inputHeight: 270,
            containerRightSpace: 400,
            channel: '',
            contentData: [],
            currentData: []
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            currentUser: "auth/user",
            selectedUser: 'chat/selectedUser',
            headerStyle: 'site/getStyle',
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
                this.channel = this.host + '-' + this.channelType + '-' + first + '-' + second
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
    updated() {
        const el = this.$el.getElementsByClassName('chat-body')[0]
        el.scrollTop = el.scrollHeight;
    },
    methods: {
        ...mapActions({
            setSelectedUser: 'chat/setSelectedUser'
        }),
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
                height = height + JSON.parse(this.headerStyle).style.height - 51
            }
            return {
                'height': 'calc(100vh - '+ height +'px)'
            }
        },
        getSpace() {
            return {
                'margin-right': this.containerRightSpace + 'px'
            }
        }
    }
}
</script>
