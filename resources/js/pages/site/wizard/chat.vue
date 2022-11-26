<template>
    <div class="wrapper" v-if="isShow">
        <support-list 
            :height="height" 
            :logo="logo" 
            :title="title"
        />
        <support-conversation />
        <user-info
            :height="height"
            :logo="logo" 
            :title="title"
        />
    </div>
</template>

<script>
import {
    mapGetters,
    mapActions
} from 'vuex'

import SupportList from '../../../containers/Chat/SupportList'
import SupportConversation from '../../../containers/Chat/SupportConversation'
import UserInfo from '../../../containers/Chat/UserInfo'

export default {
    middleware: 'site-auth',
    components: {
        SupportList,
        SupportConversation,
        UserInfo
    },
    metaInfo () {
        return { title: `Chat` }
    },
    data() {
        return {
            isShow: false,
            height: 100,
            logo: '',
            title: '',
            onMembersShow: false
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            currentUser: "auth/user"
        })
    },
    mounted() {
        this.getContacts({userId: this.currentUser.id, siteId: this.siteId})
        this.init()
    },
    methods: {
        ...mapActions({
            getContacts: 'chat/getContacts'
        }),
        init() {
            axios.post('site/domain', {id: this.siteId}).then(res => {
                this.isShow = true
                this.logo = res.data.logo
                this.title = res.data.title
            })
        }
    }
}
</script>

<style lang="scss">
    .chat-header {
        background: #dd2c00;
        color: white;
        border-radius: 8px;
        font-size: 2.5rem;
        text-align: center;
    }
    .body-bg {
        width: calc(100% - 120px);
        margin-left: 120px;
    }
    .wrapper {
        flex-direction: column;
        z-index: 100;
        width: 100%;
        max-width: none;
        display: flex;
        margin-top: -44px;
        height: calc(100vh - 100px);
    }
    .bg {
        background: white;
        left: 120px;
        position: fixed;
        overflow: auto;
    }
    .conv {
        color: white;
        font-size: 15px;
        align-items: center;
        -webkit-box-align: center;
        font-weight: 600;
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
    }
    .conv.hover {
        cursor: pointer;
    }
    .conv.hover:hover {
        background-color: rgba(0, 0, 0, .2);
    }
    .conv.active {
        background-color: rgba(0, 0, 0, .2);
    }
    .list {
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        height: 56px;
        padding: 10px 24px;
        font-family: "Roboto",sans-serif;
        font-weight: 400;
        font-size: 13px;
        text-transform: capitalize;
        color: black;
        background: transparent;
        border-radius: 8px;
    }
    .list:hover {
        background-color: rgba(0, 0, 0, .2);
    }
    .list.active {
        background-color: rgba(0, 0, 0, .2);
    }
    .contact-avatar {
        width: 36px;
        height: 36px;
        border-radius: 4px;
    }
    .msg-container {
        margin-left: 240px;
        margin-right: 300px;
        margin-top: 40px;
        margin-bottom: 20px;
    }
    .channel-header {
        flex-grow: 1;
        color: #585858;
        font-size: 18px;
        font-family: "Roboto",sans-serif;
        font-weight: 600;
        text-transform: capitalize;
    }
    .channel-description {
        flex-grow: 1;
        color: #9B9B9B;
        font-size: 13px;
        font-family: "Roboto",sans-serif;
        font-weight: 400;
        text-transform: capitalize;
    }
    .channel-header-area {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
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
    .head {
        padding-bottom: 0;
        align-items: center;
        -webkit-box-align: center;
        width: 100%;
        height: 85px;
        display: flex;
    }

    .text {
        display: flex;
        flex-direction: column;
        padding-left: 16px;
        min-height: 36px;
        -webkit-box-pack: justify;
        justify-content: space-between;
        color: black;
        font-size: 15px;
        font-weight: 800;
    }
    .user {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        padding-bottom: 0;
        text-transform: capitalize;
        font-size: 14px;
        font-weight: 500;
    }
    .status {
        height: 7px;
        width: 7px;
        display: flex;
        border-radius: 50%;
        background: chartreuse;
        margin-left: 14px;
    }
    .text-black {
        color:rgba(0, 0, 0, .6)!important
    }
    .chat-input {
        left: 21px;
        right: 21px;
        bottom: 21px;
        margin: 24px;
        margin-top: 0;
        margin-bottom: 14px;
        max-height: 260px;
        height: auto;
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
                max-height: 260px;
                overflow: auto;
                background: #F8F6FD;
                width: 100%;
                height: 100%;
            }
        }
    }
</style>