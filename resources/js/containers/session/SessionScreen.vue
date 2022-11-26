<template>
<div>
	<video
        id="session-video"
        class="h-100 w-100 video-js vjs-default-skin" 
        controls
        v-if="videoReady && type == 'live'"
    ></video>
    <iframe
        v-else-if="videoReady && type == 'vimeo'"
        :src="url"
        width="auto"
        height="auto"
        frameborder="0"
        allow="autoplay; fullscreen"
        allowfullscreen
        class="bg-dark"
        style="height: 70vh; width: 75vw"
    ></iframe>
    <div class="h-100 w-100" controls v-else>
    	Please wait...
    </div>
    <div class="float-right">
        <div class="avatar-part d-flex" v-if="presenterData">
            <h5 class="text-white my-auto" v-if="presenterData.length">Presenters: </h5>
            <div v-for="user in presenterData" :key="user.id" class="position-relative mx-2">
                <img :src="'/assets/img/avatar/' + user.avatar" class="user-avatar cursor-pointer" v-b-tooltip.hover :title="user.bio" />
                <span v-if="user.role == 2" :style="adminMark()" />
            </div>
            <div :style="'background:' + color" class="live-btn" @click="link">Live Q & A</div>
        </div>
        <div class="position-relative d-inline-block" v-if="session && session.uploads">
            <b-dropdown
                variant="empty"
                size="lg"
                right
                toggle-class="header-icon notificationButton"
                menu-class="position-absolute"
                no-caret
            >
                <template slot="button-content">
                    <i class="iconsminds-download-1" />
                    <span class="count" v-if="session.uploads.length">{{session.uploads.length}}</span>
                </template>
                <vue-perfect-scrollbar style="overflow: hidden" :settings="{ suppressScrollX: true, wheelPropagation: false }">
                    <div
                        class="d-flex flex-row p-3 border-bottom"
                        v-for="(asset,index) in session.uploads"
                        :key="index"
                    >
                        <p class="font-weight-medium mb-1 text-capitalize">{{asset.title}}</p>
                        <div class="ml-auto mr-2 my-auto" style="" v-if="asset.item">
                            <i class="simple-icon-eye handle mr-1 asset-icon-btn" v-b-modal.asset_modal @click="modalTitle = asset.title, modalContent = asset.item, extension = asset.item.split('.').pop()" />
                            <a :href="'/data/' + asset.item" target="_blank" v-if="asset.item.substr(0, 4) != 'http'" download>
                                <i class="simple-icon-cloud-download handle asset-icon-btn"/>
                            </a>
                            <a :href="asset.item" download target="_blank" v-else>
                                <i class="simple-icon-cloud-download handle asset-icon-btn" />
                            </a>
                        </div>
                    </div>
                </vue-perfect-scrollbar>
            </b-dropdown>
        </div>
    </div>
    <b-modal id="asset_modal" ref="asset_modal" size="lg" :title="modalTitle" centered hide-footer>
        <template v-if="modalContent">
            <template v-if="modalContent.substr(0, 4) == 'http'">
                <iframe class="w-100" :src="modalContent" style="height: 54vh" />
            </template>
            <template v-else>
                <video v-if="extension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
                    <source :src="'/data/'+modalContent" type="video/mp4">
                </video>

                <iframe class="w-100" :src="'/data/'+modalContent" v-else-if="extension === 'pdf'" style="height: 75vh" />

                <img class="w-100" :src="'/data/'+modalContent" v-else />
            </template>
        </template>
    </b-modal>
    <div class="p-4" v-if="session">
        <div class="d-flex my-3">
            <h2 class="text-muted" v-if="session.title">{{session.title}}</h2>
            <h2 class="text-muted" v-else>Sessin Title</h2>
        </div>
        <div class="d-flex my-3">
            <h4 class="text-muted" v-if="session.description">{{session.description}}</h4>
            <h4 class="text-muted" v-else>Session Description</h4>
        </div>
    </div>
    <!-- <v-meeting style="width: 10vw; height: 10vh"></v-meeting> -->
</div>

</template>

<script>
// import ZoomMeeting from './ZoomMeeting'

export default {
  	props: ['session', 'color', 'presenterData'],
    // components: {
    //     'v-meeting': ZoomMeeting
    // },
    data() {
    	return {
            videoReady: false,
            streamRunning: false,
            sources: [],
            type: 'vimeo',
            url: null,
            modalTitle: '',
            modalContent: '',
            extension: '',
            links: []
    	}
    },
    mounted() {
        this.init()
    },
    updated() {
        this.checkVideo()
    },
    methods: {
        init() {
            this.checkVideo()
        },
        adminMark() {
            return {
                'position': 'absolute',
                'background': this.color,
                'width': '12px',
                'height': '12px',
                'box-shadow': '0 0 5px 0 ' +  this.color,
                'margin-left': '-10px',
                'border': '2px solid white',
                'border-radius': '50%'
            }
        },
        link() {
            if (this.session && this.session.asset) {
                this.links = JSON.parse(this.session.asset)
            }
            if (this.links.length && this.session && this.session.event) {
                var currentTime = Math.round(new Date().getTime() / 1000)
                var startTime = Date.parse(this.session.event.date + ' ' + this.session.start) / 1000
                this.links.forEach((link, index) => {
                    var last = 0
                    for (var i = 0; i < index; i++) {
                        last += Number(this.links[i].time)
                    }
                    if (currentTime >= startTime + last * 60 && currentTime < startTime + (last + Number(link.time)) * 60) {
                        window.location.replace(link.link)
                    }
                })
            }
        },
        checkVideo() {
            if (this.session && this.session.vimeo_link) {
                this.url = 'https://player.vimeo.com/video/' +  this.session.vimeo_link
                this.videoReady = true
            }
        }
    }
}
</script>

<style>
    .avatar-part {
        background: rgba(0, 0, 0, .4);
        padding: 0.6rem;
        border-radius: 8px;
        margin-right: 8px;
    }
    .live-btn {
        color: white;
        padding: 8px;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
    }
    .live-btn:hover {
        box-shadow: 0 0 1px;
    }
    .tooltip {
        top: 0!important
    }
    .dropdown-menu {
        overflow: hidden;
    }
    .asset-icon-btn {
        font-size: 1rem;
        cursor: pointer
    }
    .asset-icon-btn:hover {
        color: rgb(58, 100, 166);
    }
</style>
