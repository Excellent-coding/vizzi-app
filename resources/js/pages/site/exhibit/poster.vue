<template>
    <div v-if="isShow">
        <div class="py-5 position-relative" :style="getBackdrop(posterItem)">
            <router-link to="/exhibit/poster" tag="button" class="backHall position-absolute">
                Return to Poster Hall
            </router-link>
            <div class="container position-relative" style=" height: calc(24vw + 9rem)">
                <div v-if="posterItem.type == 1">
                    <div v-if="media.length" class="text-center">
                        <div class="view-header mx-auto mb-4" style="text-transform: capitalize">{{posterItem.title.substring(0, 120)}}</div>
                        <div class="single-view mx-auto position-relative">
                            <img :src="'/data/' + media[0]" class="w-100 h-100 object-fit" v-if="typeImg(media[0])" />
                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="typeVideo(media[0])">
                                <source class="w-100 h-100 object-fit" :src="/data/ + media[0]" type="video/mp4">
                            </video>
                            <iframe class="poster-item" :src="'/data/'+media[0]" />
                        </div>
                    </div>
                </div>
                <div v-else-if="posterItem.type == 2">
                    <div v-if="media.length" class="text-center">
                        <div class="view-header mx-auto mb-4" style="text-transform: capitalize">{{posterItem.title.substring(0, 120)}}</div>
                        <div v-if="media.length" class="multiple-view d-flex justify-content-end align-items-center">
                            <div v-for="(item, index) in media" :key="index" class="item mx-auto bg-white position-relative">
                                <img :src="'/data/' + item" class="w-100 h-100 object-fit" v-if="typeImg(item)" @click="viewPosterItem(item)" />
                                <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="typeVideo(item)">
                                    <source class="w-100 h-100 object-fit" :src="/data/ + item" type="video/mp4" @click="viewPosterItem(item)" >
                                </video>
                                <div class="position-relative w-100 h-100" v-else-if="typePdf(item)" >
                                    <div class="position-absolute w-100 view-pdf" @click="viewPosterItem(item)"/>
                                    <iframe class="w-100 h-100" :src="'/data/' + item" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else-if="posterItem.type == 3">
                    <div class="position-absolute" :style="getPosterHeaderStyle(posterHeader)" style="text-transform: capitalize">{{posterItem.title.substring(0, 120)}}</div>
                    <div class="position-absolute position-relative" v-for="(content, index) in layers" :key="index" :style="contentStyle(content)">
                        <div :style="contentHeaderStyle(content.header)">{{content.title.substring(0, 120)}}</div>
                        <div :style="contentBodyStyle(content.body)" v-if="content.type == 'text'">{{content.text}}</div>
                        <div :style="contentMediaStyle(content.media)" v-else>
                            <img :src="'/data/' + content.media.file" class="w-100 h-100 object-fit" v-if="fileType(content.media.file) !== 'mp4' && fileType(content.media.file) !== 'pdf'" @click="viewPosterItem(content.media.file)" />
                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(content.media.file) == 'mp4'">
                                <source class="w-100 h-100 object-fit" :src="/data/ + content.media.file" type="video/mp4" @click="viewPosterItem(content.media.file)">
                            </video>
                            <div class="position-relative w-100 h-100" v-else >
                                <div class="position-absolute w-100 view-pdf" @click="viewPosterItem(content.media.file)"/>
                                <iframe class="w-100 h-100" :src="'/data/' + content.media.file" />
                            </div>   
                        </div>
                    </div>
                </div>
            </div>

            <button class="slick-arrow slick-prev" @click="prevPoster">Back</button>
            <button class="slick-arrow slick-next" @click="nextPoster">Next</button>
        </div>
        <div class="container">
            <b-row class="mx-0">
                <b-col class="exhibit-header text-center" :style="getHeaderStyle()">
                    <div class="d-inline-flex">
                        <div
                            class="header-btn"
                            v-for="(item, index) in posterItem.header"
                            :key="index"
                            :style="getExhibitButtonStyle()"
                            @click="exhibitHtml = item"
                        >
                        {{item.title}}
                        </div>
                    </div>
                </b-col>
            </b-row>
            <div style="min-height: 200px" class="mt-2">
                <b-tabs pills card>
                    <b-tab v-for="tab in exhibitHtml.tab" :key="tab.id" :title="capitalizeFirstLetter(tab.title)">
                        <h6 class="py-2" v-html="tab.content"></h6>
                        <div v-for="media in tab.media" :key="media.id">
                            <div class="d-flex justify-content-end align-items-center mb-2">
                                <div class="position-relative mr-3">
                                    <div class="position-absolute front-type-text" :style="getStyle(media.item)">{{getTypeText(media.item)}}</div>
                                    <img :src="getType(media.item)" style="width: 48px" />
                                </div>
                                <h6 class="mr-auto my-auto text-capitalize">{{media.title}}</h6>
                                <b-button variant="outline-primary" size="sm" @click="view(media)">View</b-button>
                                <b-button variant="primary" size="sm" @click="addCartItem(media)" class="ml-2">
                                    {{getMark(media) ? '-' : '+'}} {{$ct('cart')}}
                                </b-button>
                            </div>
                            <hr>
                        </div>
                    </b-tab>
                </b-tabs>
            </div>
        </div>

        <chat-box
            :item="posterItem" :type="2"
        />
        <b-modal
            id="poster_view_modal"
            ref="poster_view_modal"
            centered
            hide-footer
            size="lg"
        >
            <template v-slot:modal-header="{ close }">
                <div class="text-center w-100">
                    <h2 class="view-title">{{posterItem.title}}</h2>
                </div>
                <i class="simple-icon-close close-btn" @click="close"></i>
            </template>
            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-if="typeVideo(view)">
                <source class="w-100" :src="/data/ + view" type="video/mp4">
            </video>
            <iframe class="w-100" style="height: 60vh" :src="'/data/'+view" v-else-if="typePdf(view)" />
            <iframe class="w-100" style="height: 60vh" :src="view" v-else-if="view.substring(0, 4) == 'http'" />
            <img :src="'/data/' + view" class="w-100" v-else />
        </b-modal>
    </div>
