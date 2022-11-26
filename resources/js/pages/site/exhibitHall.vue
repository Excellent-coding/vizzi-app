<template>
    <div>
        <template v-if="type < 2">
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
                    <div class="backHall m-2 bg-white p-2 position-absolute" @click="onDetail = false">
                        Return to Exhibit Hall
                    </div>
                    <div class="prevBooth" style="top: 3%" @click="backBooth">Back</div>
                    <div class="nextBooth" style="top: 3%" @click="nextBooth">Next</div>
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
                    <chat-box
                        :item="exhibitItem"
                        :type="type"
                    />
                </div>
                <div class="container" v-if="exhibitItem.header && exhibitItem.header.length">
                    <b-row>
                        <b-col class="exhibit-header text-center w-100" :style="getHeaderStyle(JSON.parse(exhibitItem.header_style))">
                            <div class="d-inline-flex">
                                <div
                                    class="header-btn"
                                    v-for="(item, index) in exhibitItem.header"
                                    :key="index"
                                    :style="getExhibitButtonStyle(JSON.parse(exhibitItem.header_style))"
                                    @click="exhibitHtml = item, currentTab = item.tab[0]"
                                >
                                {{item.title}}
                                </div>
                            </div>
                        </b-col>
                    </b-row>
                    <div style="min-height: 200px" class="mt-4" v-if="exhibitHtml.tab">
                        <b-button
                            style="border-radius: 6px"
                            class="mx-1"
                            variant="primary"
                            v-for="tab in exhibitHtml.tab"
                            :key="tab.id"
                            @click="currentTab = tab"
                        >{{capitalizeFirstLetter(tab.title)}}</b-button>
                        <h6 class="py-2" v-html="currentTab.content"></h6>
                        <div v-for="media in currentTab.media" :key="media.id">
                            <div class="d-flex justify-content-end align-items-center mb-2">
                                <div class="position-relative mr-3">
                                    <div class="position-absolute front-type-text" :style="getStyle(media.item)">{{getTypeText(media.item)}}</div>
                                    <img :src="getType(media.item)" style="width: 48px" />
                                </div>
                                <h6 class="mr-auto my-auto text-capitalize">{{media.title}}</h6>
                                <b-button variant="outline-primary" size="sm" v-b-modal.view_modal @click="boothView(media)">View</b-button>
                                <b-button variant="primary" size="sm" @click="addCartItem(media)" class="ml-2">
                                    {{getMark(media) ? '-' : '+'}} {{$ct('cart')}}
                                </b-button>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>    
        </template>
        <template v-else>
            <div class="px-5" v-if="isHall">
                <template v-if="!overview">
                    <b-card class="w-75 w-md-100 mx-auto my-3 p-4" no-body>
                        <h4>Select Poster Presentation by Filter</h4>
                        <div class="d-block justify-content-end align-items-center d-md-flex">
                            <b-form-group label="Filter by Category" class="has-float-label mb-4 mr-auto w-25 w-md-100">
                                <multiselect
                                    v-model="filter.category"
                                    :options="categoryOptions"
                                />
                            </b-form-group>
                            <b-form-group label="Filter by Poster Number" class="has-float-label mb-4 mx-auto w-25 w-md-100">
                                <multiselect
                                    v-model="filter.entry_id"
                                    :options="orderOptions"
                                />
                            </b-form-group>
                            <b-form-group label="Filter by Ranking" class="has-float-label mb-4 mx-auto w-25 w-md-100">
                                <multiselect
                                    v-model="filter.rank"
                                    :options="rankOptions"
                                />
                            </b-form-group>
                            <b-button variant="outline-dark" class="ml-auto w-md-100 mb-4" @click="viewFilter">Search</b-button>
                        </div>
                    </b-card>

                    <b-card no-body v-for="item in categories" :key="item.id" class="container p-2 my-2 d-flex flex-row w-30 w-lg-60 w-md-100">
                        <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                            <h4 class="my-auto mr-auto">{{item.title}}</h4>
                            <b-button variant="outline-primary" size="xs" @click="viewCategoryItems(item.id)">View</b-button>
                        </div>
                    </b-card>

                    <paginate-nav
                        :lastPage='lastPage'
                        :page='page'
                        :changePage='changePage'
                    />
                </template>
                <template v-else>
                    <div class="backHall m-2 bg-white p-2" @click="overview = false">
                        Return to Poster Hall
                    </div>
                    <b-row>
                        <b-colxx md="4" sm="12" lg="3" xxs="12" v-for="(item, index) in items" :key="item.id">
                            <b-card class="mb-4 text-center" :title="item.title.substring(0, 120)" @click="goPage(index)">
                                <div class="w-100 position-relative" v-if="item.category">
                                    <template v-if="item.type == 1 && item.media">
                                        <img :src="'/data/' + item.media" alt="Poster" class="w-100 model" v-if="typeImg(item.media)" />
                                        <iframe :src="'/data/' + item.media" alt="Poster" class="w-100 model" v-else/>
                                    </template>
                                    <template v-else-if="item.type == 2 && item.media && JSON.parse(item.media)">
                                        <img :src="'/data/' + JSON.parse(item.media)[0]" alt="Poster" class="w-100 model" v-if="typeImg(JSON.parse(item.media)[0])" />
                                        <iframe :src="'/data/' + JSON.parse(item.media)[0]" alt="Poster" class="w-100 model" v-else></iframe>
                                    </template>
                                    <template v-else>
                                        <img :src="'/assets/img/poster-backdrop/' + item.category.backdrop.media" alt="Poster" class="w-100 model" v-if="typeImg(item.category.backdrop.media)" />
                                        <iframe :src="'/assets/img/poster-backdrop/' + item.category.backdrop.media" alt="Poster" class="w-100 model" v-else/>
                                    </template>
                                </div>
                            </b-card>
                        </b-colxx>
                    </b-row>
                </template>
            </div>
            <template v-else>
                <div class="py-5 position-relative" :style="getBackdrop(posterItem)">
                    <div class="backHall position-absolute bg-white p-2" @click="isHall = true, filter = {category: null, entry_id: null,rank: null}, overview = false">
                        Return to Poster Hall
                    </div>
                    <div class="container position-relative" style=" height: calc(24vw + 9rem)">
                        <div v-if="posterItem.type == 1">
                            <div v-if="posterItem.media" class="text-center zoom-part">
                                <div class="view-header mx-auto mb-4" style="text-transform: capitalize">{{posterItem.title.substring(0, 120)}}</div>
                                <div class="single-view mx-auto position-relative" @click="viewPosterItem(posterItem.media)">
                                    <img :src="'/data/' + posterItem.media" class="w-100 h-100 object-fit" v-if="typeImg(posterItem.media)" />
                                    <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="typeVideo(posterItem.media)">
                                        <source class="w-100 h-100 object-fit" :src="/data/ + posterItem.media" type="video/mp4">
                                    </video>
                                    <iframe class="poster-item" :src="'/data/'+posterItem.media" v-else />
                                </div>
                                <div class="zoom-btn mt-2 mx-auto" style="" @click="viewPosterItem(posterItem.media)">View Enlarged Poster</div>
                            </div>
                        </div>
                        <div v-else-if="posterItem.type == 2">
                            <div v-if="posterItem.media && JSON.parse(posterItem.media)" class="text-center">
                                <div class="view-header mx-auto mb-4" style="text-transform: capitalize">{{posterItem.title.substring(0, 120)}}</div>
                                <div class="multiple-view d-flex justify-content-end align-items-center">
                                    <div v-for="(item, index) in JSON.parse(posterItem.media)" :key="index" class="mx-auto zoom-part mb-auto" @click="viewPosterItem(item)">
                                        <div class="item position-relative bg-white">
                                            <img :src="'/data/' + item" class="w-100 h-100 object-fit" v-if="typeImg(item)"/>
                                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="typeVideo(item)">
                                                <source class="w-100 h-100 object-fit" :src="/data/ + item" type="video/mp4"/>
                                            </video>
                                            <div class="position-relative w-100 h-100" v-else-if="typePdf(item)" >
                                                <div class="position-absolute w-100 view-pdf"/>
                                                <iframe class="w-100 h-100" :src="'/data/' + item" />
                                            </div>
                                        </div>
                                        <div class="zoom-btn mx-auto mt-2" style="">View Enlarged Poster</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="posterItem.type == 3">
                            <div class="position-absolute" :style="getPosterHeaderStyle(posterHeader)" style="text-transform: capitalize">{{posterItem.title.substring(0, 120)}}</div>
                            <template v-if="posterItem.layers">
                                <div class="position-absolute position-relative zoom-part" v-for="(content, index) in JSON.parse(posterItem.layers)" :key="index" :style="contentStyle(content)">
                                    <div :style="contentHeaderStyle(content.header)">{{content.title.substring(0, 120)}}</div>
                                    <div :style="contentBodyStyle(content.body)" v-if="content.type == 'text'">{{content.text}}</div>
                                    <div :style="contentMediaStyle(content.media)" class="position-relative" v-else @click="viewPosterItem(content.media.file)">
                                        <img :src="'/data/' + content.media.file" class="w-100 h-100 object-fit" v-if="fileType(content.media.file) !== 'mp4' && fileType(content.media.file) !== 'pdf'" />
                                        <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(content.media.file) == 'mp4'">
                                            <source class="w-100 h-100 object-fit" :src="/data/ + content.media.file" type="video/mp4">
                                        </video>
                                        <div class="position-relative w-100 h-100" v-else >
                                            <div class="position-absolute w-100 view-pdf"/>
                                            <iframe class="w-100 h-100" :src="'/data/' + content.media.file" />
                                        </div>   
                                    </div>
                                    <div class="zoom-btn mx-auto mt-2" style="" @click="viewPosterItem(content.media.file)">View Enlarged Poster</div>
                                </div>
                            </template>
                        </div>
                        <chat-box
                            :item="posterItem"
                            :type="type"
                        />
                    </div>

                    <button class="slick-arrow slick-prev" @click="prevPoster">Back</button>
                    <button class="slick-arrow slick-next" @click="nextPoster">Next</button>
                </div>
                <div class="container" v-if="posterItem.header && posterItem.header.length">
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
                                        <b-button variant="outline-primary" size="sm" v-b-modal.view_modal @click="boothView(media)">View</b-button>
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
            </template>
        </template>
        <b-modal ref="view_modal" id="view_modal" :size="'size-' + modalWidth" centered hide-footer class="text-center">
            <template v-slot:modal-header>
                <h5 v-if="viewItem.title" class="text-capitalize text-dark">{{viewItem.title}}</h5>
                <h5 v-else class="text-capitalize text-dark">{{exhibitItem.title}}</h5>
                <i class="close-btn simple-icon-close" @click="hideModal('view_modal')"></i>
            </template>
            <template v-if="viewItem.item">
                <template v-if="viewItem.item.substr(0, 4) == 'http'">
                    <iframe class="w-100 bg-dark" style="height: 75vh" :src="viewItem.item" />
                </template>
                <template v-else>
                    <video v-if="fileExtension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
                        <source :src="'/data/'+viewItem.item" type="video/mp4">
                    </video>

                    <iframe class="w-100" :src="'/data/'+viewItem.item" v-else-if="fileExtension === 'pdf'" style="height: 75vh" />

                    <img class="w-100" :src="'/data/'+viewItem.item" v-else />
                </template>
            </template>
        </b-modal>
        <b-modal
            id="poster_view_modal"
            ref="poster_view_modal"
            :size="'size-' + modalWidth"
            centered
            hide-footer
        >
            <template v-slot:modal-header="{ close }">
                <div class="text-center w-100">
                    <h2 class="view-title">{{posterItem.title}}</h2>
                </div>
                <i class="simple-icon-close poster-close-btn" @click="close"></i>
            </template>
            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-if="typeVideo(view)">
                <source class="w-100" :src="/data/ + view" type="video/mp4">
            </video>
            <iframe class="w-100" style="height: 60vh" :src="'/data/'+view" v-else-if="typePdf(view)" />
            <iframe class="w-100" style="height: 60vh" :src="view" v-else-if="view.substr(0, 4) == 'http'" />
            <img :src="'/data/' + view" class="w-100" v-else />
        </b-modal>
        <b-modal ref="view_items_modal" id="view_items_modal" :size="'size-' + modalWidth" centered hide-footer>
            <div v-for="(item, index) in viewItems" :key="index">
                <div class="d-flex flex-row">
                    <img class="list-thumbnail responsive border-0" :src="getType(item.file)" />
                    <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                        <div
                            class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center"
                        >
                            <p class="list-item-heading mb-1 truncate">{{item.title}}</p>
                        </div>
                    </div>
                    <div class="my-auto mr-2">
                        <button
                            type="button"  
                            class="btn btn-outline-primary mb-1"
                            @click="view(item)"
                        >View</button>
                    </div>
                </div>
                <hr />
            </div>
        </b-modal>
    </div>
