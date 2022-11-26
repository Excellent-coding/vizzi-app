<template>
    <div class="vw-25 rounded" style="height: calc(100vh - 150px)">
        <chat-switch-button @click="chatSwitch" on-label="Session" off-label="Event" class="chat-switch"></chat-switch-button>
        <b-tabs no-fade class="pl-0 pr-0 h-100" content-class="chat-app-tab-content" nav-class="card-header-tabs ml-0 mr-0">
            <b-tab title="Chat" active title-item-class="w-50 text-center" no-body class="chat-app-tab-pane">
                <div class="my-2 h-100 py-1">
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
            </b-tab>
            <b-tab title="People" title-item-class="w-50 text-center" no-body class="chat-app-tab-pane">
                <div class="my-2 h-100 py-1">
                    <div class="chat-body" :style="getBodySize()">
                        <chat-user-list-item
                            :userList="userList"
                        />
                    </div>
                </div>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
import {
    mapGetters
} from 'vuex'

import axios from 'axios'
import PubNubVue from 'pubnub-vue'

import ChatListItem from '../../components/ChatApp/ChatListItem'
import ChatUserListItem from '../../components/ChatApp/ChatUserListItem'
import ChatInput from '../../components/ChatApp/ChatInput'
import ChatSwitchButton from '../../components/ChatApp/ChatSwitchButton'
export default {
    components: {
        'chat-list-item': ChatListItem,
        'chat-user-list-item': ChatUserListItem,
        'chat-input': ChatInput,
        'chat-switch-button': ChatSwitchButton
    },
    data() {
        return {
            sessionType: 1,
            inputHeight: 160,
            contentData: [],
            currentData: [],
            userList: [],
            channel: '',
            chatStatus: true
        }
    },
    computed: {
        ...mapGetters({
            currentUser: "auth/user",
        }),
        host() {
            return window.location.host.split('.')[0]
        }
    },
    updated() {
        const el = this.$el.getElementsByClassName('chat-body')[0]
        el.scrollTop = el.scrollHeight;
    },
    mounted() {
        this.id = this.$route.params.id
        this.channel = this.host + '-session-' + this.id
        this.getChat()
    },
    methods: {
        getBodySize() {
            return {
                'height': 'calc(60vh - '+ this.inputHeight +'px)',
                'background': '#f4f4f4'
            }
        },
        getChat() {
            this.$pnSubscribe({
				channels: [this.channel]
			});

            const pubnub = PubNubVue.getInstance()
            pubnub.fetchMessages({
                channels: [this.channel],
                reverse: true,
                count: 100,
                stringifiedTimeToken: true,  
                includeMeta: true
            }).then((res) => {
                this.userList = []
                var keys = []
                var userList = []
                this.contentData = []
                this.contentData = res.channels[this.channel]
                var filteredData = this.contentData.forEach(function(item){           
                    var key = item["meta"]["id"]
                    if (keys.indexOf(key) === -1) {
                        keys.push(key);
                        userList.push(item["meta"])
                    }
                });
                this.userList = userList
            }).catch((err) => {
                this.contentData = []
            })
            this.currentData = this.$pnGetMessage(this.channel)
        },
        resize(val) {
            this.inputHeight = val + 116
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
            this.inputHeight = 160
        },
        chatSwitch(val) {
            this.chatStatus = val
            if(this.chatStatus){
                this.channel = this.host + '-session-' + this.id
                this.getChat()
            } else {
                axios.post('/site/session-data', {id: this.id}).then(res => {
                    this.channel = this.host + '-event-' + res.data.event_id
                    this.getChat()
                })
            }
        }
    }
}
</script>

<style lang="scss">
    .chat-body {
        display: flex;
        flex-grow: 1;
        flex-direction: column;
        overflow-y: scroll;
    }
    .chat-list-item {
        flex-direction: row;
        flex-shrink: 0;
        width: fit-content;
    }
    .chat-user-name {
        font-size: 15px;
        font-family: "Roboto",sans-serif;
        font-weight: 600;
        text-transform: capitalize;
        color: #585858;
    }
    .chat-time {
        font-size: 11px;
        font-family: "Roboto",sans-serif;
        font-weight: 300;
        margin-left: 14px;
        color: #585858;
    }
    .chat-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
    }
    .chat-content {
        word-break: break-word;
        white-space: pre-wrap;
        border-radius: 10px;
        border-top-left-radius: 0;
        padding: 16px;
        padding-bottom: 0;
        width: -webkit-fit-content;
        width: -moz-fit-content;
        width: fit-content;
        line-height: 1.5;
        font-size: 13px;
        font-family: "Roboto",sans-serif;
        font-weight: 400;
        text-align: left;
        background: #FFFFFF;
        box-shadow: 0 6px 10px rgba(103,19,176,0.06);
        color: #585858;
    }
    .chat-input {
        max-height: 260px;
        height: auto;
        margin: 10px 20px;
        .input-box {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-align-items: flex-end;
            -webkit-box-align: flex-end;
            -ms-flex-align: flex-end;
            align-items: flex-end;
            position: relative;
            bottom: 0;
            border-radius: 5px;
            border: 1px solid #9B9B9B;
            padding: 12px;
            height: 100%;
            .text-area {
                border: none;
                font-size: 13px;
                font-weight: 500;
                color: #585858;
                font-family: "Roboto",sans-serif;
                max-height: 120px;
                overflow: auto;
                background: white;
                width: 100%;
                height: 100%;
            }
        }
    }
    .sended {
        .chat-content {
            background: #008ecc;
            color: white;
        }
    }
    .chat-switch{
        margin: 10px;
        text-align: center;
    }
</style>