</template>

<script>
import Axios from 'axios'
import VueSlickCarousel from 'vue-slick-carousel'
import 'vue-slick-carousel/dist/vue-slick-carousel.css'

import ChatBox from '../../../containers/Chat/ChatBox'

import mixins from '../../../mixins/mixins.js'

export default {
    components: {
        VueSlickCarousel,
        'chat-box': ChatBox
    },
    metaInfo () {
        return { title: `Poster` }
    },
    mixins: [mixins],
    data() {
        return {
            id: null,
            isShow: false,
            apiBase: '/site/',
            items: [],
            posterItem: {},
            currentIndex: 0,
            headers: [],
            header: [],
            media: [],
            headerStyle: {
                bg: '#922c88',
                height: 32,
                btn: {
                    bg: '#d683ce',
                    color: '#ffffff',
                    radius: 4,
                    space: 8,
                    boxShadow: true,
                    hasBg: true,
                    fontSize: 13,
                    paddingX: 2,
                    paddingY: 8
                }
            },
            exhibitHtml: '',
            layers: [],
            posterHeader: {
                position: {
                    x: 1, y: 1, ex: 99, ey: 10
                },
                bg: '#ffffff',
                text: {
                    color: '#000000',
                    size: 36
                }
            },
            viewTitle: '',
            view: ''
        }
    },
    mounted() {
        this.id = this.$route.params.id
        Axios.post(this.apiBase + 'poster', {id: this.siteId}).then(res => {
            this.items = res.data
            this.init()
        })
    },
    methods: {
        init() {
            this.items.forEach((item, index) => {
                if (item.id == this.id) {
                    this.currentIndex = index
                    this.getItem()
                }
            })
            this.isShow = true
        },
        getItem() {
            this.media = []
            this.posterItem = {}
            this.exhibitHtml = ''
            this.header = []

            this.posterItem = this.items[this.currentIndex]
            if (this.posterItem.media) {
                this.media = this.posterItem.media.split(', ')
            }
            if (this.posterItem.type == 3) {
                if (this.posterItem.layers) {
                    this.layers = JSON.parse(this.posterItem.layers)
                }
                if (this.posterItem.posterHeader) {
                    this.posterHeader = JSON.parse(this.posterItem.posterHeader)
                }
            }
            if (this.posterItem.header_style) {
                this.headerStyle = JSON.parse(this.posterItem.header_style)
            } else {
                this.headerStyle = {
                    bg: '#922c88',
                    height: 32,
                    btn: {
                        bg: '#d683ce',
                        color: '#ffffff',
                        radius: 4,
                        space: 8,
                        boxShadow: true,
                        hasBg: true,
                        fontSize: 13,
                        paddingX: 2,
                        paddingY: 8
                    }
                }
            }
            if (this.posterItem.header.length) {
                this.exhibitHtml = this.posterItem.header[0]
            }
        },
        nextPoster() {
            if (this.items.length > 1) {
                this.currentIndex = this.currentIndex + 1
                if (this.currentIndex == this.items.length) {
                    this.currentIndex = 0
                }
                this.$router.push('/exhibit/poster/'+this.items[this.currentIndex].id)
                this.getItem()
            }
        },
        prevPoster() {
            if (this.items.length > 1) {
                this.currentIndex = this.currentIndex - 1
                if (this.currentIndex == -1) {
                    this.currentIndex = this.items.length - 1
                }
                this.$router.push('/exhibit/poster/'+this.items[this.currentIndex].id)
                this.getItem()
            }
        },
        typeImg (media) {
            const extension = media.split('.').pop()
            if (extension != 'pdf' && extension != 'mp4') {
                return true
            } else {
                return false
            }
        },
        typeVideo (media) {
            const extension = media.split('.').pop()
            if (extension == 'mp4') {
                return true
            } else {
                return false
            }
        },
        typePdf (media) {
            const extension = media.split('.').pop()
            if (extension == 'pdf') {
                return true
            } else {
                return false
            }
        },
        getHeaderStyle(data) {
            return {
                'background-color': this.headerStyle.bg,
                'height': this.headerStyle.height + 'px'
            }
        },
        getExhibitButtonStyle(data) {
            let boxShadow = '0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12)'
            let bg = this.headerStyle.btn.bg
            if (!this.headerStyle.btn.boxShadow) {
                boxShadow = 'none'
            }
            if (!this.headerStyle.btn.hasBg) {
                bg = 'transparent'
            }
            const buttonHeight = 2 * Number(this.headerStyle.btn.paddingX) +Number( this.headerStyle.btn.fontSize)
            return {
                'background-color': bg,
                'color': this.headerStyle.btn.color,
                'margin-left': this.headerStyle.btn.space + 'px',
                'margin-right': this.headerStyle.btn.space + 'px',
                'border-radius': this.headerStyle.btn.radius + 'px',
                'margin-top': ((this.headerStyle.height - buttonHeight) / 2 - 5) + 'px',
                'font-size': this.headerStyle.btn.fontSize + 'px',
                'box-shadow': boxShadow,
                'padding-top': this.headerStyle.btn.paddingX + 'px',
                'padding-bottom': this.headerStyle.btn.paddingX + 'px',
                'padding-left': this.headerStyle.btn.paddingY + 'px',
                'padding-right': this.headerStyle.btn.paddingY + 'px'
            }
        },
        getPosterHeaderStyle(data) {
            return {
                'background': data.bg,
                'color': data.text.color,
                'font-size': data.text.size + 'px',
                'text-align': 'center',
                'left': Math.min(data.position.x, data.position.ex) + '%',
                'top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%'
            }
        },
        contentStyle(data) {
            return {
                'padding': data.padding + 'px',
                'background': data.bg,
                'z-index': data.zIndex,
                'left': Math.min(data.position.x, data.position.ex) + '%',
                'top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'border-radius': data.borderRadius + 'px'
            }
        },
        contentHeaderStyle(data) {
            return {
                'color': data.color,
                'font-size': data.size + 'px'
            }
        },
        contentBodyStyle(data) {
            return {
                'color': data.color,
                'font-size': data.size + 'px',
                'margin-left': Math.min(data.position.x, data.position.ex) + '%',
                'margin-top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'word-break': 'break-all',
                'background': data.bg,
                'border-radius': data.borderRadius + 'px',
                'padding': data.padding + 'px',
            }
        },
        contentMediaStyle(data) {
            return {
                'margin-left': Math.min(data.position.x, data.position.ex) + '%',
                'margin-top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'border-radius': data.borderRadius + 'px',
                'padding': data.padding + 'px',
                'background': data.bg
            }
        },
        fileType (media) {
            if (media !== undefined) {
                return media.split('.').pop()
            }
        },
        getBackdrop(data) {
            if (data.category && data.category.backdrop) {
                return {
                    'background': 'url(/assets/img/poster-backdrop/' + data.category.backdrop.media + ')',
                    'background-position': 'bottom'
                }
            }
        },
        viewAsset(item) {
            this.view = item.item
            this.$refs['poster_view_modal'].show()
        },
        viewPosterItem(item) {
            this.view = item
            this.$refs['poster_view_modal'].show()
        },
    }
}
</script>

