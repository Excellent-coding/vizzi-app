<template>
    <div>
        <b-row v-if="!onDetail" class="p-4">
            <b-colxx xxs="12" xs="4" lg="3" v-for="(item, index) in items" :key="item.id">
                <b-card class="mb-4" no-body @click="viewExhibit(item, index)" style="cursor: pointer">
                    <b-card-header class="text-center h4 mt-2 text-capitalize">{{item.title}}</b-card-header>
                    <div class="position-relative">
                        <img :src="'/data/'+item.template.media" class="card-img-top" />
                        <template v-if="item.data">
                            <div
                                :style="itemStyle(element)"
                                class="position-absolute" 
                                v-for="(element, index) in JSON.parse(item.data)"
                                :key="index"
                            >
                                <img :src="'/data/'+element.url" class="w-100 h-100" style="object-fit: cover" v-if="element.url.substring(0, 4) != 'http'" />
                                <img :src="element.url" class="w-100 h-100" style="object-fit: cover" v-else />
                            </div>
                        </template>
                    </div>
                </b-card>
            </b-colxx>
        </b-row>
        <div v-else>
            <div class="w-100 position-relative">
                <div class="prevBooth" @click="backBooth">Back</div>
                <div class="nextBooth" @click="nextBooth">Next</div>
                <img :src="'/data/' + exhibitItem.template.media" alt="Sponsor" class="w-100" v-if="exhibitItem.type != 2" id="content" />
                <template v-if="exhibitItem.data">
                    <div
                        :style="itemStyle(element)"
                        class="position-absolute" 
                        v-for="(element, index) in JSON.parse(exhibitItem.data)"
                        :key="index"
                        @click="viewMedia(element)"
                        style="cursor: pointer"
                    >
                        <img :src="'/data/'+element.url" class="w-100 h-100" style="object-fit: cover" v-if="element.url.substring(0, 4) != 'http'" />
                        <img :src="element.url" class="w-100 h-100" style="object-fit: cover" v-else />
                    </div>
                </template>
            </div>
            <div class="container">
                <b-row>
                    <b-col class="exhibit-header text-center w-100" :style="getHeaderStyle(JSON.parse(exhibitItem.header_style))">
                        <div class="d-inline-flex">
                            <div
                                class="header-btn"
                                v-for="(item, index) in exhibitItem.header"
                                :key="index"
                                :style="getExhibitButtonStyle(JSON.parse(exhibitItem.header_style))"
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
        </div>
        <b-modal ref="view_modal" id="view_modal" size="xl" centered hide-footer class="text-center">
            <template v-slot:modal-header="{ close }">
                <h5 v-if="viewItem.title" class="text-capitalize">{{viewItem.title}}</h5>
                <h5 v-else>{{exhibitItem.title}}</h5>
                <i class="close-btn simple-icon-close" @click="close()"></i>
            </template>
            <video v-if="fileExtension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
                <source :src="'/data/'+viewItem.item" type="video/mp4">
            </video>

            <iframe class="w-100" :src="'/data/'+viewItem.item" v-else-if="fileExtension === 'pdf'" style="height: 75vh" />

            <img class="w-100" :src="'/data/'+viewItem.item" v-else />
        </b-modal>
        <chat-box :item="exhibitItem" :type="0" v-if="isChat" />
    </div>
</template>

<script>
import Axios from 'axios'
import ChatBox from '../../../containers/Chat/ChatBox'

import mixins from '../../../mixins/mixins.js'

export default {
    components: {
        'chat-box': ChatBox
    },
    mixins: [mixins],
    data() {
        return {
            apiBase: '/site/',
            items: [],
            currentIndex: 0,
            exhibitItem: {},
            onDetail: false,
            header: [],
            exhibitItemData: [],
            headerData: [],
            exhibitHtml: '',
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
            isChat: false,
            viewItem: {},
            fileExtension: '',
            cartStatus: [],
            isAdded: []
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        init() {
            Axios.post(this.apiBase + 'booth', {id: this.siteId}).then(res => {
                this.items = res.data
            })
        },
        viewExhibit(item, index) {
            this.currentIndex = index
            this.exhibitItem = item
            this.setExhibit()
            this.onDetail = true
            this.isChat = true
        },
        setExhibit() {
            if (this.exhibitItem.data) {
                this.exhibitItemData = JSON.parse(this.exhibitItem.data)
            }
            if (this.exhibitItem.header[0]) {
                this.exhibitHtml = this.exhibitItem.header[0]
            }
        },
        backBooth() {
            if (this.currentIndex > 0) {
                this.currentIndex--
                this.exhibitItem = this.items[this.currentIndex]
                this.setExhibit()
            }
        },
        nextBooth() {
            if (this.items.length > (this.currentIndex + 1)) {
                this.currentIndex++
                this.exhibitItem = this.items[this.currentIndex]
                this.setExhibit()
            }
        },
        itemStyle(data) {
            return {
                'top': 'calc(' + Math.min(data.y, data.ey) + '%)',
                'left': 'calc(' + Math.min(data.x, data.ex) + '%)',
                'width': 'calc(' + Math.abs(data.ex - data.x) + '%)',
                'height': 'calc(' + Math.abs(data.ey - data.y) + '%)'
            }
        },
        getHeaderStyle(data) {
            if (data) {
                this.headerStyle = data
            }
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
        view(item) {
            this.viewItem = item
            this.fileExtension = this.viewItem.item.split('.').pop()
            this.$refs.view_modal.show()
        },
        viewMedia(item) {
           this.viewItem = {
               title: item.title,
               item: item.url
           }
           this.fileExtension = this.viewItem.item.split('.').pop()
           this.$refs.view_modal.show()
        }
    }
}
</script>

<style scoped>
    .modal-xl .modal-content {
        max-height: 80vh;
        background: lightgrey;
        border: solid 16px white;
        border-radius: 0;
    }
    .close-btn {
        font-size: 25px;
        border-radius: 50%;
        background: #212121;
        color: white;
        box-shadow: 0px 0px 1px 1px black;
        position: absolute;
        right: -14px;
        top: -14px;
        cursor: pointer;
    }
    .prevBooth, .nextBooth {
        z-index: 9
    }
    .nav-pills .nav-link {
        border-radius: 8px;
    }
    .modal-body {
        overflow: auto;
    }
    .modal-header {
        background: rgba(0, 0, 0, .4);
        color: white;
        text-transform: capitalize;
    }
</style>