</template>

<script>
import Axios from 'axios'
import VueSlickCarousel from 'vue-slick-carousel'
import 'vue-slick-carousel/dist/vue-slick-carousel.css'
import ChatBox from '../../containers/Chat/ChatBox'
import mixins from '../../mixins/mixins'
import { mapGetters } from 'vuex'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import PaginateNav from '../../components/Common/PaginateNav'

export default {
    components: {
        VueSlickCarousel,
        Multiselect,
        ChatBox,
        PaginateNav
    },
    computed: {
        ...mapGetters({
            siteStyle: 'site/getStyle',
            modalWidth: 'site/modalWidth',
            siteId: 'site/getSiteId'
        })
    },
    mixins: [mixins],
    props: [
      'cart-open', 'agenda-open'
    ],
    data() {
        return {
            url: '',
            apiBase: '/site/exhibit/',
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
            siteHeaderStyle: {
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
            isAdded: [],

            id: null,
            isShow: false,
            posterItem: {},
            headers: [],
            media: [],
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
            view: '',
            type: null,
            isHall: true,
            categories: [],
            cateItems: [],
            viewItems: [],
            modalItems: [],
            viewTitle: '',
            fileExtension: '',
            filter: {
                category: null,
                entry_id: null,
                rank: null
            },
            categoryOptions: [],
            categories: [],
            orderOptions: [],
            rankOptions: ['All', 'Award', 'General'],
            page: 0,
            lastPage: 0,
            id: null,
            overview: false,
            currentTab: {}
        }
    },
    mounted() {
        if (this.siteStyle) {
            this.siteHeaderStyle = JSON.parse(this.siteStyle)
            this.siteHeaderStyle = this.siteHeaderStyle.style
        }
        window.setInterval(this.init, 3000)

        this.init()
    },
    methods: {
        async init() {
            if (this.url != window.location.pathname) {
                this.url = window.location.pathname
                const {data} = await Axios.post(this.apiBase + 'get', {url: this.url.substr(8, this.url.length)})
                if (data.data && data.data.type) {
                    this.type = data.data.type
                    this.categoryOptions = data.categoryOptions
                    this.categories = data.categories.data
                    this.page = data.categories.current_page
                    this.id = data.data.id
                    this.lastPage = data.categories.last_page
                    this.orderOptions = data.orderOptions
                }
                if (data.type == 0 || data.type == 1) {
                    this.type = data.type
                    this.items = data.booth_items
                }
            }
        },
        hideModal(ref) {
            this.$refs[ref].hide()
            document.body.className = document.body.className.replace("modal-open", "")
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
            this.currentTab = this.exhibitHtml.tab[0]
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
        getTopHeaderStyle() {
            return {
                'background-color': 'transparent',
                'height': this.siteHeaderStyle.height + 'px',
                'margin-top': '-' + this.siteHeaderStyle.height + 'px',
                'z-index': 90
            }
        },
        getMenuButtonStyle() {
            const buttonHeight = 2 * Number(this.siteHeaderStyle.btn.paddingX) +Number( this.siteHeaderStyle.btn.fontSize)
            return {
                'background-color': 'transparent',
                'color': 'transparent',
                'margin-left': this.siteHeaderStyle.btn.space + 'px',
                'margin-right': this.siteHeaderStyle.btn.space + 'px',
                'border-radius': this.siteHeaderStyle.btn.radius + 'px',
                'margin-top': ((this.siteHeaderStyle.height - buttonHeight) / 2 - 5) + 'px',
                'font-size': this.siteHeaderStyle.btn.fontSize + 'px',
                'padding-top': this.siteHeaderStyle.btn.paddingX + 'px',
                'padding-bottom': this.siteHeaderStyle.btn.paddingX + 'px',
                'padding-left': this.siteHeaderStyle.btn.paddingY + 'px',
                'padding-right': this.siteHeaderStyle.btn.paddingY + 'px'
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
        boothView(item) {
            this.viewItem = item
            this.fileExtension = this.viewItem.item.split('.').pop()
        },
        viewMedia(item) {
            if (item.link && this.validURL(item.link)) {
                window.location.replace(item.link)
            } else {
                this.viewItem = {
                    title: item.title,
                    item: item.url
                }
                this.fileExtension = this.viewItem.item.split('.').pop()
                this.$refs.view_modal.show()
            }
        },
        validURL(str) {
            var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
                '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
            return !!pattern.test(str);
        },
        goPage(index) {
            this.currentIndex = index
            this.getItem()
            this.isHall = false
        },
        getItem() {
            this.exhibitHtml = ''
            this.posterItem = this.items[this.currentIndex]
            if (this.posterItem.type == 3) {
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
            if (this.posterItem.header && this.posterItem.header.length) {
                this.exhibitHtml = this.posterItem.header[0]
            }
        },
        getPage(item) {
            if (item.type.toLowerCase() == 'agenda') {
                this.$parent.$emit('agenda-open')
            } else if (item.type.toLowerCase() == 'cart') {
                this.$parent.$emit('cart-open')
            } else if ((item.type == 'booth' || item.type == 'poster' || item.type == 'sponsor') && window.location.pathname != '/exhibit/' + item.url) {
                this.$router.push('/exhibit/' + item.url)
                this.url = item.url
                this.init()
            } else {
                if (item.type == 'modal') {
                    this.viewItems = []
                    this.modalItems.forEach(element => {
                        if (element.page_id == item.id) {
                            this.viewItems.push(element)
                        }
                    })
                    if (this.viewItems.length == 1) {
                        this.viewItem = this.viewItems[0]
                        this.viewTitle = this.viewItem.title
                        this.fileExtension = this.viewItem.file.split('.').pop()
                        this.$refs.view_modal.show()
                    } else {
                        this.modalTitle = item.title
                        this.$refs.view_items_modal.show()
                    }
                } else {
                    if (window.location.pathname != item.url) {
                        this.$router.push(item.url)
                    }
                }
            }
        },
        nextPoster() {
            if (this.items.length > 1) {
                this.currentIndex = this.currentIndex + 1
                if (this.currentIndex == this.items.length) {
                    this.currentIndex = 0
                }
                this.getItem()
            }
        },
        prevPoster() {
            if (this.items.length > 1) {
                this.currentIndex = this.currentIndex - 1
                if (this.currentIndex == -1) {
                    this.currentIndex = this.items.length - 1
                }
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
        isCategory(id) {
            for (var i = 0; i < this.filterItems.length; i++) {
                if (this.filterItems[i].category_id == id) {
                    return true
                }
            }
        },
        async changePage(pageNum) {
            const {data} = await Axios.get(
                this.apiBase + 'get?page=' + pageNum, 
                {params: {
                    id: this.id
                }}
            )
            this.page = data.current_page
            this.categories = data.data
        },
        async viewFilter() {
            const {data} = await Axios.post(this.apiBase + 'get-filter', {id: this.siteId, filter: this.filter})
            this.items = data
            this.overview = true
        },
        async viewCategoryItems(id) {
            this.exhibitHtml = ''
            const {data} = await Axios.post(this.apiBase + 'get-items', {id: id})
            this.items = data
            this.overview = true
        },
        initItem() {
            if (this.items.length) {
                this.posterItem = this.items[0]
                this.isHall = false
                this.currentIndex = 0
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
                if (this.posterItem.header && this.posterItem.header.length) {
                    this.exhibitHtml = this.posterItem.header[0]
                }
            } else {
                this.$notify('primary filled', '', 'Sorry! There is not matching Data!', { duration: 3000, permanent: false });
            }
        }
    }
}
</script>

<style>
    .modal-xl .modal-content {
        max-height: 80vh;
        background: lightgrey;
        border: solid 16px white;
        border-radius: 0;
    }
    .zoom-btn {
        padding: .4rem.8rem;
        background: lightgray;
        border-radius: 4px;
        display: block;
        bottom: 1rem;
        right: 1rem;
        cursor: pointer;
        width: fit-content;
        box-shadow: 0 0 4px 0 lightgray;
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
    .modal-header {
        background: rgba(0, 0, 0, .4);
        color: white;
        text-transform: capitalize;
    }
    img {
        width: 100%;
    }
    .border-rounded {
        border-radius: 1rem;
    }
    .cursor-pointer {
        cursor: pointer
    }
    .prevBooth {
        position: absolute;
        font-size: 15px;
        color: red;
        width: fit-content;
        top: 5%;
        background: white;
        padding: 0 12px;
        border-radius: 4px;
        font-weight: 900;
        cursor: pointer;
        left: 2%;
    }
    .nextBooth {
        position: absolute;
        font-size: 15px;
        color: red;
        width: fit-content;
        top: 5%;
        right: 2%;
        background: white;
        padding: 0 12px;
        border-radius: 4px;
        font-weight: 900;
        cursor: pointer;
    }
    .exhibit-header .header-btn {
        padding: 0 4px;
        font-weight: 400;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        line-height: 1.75;
        letter-spacing: 0.02857em;
        text-transform: capitalize;
        box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12);
    }
    .model {
        height: 24vh;
        object-fit: cover;
    }
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
        box-shadow: 0 0 4px;
        width: fit-content;
    }
    .view-title {
        text-transform: capitalize;
        color: darkslategrey;
        padding: 16px;
        text-transform: capitalize;
    }
    .poster-close-btn {
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
    .modal-header {
        background: white;
    }
    .modal-body {
        overflow: auto;
        max-height: 80vh;
        width: 100%;
    }
    .modal-dialog.modal-size-20 {
        max-width: 20%!important;
    }
    .modal-dialog.modal-size-40 {
        max-width: 40%!important;
    }
    .modal-dialog.modal-size-60 {
        max-width: 60%!important;
    }
    .modal-dialog.modal-size-80 {
        max-width: 80%!important;
    }
    .modal-dialog.modal-size-100 {
        max-width: 100%!important;
    }
    nav {
        width: 100%
    }
    @media (max-width: 1200px) {
        .w-lg-60 {
            width: 60%!important;
        }
    }
    @media (max-width: 991px) {
        .w-md-100 {
            width: 100%!important;
        }
    }
</style>