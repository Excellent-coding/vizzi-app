<template>
    <div>
        <div v-if="!onView">
            <div class="disable-text-selection">
                <list-page-heading 
                    :title="'Layout View'"
                    :displayMode="displayMode"
                    :tabIndex="hallIndex"
                    :categoryData="categoryData"
                    :entryData="entryData"
                    :rankOptions="rankOptions"
                    :changeDisplayMode="changeDisplayMode"
                    :changeOrderBy="changeOrderBy"
                />
            </div>
            <b-tabs v-model="hallIndex">
                <b-tab title="Poster Management">
                    <b-row v-if="displayMode == 'image'">
                        <b-colxx md="6" sm="12" lg="4" xxs="12" v-for="item in items" :key="item.id">
                            <b-card class="mb-4 text-center" :title="item.title.substring(0, 120)">
                                <div class="w-100 position-relative" v-if="item.category">
                                    <b-badge variant="danger" pill class="position-absolute badge-top-left" v-if="item.status == 2">Published</b-badge>
                                    <b-badge variant="primary" pill class="position-absolute badge-top-left" v-else-if="item.status == 1">Activated</b-badge>
                                    <b-badge variant="info" pill class="position-absolute badge-top-left-2" v-else>Working</b-badge>
                                    <b-badge variant="success" pill class="position-absolute badge-top-left-2" v-if="item.award">Award</b-badge>
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
                                <div class="d-flex justify-content-end align-items-center mt-2">
                                    <b-button variant="primary" size="sm" @click="select(item)">Edit</b-button>
                                    <b-button variant="outline-primary" size="sm" @click="viewPoster(item)" class="ml-2">View</b-button>
                                    <b-dropdown variant="primary" size="sm" text="Action" class="ml-auto" v-if="user && user.role < 3">
                                        <b-dropdown-item @click="setAward(item.id)">
                                            <template v-if="item.award">Unaward</template>
                                            <template v-else>Award</template>
                                        </b-dropdown-item>
                                        <b-dropdown-divider/>
                                        <b-dropdown-item @click="setStatus(item.id, 1)">Active</b-dropdown-item>
                                        <b-dropdown-item @click="setStatus(item.id, 2)">Publish</b-dropdown-item>
                                        <b-dropdown-item @click="setStatus(item.id, 0)">Reject</b-dropdown-item>
                                        <b-dropdown-item @click="setStatus(item.id, 3)">Remove</b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </b-card>
                        </b-colxx> 
                    </b-row>
                    <b-row v-else-if="displayMode==='thumb'" key="thumb">
                        <b-colxx xxs="12" class="mb-3" v-for="(item, index) in items" :key="index" :id="item.id">
                            <thumb-list-item
                                :key="item.id"
                                :data="item"
                                :select="select"
                                :user="user"
                                :setStatus="setStatus"
                                :viewPoster="viewPoster"
                                :viewThumb="viewThumb"
                                :typeImg="typeImg"
                            />
                        </b-colxx>
                    </b-row>
                    <paginate-nav
                        :lastPage='lastPage'
                        :page='page'
                        :changePage='changePage'
                    />
                </b-tab>
                <b-tab title="Poster Hall Setting" v-if="user && user.role < 3">
                    <div class="table-responsive">
                        <b-table
                            :fields="hallFields"
                            :items="hallData"
                            hover
                        >
                            <template v-slot:cell(#)="data">
                                <div class="mt-2">{{data.index + 1}}</div>
                            </template>
                            <template v-slot:cell(status)="data">
                                <b-badge class="mt-2" pill :variant="'info'" v-if="data.item.status == 0">Inactive</b-badge>
                                <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status == 1">Active</b-badge>
                            </template>
                            <template v-slot:cell(category)="data">
                                <b-button variant="outline-primary" @click="data.toggleDetails" style="min-width: 120px">
                                    {{data.detailsShowing ? 'Hide' : 'Show'}} Items
                                </b-button>
                            </template>
                            <template v-slot:row-details="row">
                                <b-table
                                    :fields="boothFields"
                                    :items="JSON.parse(row.item.categories)"
                                    hover
                                >
                                    <template v-slot:cell(#)="data">
                                        <div class="mt-2">{{data.index + 1}}</div>
                                    </template>
                                    <template v-slot:cell(category)="data">
                                        <div class="mt-2">{{data.item.title}}</div>
                                    </template>
                                    <template v-slot:cell(action)="data">
                                        <b-button variant="primary" @click="delItem(data.index, row.item)">Delete</b-button>
                                    </template>
                                </b-table>
                            </template>
                            <template v-slot:cell(action)="data">
                                <b-dropdown variant="primary" right text="Action">
                                    <b-dropdown-item @click="editItem(data.item)">Edit</b-dropdown-item>
                                    <hr>
                                    <b-dropdown-item @click="hallStatus(data.item.id, 1)">Active</b-dropdown-item>
                                    <b-dropdown-item @click="hallStatus(data.item.id, 0)">InActive</b-dropdown-item>
                                    <b-dropdown-item @click="hallStatus(data.item.id, 2)">Remove</b-dropdown-item>
                                </b-dropdown>
                            </template>
                        </b-table>
                    </div>
                </b-tab>
            </b-tabs>
        </div>
        <template v-else>
            <div class="py-5 bg position-relative text-center" :style="getBackdrop(viewData)">
                <div class="backHall position-absolute bg-white p-2" @click="onView = false">
                    Return
                </div>
                <template v-if="viewData.type < 3">
                    <div class="view-header mx-auto mb-4 text-capitalize">{{viewData.title.substring(0, 120)}}</div>
                    <div v-if="viewData.type == 1">
                        <div v-if="viewData.media" class="text-center single-view mx-auto position-relative">
                            <img :src="'/data/' + viewData.media" class="w-100 h-100 object-fit" v-if="typeImg(viewData.media)" />
                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="typeVideo(viewData.media)">
                                <source class="w-100 h-100 object-fit" :src="/data/ + viewData.media" type="video/mp4">
                            </video>
                            <iframe class="poster-item w-100 h-100 object-fit" :src="'/data/'+viewData.media" v-else />
                        </div>
                    </div>
                    <div v-else-if="viewData.type == 2" class="text-center">
                        <div v-if="viewData.media && JSON.parse(viewData.media) && JSON.parse(viewData.media).length" class="multiple-view d-flex justify-content-end align-items-center">
                            <div v-for="(item, index) in JSON.parse(viewData.media)" :key="index" class="item mx-auto bg-white position-relative">
                                <img :src="'/data/' + item" class="w-100 h-100 object-fit" v-if="typeImg(item)"  />
                                <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="typeVideo(item)">
                                    <source class="w-100 h-100 object-fit" :src="/data/ + item" type="video/mp4" />
                                </video>
                                <div class="position-relative w-100 h-100" v-else-if="typePdf(item)" >
                                    <div class="position-absolute w-100 view-pdf" />
                                    <iframe class="w-100 h-100" :src="'/data/' + item" />
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <div v-else style=" height: calc(24vw + 9rem)">
                    <div class="position-absolute" :style="getPosterHeaderStyle(posterHeader)" style="text-transform: capitalize">{{viewData.title.substring(0, 120)}}</div>
                    <template v-if="viewData.layers">
                        <div class="position-absolute position-relative" v-for="(content, index) in JSON.parse(viewData.layers)" :key="index" :style="contentStyle(content)">
                            <div :style="contentHeaderStyle(content.header)">{{content.title.substring(0, 120)}}</div>
                            <div :style="contentBodyStyle(content.body)" v-if="content.type == 'text'">{{content.text}}</div>
                            <div :style="contentMediaStyle(content.media)" v-else>
                                <img :src="'/data/' + content.media.file" class="w-100 h-100 object-fit" v-if="fileType(content.media.file) !== 'mp4' && fileType(content.media.file) !== 'pdf'" />
                                <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(content.media.file) == 'mp4'">
                                    <source class="w-100 h-100 object-fit" :src="/data/ + content.media.file" type="video/mp4" />
                                </video>
                                <div class="position-relative w-100 h-100" v-else >
                                    <div class="position-absolute w-100 view-pdf"/>
                                    <iframe class="w-100 h-100" :src="'/data/' + content.media.file" />
                                </div>   
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="container" v-if="viewData.header && viewData.header.length">
                <b-row class="mx-0">
                    <b-col class="exhibit-header text-center" :style="getHeaderStyle()">
                        <div class="d-inline-flex">
                            <div
                                class="header-btn"
                                v-for="(item, index) in viewData.header"
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
                                    <b-button variant="outline-primary" size="sm" v-b-modal.poster_view_modal @click="view = media.item">View</b-button>
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

        <b-modal ref="hall_modal" id="hall_modal" :title="hallTitle" centered>
            <b-form class="text-left">
                <b-form-group label="Hall Name">
                    <b-form-input v-model="hallItem.name"/>
                </b-form-group>
                <b-form-group label="Poster Categories" class="my-2">
                    <multiselect
                        v-model="hallItem.categories"
                        :options="[{all: 'All', hallOptions}]"
                        :multiple="true"
                        group-values="hallOptions"
                        group-label="all"
                        :group-select="true"
                    />
                </b-form-group>
            </b-form>
            <template slot="modal-footer">
                <b-button variant="secondary" @click="hideModal('hall_modal')" class="mr-1">Cancel</b-button>
                <b-button variant="primary" @click="addHall()" class="ml-1">Confirm</b-button>
            </template>
        </b-modal>
        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal('confirm_modal')">Cancel</b-button>
            </template>
        </b-modal>
        <b-modal
            ref="poster_modal"
            id="poster_modal"
            title="Poster"
            hide-footer
            centered
            :size="'size-' + this.modalWidth"
        >
            <img :src="posterMedia" class="w-100" v-if="typeImg(posterMedia)" />
            <iframe :src="posterMedia" class="w-100" v-else />
        </b-modal>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'

import ListPageHeading from "../../../../containers/Web/ListPageHeading"
import ThumbListItem from "../../../../components/Pages/ThumbListItem"
import PaginateNav from '../../../../components/Common/PaginateNav'
import mixins from '../../../../mixins/mixins'

export default {
    metaInfo () {
        return { title: `Poster Models` }
    },
    components: {
        Multiselect, ListPageHeading, ThumbListItem, PaginateNav
    },
    mixins: [mixins],
    data() {
        return {
            apiBase: '/wizard/',
            displayMode: "image",
            totalItems: [],
            items: [],
            filterItems: [],
            selectedItems: [],
            type: 2,
            hallTitle: 'Create new Poster Hall',
            hallIndex: 0,
            hallItem: {
                name: '',
                categories: [],
                list: [],
                id: 0,
                siteId: null,
                type: null
            },
            hallCategories: [],
            hallData: [],
            notifyText: 'Successfully Changed!',
            isConfirmed: false,
            hallFields: [
                '#',
                { key: 'name', sortable: true },
                { key: 'status', sortable: true },
                { key: 'category', sortable: true },
                { key: 'action', sortable: false}
            ],
            boothFields: [
                '#',
                { key: 'category', sortable: true },
                { key: 'action', sortable: false}
            ],
            onView: false,
            viewData: {},
            headers: [],
            media: [],
            layers: [],
            categoryOptions: ['All'],
            hallOptions: [],
            rankOptions: ['All', 'Awarded', 'General'],
            entryData: [],
            rankOptions: ['All', 'Award', 'General'],
            categoryData: [],
            filter: {
                category: null,
                entry_id: null,
                rank: null
            },
            selectedId: null,
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
            posterMedia: '',
            page: 0,
            lastPage: 0
        }
    },
    mounted() {
        this.init()
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            boothId: 'booth/getId',
            user: 'auth/user',
            modalWidth: 'site/modalWidth'
        })
    },
    methods: {
        init() {
            Axios.post(this.apiBase + 'get-poster', {siteId: this.siteId, filter: this.filter}).then(res => {
                if (res.status == 200) {
                    this.items = res.data.posters.data
                    this.page = res.data.posters.current_page
                    this.lastPage = res.data.posters.last_page
                    this.hallData = res.data.hallData
                    this.categoryData = res.data.categoryData
                    this.categoryData.forEach(category => {
                        this.hallOptions.push(category.category)
                    })
                    this.entryData = res.data.entryData
                }
            })
        },
        async select(item) {
            if (item.category) {
                if (this.user.role < 3) {
                    this.$router.push('/app/wizard/poster/edit/' + item.id)
                } else {
                    this.$router.push('/wizard/poster/edit/' + item.id)
                }
            } else {
                await this.$store.dispatch('booth/setItem', item)
                if (this.user.role < 3) {
                    this.$router.push({name: 'poster.main'})
                } else {
                    this.$router.push({name: 'wizard.poster.main'})
                }
            }
        },
        addHall() {
            if (this.hallItem.name == '' || this.hallItem.categories.length == 0) {
                this.$notify('primary filled', '', 'Please Insert Fields', { duration: 3000, permanent: false })
            } else {
                this.hallItem.siteId = this.siteId
                this.hallItem.type = this.type
                this.hallItem.categories.forEach(item => {
                    this.categoryData.forEach(element => {
                        if (element.category == item) {
                            this.hallItem.list.push({
                                title: element.category,
                                id: element.id
                            })
                        }
                    })
                })
                Axios.post(this.apiBase + 'hall-set', this.hallItem).then(res => {
                    this.$notify('primary filled', '', 'Successfully Saved', { duration: 3000, permanent: false })
                    this.hallData = res.data
                })
                this.hallItem = {
                    name: '',
                    items: [],
                    list: [],
                    id: 0,
                    siteId: null
                }
                this.$refs['hall_modal'].hide()
            }
        },
        async changePage(pageNum) {
            this.page = pageNum
            const { data } = await Axios.get(
                this.apiBase + 'get-poster?page=' + pageNum, 
                {params: {
                    id: this.siteId
                }}
            )
            this.items = data.data
            this.page = data.current_page
            this.lastPage = data.last_page
        },
        viewPoster(item) {
            this.media = []
            this.exhibitHtml = ''
            this.header = []
            this.viewData = item
            if (this.viewData.media) {
                if (this.viewData.type == 1) {
                    this.media = this.viewData.media
                } else if (this.viewData.type == 2) {
                    this.media = JSON.parse(this.viewData.media)
                }
            }
            if (this.viewData.type == 3) {
                if (this.viewData.layers) {
                    this.layers = JSON.parse(this.viewData.layers)
                }
                if (this.viewData.posterHeader) {
                    this.posterHeader = JSON.parse(this.viewData.posterHeader)
                }
            }
            if (this.viewData.header_style) {
                this.headerStyle = JSON.parse(this.viewData.header_style)
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
            if (this.viewData.header && this.viewData.header.length) {
                this.exhibitHtml = this.viewData.header[0]
            }
            this.onView = true
        },
        viewThumb(data) {
            this.posterMedia = data
            this.$refs.poster_modal.show()
        },
        return() {
            this.onView = false
        },
        getBackdrop(data) {
            if (data.category && data.category.backdrop) {
                return {
                    'background': 'url(/assets/img/poster-backdrop/' + data.category.backdrop.media + ')',
                    'background-position': 'bottom'
                }
            }
        },
        delItem(dataIndex, item) {
            var list = []
            JSON.parse(item.categories).forEach((element, index) => {
                if (dataIndex != index) {
                    list.push(element)
                }
            });
            this.hallItem = {
                id: item.id,
                siteId: item.domain_id,
                list: list,
                type: item.type,
                name: item.name
            }
            Axios.post(this.apiBase + 'hall-set', this.hallItem).then(res => {
                this.$notify('primary filled', '', 'Successfully Saved', { duration: 3000, permanent: false })
                this.hallData = res.data
            })
            this.hallItem = {
                name: '',
                items: [],
                list: [],
                id: 0,
                siteId: null
            }
        },
        editItem(item) {
            var categories = []
            if (item.categories) {
                JSON.parse(item.categories).forEach(element => {
                    categories.push(element.title)
                })
            }
            this.hallItem = {
                name: item.name,
                categories: categories,
                list: [],
                id: item.id,
                siteId: null
            }
            this.$refs['hall_modal'].show()
        },
        hideModal(ref) {
            this.$refs[ref].hide()
            this.memberId = 0
        },
        hallStatus(id, status) {
            Axios.post(this.apiBase + 'hall-status', {id: id, status: status, siteId: this.siteId, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.hallData = res.data
                    this.$notify('primary filled', '', 'Successfully Changed', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        changeDisplayMode(displayType) {
            this.displayMode = displayType;
        },
        changeOrderBy(data, type) {
            if (type == 'category') {
                this.filter.category = data
            } else if (type == 'number') {
                this.filter.entry_id = data
            } else {
                this.filter.rank = data
            }
        },
        confirm() {
            this.isConfirmed = true
            this.$refs.confirm_modal.hide()
            this.setStatus(this.selectedId, 3)
        },
        setAward(id) {
            Axios.post(this.apiBase + 'award', {id: id, siteId: this.siteId, type: this.type, filterr: this.filter}).then(res => {
                this.items = res.data.data
                this.page = res.data.current_page
                this.lastPage = res.data.last_page
            })
        },
        async setStatus(id, status) {
            this.selectedId = id
            if (status == 3 && !this.isConfirmed) {
                this.notifyText = 'Successfully Deleted!'
                this.$refs.confirm_modal.show()
            } else {
                const res = await Axios.post(this.apiBase + 'poster/set-status', {id: this.selectedId, status: status, siteId: this.siteId}).then(res => {
                    if (res.status == 200) {
                        this.items = res.data.data
                        this.page = res.data.current_page
                        this.lastPage = res.data.last_page
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                    }
                })
                this.isConfirmed = false
                this.notifyText = 'Successfully Changed!'
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
            if (data) {
                this.headerStyle = data
            }
            return {
                'background-color': this.headerStyle.bg,
                'height': this.headerStyle.height + 'px'
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
        fileType (media) {
            if (media !== undefined) {
                return media.split('.').pop()
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
        getExhibitButtonStyle(data) {
            let boxShadow = '0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12)'
            let bg = this.headerStyle.btn.bg
            if (!this.headerStyle.btn.boxShadow) {
                boxShadow = 'none'
            }
            if (!this.headerStyle.btn.hasBg) {
                bg = 'transparent'
            }
            const buttonHeight = 2 * Number(this.headerStyle.btn.paddingX) + Number(this.headerStyle.btn.fontSize)
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
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        },
    },
    watch: {
        filter: {
            deep: true,
            handler() {
                this.init()
            }
        }
    }
}
</script>

<style>
    .model {
        height: 160px;
        background-size: cover;
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
    .object-fit {
        object-fit: contain;
    }

    .modal-body {
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
</style>