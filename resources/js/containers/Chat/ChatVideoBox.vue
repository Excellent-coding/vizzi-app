<template>
    <div class="chat-video-box">
        <i class="simple-icon-camrecorder chat-icon" v-if="!isOpened" @click="isOpened = !isOpened"></i>
        <div class="init-form" v-if="isOpened && !isChat">
            <i class="iconsminds-arrow-down-3 float-right" @click="isOpened = !isOpened"></i>
            <iframe
                :src="iframeUrl"
                width=800
                height=640
                scrolling="auto"
                allow="microphone; camera">
            </iframe>
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
            isOpened: false,
            isChat: false,
            iframeUrl: ""
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
        this.setIframeUrl(this.item.id)
        console.log(this.iframeUrl)
    },
    methods: {
        setIframeUrl(id) {
            this.iframeUrl = "https://tokbox.com/embed/embed/ot-embed.js?embedId=69d6c179-fb39-471b-b7ea-f929a5b2d1e5&room=POSTER_" + id + "&iframe=true"
        }
    }
}
</script>

<style lang="scss">

.chat-video-box ::-webkit-scrollbar {
    width: 6px;
}
.chat-video-box ::-webkit-scrollbar-track {
    border-radius: 3px;
    background: transparent; 
}
.chat-video-box ::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, .6);
    border-radius: 3px;
}
.chat-video-box ::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, .8); 
}
.chat-video-box {
    position: fixed;
    right: 7%;
    bottom: 16%;
    left: auto;
    z-index: 98;
}
.chat-video-box i {
    cursor: pointer;
}
.chat-video-box .chat-form {
    background: rgba(0,0,0,0.9);
    color: white;
    border-radius: 8px;
    padding: 0px;
    width: 800px;
    height: 640px;
    font-size: 14px;
}
.chat-video-box .chat-container {
    width: 100%;
    height: calc(50vh - 106px);
    background: rgba(255, 255, 255, .1);
    padding: 8px;
    border-radius: 8px;
}
.chat-video-box .chat-icon {
    padding: 20px;
    font-size: 20px;
    background: #117c4a;
    color: white;
    border-radius: 50%;
    cursor: pointer;
}
.chat-video-box .chat-icon:hover {
    box-shadow: 0 0 10px 0 grey
}
.chat-video-box .init-form {
    background: rgba(0,0,0,0.9);
    color: white;
    border-radius: 8px;
    padding: 0px;
    text-align: center;
    width: 800px;
    height: 640px;
    font-size: 14px;
}
.chat-video-box .chat-bg {
    left: 16px;
    bottom: 16px;
    right: 16px;
    width: 238px;
}

</style>