<style scoped>
    .slick-arrow {
        position: absolute;
        top: 20%;
        display: block;
        border: none;
        border-radius: 8px;
        padding: 8px 24px;
        z-index: 9
    }
    .slick-arrow:hover {
        box-shadow: 0 0 50px 0 white;
    }
    .slick-next {
        right: 2%;
    }
    .slick-prev {
        left: 2%;
    }
    .slick-track {
        padding-bottom: 0!important;
    }
    .poster-item {
        width: 100%;
        height: 24vw;
    }
    .view-header {
        font-size: 1.4vw;
        background: rgba(255, 255, 255, .8);
        border-radius: 1rem;
        width: fit-content;
        padding: 0 4rem;
    }
    .single-view {
        width: 36vw;
        height: 24vw;
        border-radius: 2rem;
    }
    .item {
        width: 20vw;
        height: 24vw;
        border-radius: 1rem;
    }
    .position-center {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .object-fit {
        object-fit: contain;
    }
    .backHall {
        top: 5%;
        left: 2%;
        color: red;
        font-weight: 900;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        z-index: 99;
    }
    .view-title {
        background: #922c88;
        text-transform: capitalize;
        color: white;
        padding: 16px;
        text-transform: capitalize;
    }
    .close-btn {
        border-radius: 50%;
        font-size: 32px;
        background: #212121;
        color: white;
        box-shadow: 0px 0px 1px 1px black;
        position: absolute;
        right: -16px;
        top: -16px;
        cursor: pointer;
    }
    .view-pdf {
        cursor: pointer;
        height: 80%;
        top: 20%;
        background: transparent;
    }
    .nav-pills .nav-link {
        border-radius: 8px;
    }
    .modal-body {
        overflow: auto;
    }
    .modal-header {
        background: white;
    }
</style>