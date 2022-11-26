<template>
<div>
    <b-header
        :title="title"
        :selectAll="selectAll"
        :isSelectedAll="isSelectedAll"
        :keymap="keymap"
        :isAnyItemSelected="isAnyItemSelected"
        :setStatus="setStatus"
        :initData="initData"
        :status="status"
        :type="type"
    />

    <b-card no-body>
        <b-tabs card>
            <b-tab title="Site Setting" active @click="type = ''">
                <div class="container">
                    <b-row>
                        <b-col cols="12">
                            <b-form-group label="Title">
                                <b-form-input type="text" v-model="event"/>
                            </b-form-group>
                        </b-col>
                        <b-col md="6" sm="12">
                            <b-form-group label="Upload your logo">
                                <b-input-group :prepend="$t('input-groups.upload')" class="mb-3 w-100">
                                    <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onFileChange" />
                                </b-input-group>
                            </b-form-group>
                            <div class="text-center">
                                <img v-if="url" :src="url" class="w-25"/>
                                <img v-else-if="logo" :src="'/data/' + logo" class="w-25"/>
                            </div>
                        </b-col>
                        <b-col md="6" sm="12">
                            <b-form-group label="Upload your dark logo">
                                <b-input-group :prepend="$t('input-groups.upload')" class="mb-3 w-100">
                                    <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onDarkChange" />
                                </b-input-group>
                            </b-form-group>
                            <div class="text-center">
                                <img v-if="darkUrl" :src="darkUrl" class="w-25"/>
                                <img v-else-if="darkLogo" :src="'/data/' + darkLogo" class="w-25"/>
                            </div>
                        </b-col>
                        <b-col md="6" sm="12">
                            <b-form-group label="Event Start Time">
                                <VueCtkDateTimePicker
                                    id="selectStartTime"
                                    v-model="start"
                                />
                            </b-form-group>
                        </b-col>
                        <b-col md="6" sm="12">
                            <b-form-group label="Event End Time">
                                <VueCtkDateTimePicker 
                                    id="selectEndTime"
                                    v-model="end"
                                />
                            </b-form-group>
                        </b-col>
                        <b-col md="6" sm="12">
                            Upload Welcome Video
                            <b-form-radio-group v-model="videoOption" class="my-2">
                                <b-form-radio :value="1">Upload</b-form-radio>
                                <b-form-radio :value="0">Link</b-form-radio>
                            </b-form-radio-group>
                            <b-form-group v-if="videoOption == 1">
                                <b-input-group :prepend="$t('input-groups.upload')" class="mb-3 w-100">
                                    <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onVideoChange" />
                                </b-input-group>
                            </b-form-group>
                            <b-form-group v-else>
                                <b-form-input v-model="video" />
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-button variant="primary" @click="setting">Submit</b-button>
                </div>
            </b-tab>
            <b-tab title="Menu" @click="type = 'modal'">
                <b-row>
                    <b-colxx lg="12" xl="6">
                        <h3 class="text-center mt-4">Pages</h3>
                        <draggable class="list-group" :list="header" group="menus" @change="getHeader">
                            <b-colxx xxs="12" class="mb-3" v-for="(item, index) in header" :key="index" :id="item.id">
                                <b-card :class="{'d-flex flex-row':true,'active' : selectedMenus.includes(item.id)}" no-body>
                                    <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                        <div class="custom-control custom-checkbox pl-1 align-self-center" @click.prevent="toggleItem($event, item.id)">
                                            <b-form-checkbox :checked="selectedMenus.includes(item.id)" class="itemCheck mb-0" />
                                        </div>
                                        <div class="justify-content-between min-width-zero align-items-lg-center my-auto">
                                            <b-button variant="link">
                                                <p class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</p>
                                            </b-button>
                                        </div>
                                        <div class="my-auto ml-auto d-none d-sm-block" v-if="item.type == 'content'">
                                            <p class="list-item-heading mb-0 truncate">{{item.url}}</p>
                                        </div>
                                        <div class="my-auto ml-auto d-none d-sm-block" v-else>
                                            <p class="list-item-heading mb-0 truncate">{{item.type}}</p>
                                        </div>
                                        <div class="w-15 my-auto mx-2 d-none d-sm-block">
                                            <b-badge pill :variant="'primary'">Header</b-badge>
                                        </div>
                                        <div class="my-auto mx-md-2 ml-auto d-flex">
                                            <template v-if="item.type == 'content' || item.type == 'modal' || item.type == 'exhibit' || item.type == 'iframe'">
                                                <router-link :to="'/app/website/' + domain + '/' +item.id">
                                                    <b-button size="sm" variant="outline-primary">Setting</b-button>
                                                </router-link>
                                            </template>
                                            <template v-else-if="item.type == 'booth' || item.type == 'poster' || item.type == 'sponsor'">
                                                <router-link :to="'/app/website/' + domain + '/exhibit/' + item.type + '/' +item.hall_id">
                                                    <b-button size="sm" variant="outline-primary">Setting</b-button>
                                                </router-link>
                                            </template>
                                            <template v-else>
                                                <b-button size="sm" variant="outline-primary">Setting</b-button>
                                            </template>
                                            <b-button
                                                variant="primary"
                                                size="sm"
                                                class="ml-1"
                                                v-b-modal.add_modal
                                                @click="edit(item)"
                                            >Edit</b-button>
                                        </div>
                                    </div>
                                </b-card>
                            </b-colxx>
                        </draggable> 
                    </b-colxx>
                    <b-colxx lg="12" xl="6">
                        <h3 class="text-center mt-4">Page Assets (Non Menu Items)</h3>
                        <draggable class="list-group" :list="assets" group="menus" @change="getHeader">
                            <b-colxx xxs="12" class="mb-3" v-for="(item, index) in assets" :key="index" :id="item.id">
                                <b-card :class="{'d-flex flex-row':true,'active' : selectedMenus.includes(item.id)}" no-body>
                                    <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                        <div class="custom-control custom-checkbox pl-1 align-self-center" @click.prevent="toggleItem($event, item.id)">
                                            <b-form-checkbox :checked="selectedMenus.includes(item.id)" class="itemCheck mb-0" />
                                        </div>
                                        <div class="align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                            <b-button variant="link">
                                                <p class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</p>
                                            </b-button>
                                        </div>
                                        <div class="my-auto ml-auto">
                                            <p class="list-item-heading mb-0 truncate" v-if="item.type == 'content'">{{item.url}}</p>
                                            <p class="list-item-heading mb-0 truncate" v-else>{{item.type}}</p>
                                        </div>
                                        <div class="w-15 mx-2 my-auto ml-auto">
                                            <b-badge pill :variant="'danger'">SubPage</b-badge>
                                        </div>
                                        <div class="my-auto mx-2 d-none">
                                            <router-link :to="'/app/website/pages/'+item.id">
                                                <button type="button" class="btn btn-primary mb-1">Setting</button>
                                            </router-link>
                                            <button
                                                type="button"
                                                class="btn btn-primary mb-1"
                                                v-b-modal.add_modal
                                                @click="edit(item)"
                                            >Edit</button>
                                        </div>
                                    </div>
                                </b-card>
                            </b-colxx>
                        </draggable> 
                    </b-colxx>
                </b-row>
            </b-tab>
            <b-tab title="Menu Setting" @click="type = ''">
                <b-row class="d-none d-lg-block">
                    <b-colxx xxs="12" class="header text-center" :style="getHeaderStyle()">
                        <div class="d-inline-flex">
                            <div class="header-btn d-flex" v-for="(item, index) in header" :key="index" :style="getButtonStyle()">
                                {{item.title}}
                            </div>
                        </div>
                    </b-colxx>
                </b-row>
                <div class="my-4">
                    <b-row>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="color" class="form-control" v-model="headerStyle.bg" />
                                <span>Header Background</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="number" class="form-control" v-model="headerStyle.height" />
                                <span>Header Height</span>
                            </label>
                        </b-colxx>
                    </b-row>
                </div>
                <div class="mb-4">
                    <b-row>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <b-select class="form-control" v-model="headerStyle.btn.hasBg">
                                    <option :value="true">True</option>
                                    <option :value="false">None</option>
                                </b-select>
                                <span>Background</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="color" class="form-control" v-model="headerStyle.btn.bg" />
                                <span>Background</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="color" class="form-control" v-model="headerStyle.btn.color" />
                                <span>Text Color</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <b-select class="form-control" v-model="headerStyle.btn.boxShadow">
                                    <option :value="true">True</option>
                                    <option :value="false">None</option>
                                </b-select>
                                <span>Box Shadow</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="number" class="form-control" v-model="headerStyle.btn.fontSize" />
                                <span>Font Size</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="number" class="form-control" v-model="headerStyle.btn.radius" />
                                <span>Boder radius</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="number" class="form-control" v-model="headerStyle.btn.space" />
                                <span>Space</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="number" class="form-control" v-model="headerStyle.btn.paddingX" />
                                <span>PaddingX</span>
                            </label>
                        </b-colxx>
                        <b-colxx xs="12" md="3">
                            <label class="form-group has-float-label">
                                <input type="number" class="form-control" v-model="headerStyle.btn.paddingY" />
                                <span>PaddingY</span>
                            </label>
                        </b-colxx>
                    </b-row>
                </div>
                <button class="btn mt-4 btn-primary" @click="setHead">Save</button>
            </b-tab>
        </b-tabs>
    </b-card>

    <b-modal
        id="add_modal"
        ref="add_modal"
        :title="modalTitle"
        modal-class="modal-center"
    >
        <b-form class="text-left">
            <b-form-group :label="'Title'">
                <b-form-input v-model="newItem.title" @keyup="autoUrl()" />
            </b-form-group>
            <b-form-group :label="'Page Style'">
                <v-select v-model="modalType" @input="subListSet(modalType)" :options="types" />
            </b-form-group>
            <template v-if="newItem.type == 'booth'">
                <b-form-group label="Hall Name">
                    <b-form-select v-model="newItem.exhibit" class="p-1">
                        <option :value="hall.id" v-for="hall in boothData" :key="hall.id">{{hall.name}}</option>
                    </b-form-select>
                </b-form-group>
            </template>
            <template v-if="newItem.type == 'sponsor'">
                <b-form-group label="Hall Name">
                    <b-form-select v-model="newItem.exhibit" class="p-1">
                        <option :value="hall.id" v-for="hall in sponsorData" :key="hall.id">{{hall.name}}</option>
                    </b-form-select>
                </b-form-group>
            </template>
            <template v-if="newItem.type == 'poster'">
                <b-form-group label="Hall Name">
                    <b-form-select v-model="newItem.exhibit" class="p-1">
                        <option :value="hall.id" v-for="hall in posterData" :key="hall.id">{{hall.name}}</option>
                    </b-form-select>
                </b-form-group>
            </template>
            <template v-if="newItem.type != 'modal'">
                <label>Url</label>
                <b-input-group>
                    <b-input-group-append class="w-100">
                        <b-input-group-text>
                            {{host}}
                        </b-input-group-text>
                        <b-form-input v-model="newItem.url"/>
                    </b-input-group-append>
                </b-input-group>
            </template>
        </b-form>

        <template slot="modal-footer">
            <b-button
                variant="outline-secondary"
                @click="hideModal('add_modal')"
            >{{ $t('cancel') }}</b-button>
            <b-button variant="primary" @click="addNewItem(newItem)" class="mr-1">{{submit}}</b-button>
        </template>
    </b-modal>
    <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
        If you click confirm, you can't recover this data anymore!
        <template slot="modal-footer">
            <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
            <b-button variant="secondary" @click="hideModal('confirm_modal')">Cancel</b-button>
        </template>
    </b-modal>
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import Draggable from 'vuedraggable'

import Axios from 'axios'
import Header from '../../../containers/Web/DomainHeader'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

import VueCtkDateTimePicker from 'vue-ctk-date-time-picker'
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css'

export default {
    middleware: 'should',
    metaInfo () {
        return { title: `Vizzi Menus` }
    },
    components: {
        'b-header': Header,
        'draggable': Draggable,
        "v-select": vSelect,
        VueCtkDateTimePicker
    },
    data() {
        return {
            apiBase: '/menu/',
            menus: [],
            selectedMenus: [],
            status: ['Pages', 'Page Assets', 'Delete'],
            editData: {},
            title: '',
            host: '',
            header: [],
            assets: [],
            submit: 'Create Page',
            headerStyle: {
                bg: '#922c88',
                height: 32,
                btn: {
                    bg: '#922c88',
                    color: '#ffffff',
                    radius: 4,
                    space: 8,
                    boxShadow: false,
                    hasBg: true,
                    fontSize: 13,
                    paddingX: 2,
                    paddingY: 8
                }
            },
            buttonStyle: {},
            bgStyle: {},
            iconStyle: {},
            url: null,
            darkUrl: null,
            formData: new FormData(),
            event: '',
            logo: '',
            darkLogo: '',
            type: '',
            icons: [],
            iconStatus: [],
            modalTitle: 'Add New Page',
            newItem: {
                title: '',
                type: '',
                url: ''
            },
            editId: 0,
            types: [
                {label: "Agenda", value: "agenda"},
                {label: "Page", value: "content"},
                {label: "Modal", value: "modal"},
                {label: "Exhibit Hall", value: "exhibit"},
                {label: "Video", value: "video"},
                {label: "Booth", value: "booth"},
                {label: "Sponsor", value: "sponsor"},
                {label: "Poster", value: "poster"},
                {label: "iFrame", value: "iframe"},
                {label: "Cart", value: "cart"}
            ],
            modalType: '',
            isConfirmed: false,
            boothData: [],
            posterData: [],
            sponsorData: [],
            start: null,
            end: null,
            video: null,
            videoOption: 1,
        }
    },
    watch: {
        editData:function(val) {
            if (val.id) {
                this.modalTitle = "Edit Page"
                this.newItem = {
                    title: val.title,
                    type: val.type,
                    url: val.url.substring(1)
                }
                switch (val.type) {
                    case 'cart':
                        this.modalType = {label: "Cart", value: "cart"}
                        break

                    case 'content':
                        this.modalType = {label: "Page", value: "content"}
                        break
                    case 'modal':
                        this.modalType = {label: "Modal", value: "modal"}
                        break
                    case 'exhibit':
                        this.modalType = {label: "Exhibit Hall", value: "exhibit"}
                        break
                    case 'video':
                        this.modalType = {label: "Video", value: "video"}
                        break
                    case 'booth':
                        this.modalType = {label: "Booth", value: "booth"}
                        break
                    case 'sponsor':
                        this.modalType = {label: "Sponsor", value: "sponsor"}
                        break
                    case 'poster':
                        this.modalType = {label: "Poster", value: "poster"}
                        break
                    case 'iframe':
                        this.modalType = {label: "iFrame", value: "iframe"}
                        break
                    default :
                        this.modalType = {label: "Agenda", value: "agenda"}
                }
                this.editId = val.id
            } else {
                this.modalTitle = "Add New Page"
                this.editId = 0
            }
        },
        videoOption(val) {
            if (val == 0 && this.video && typeof this.video === 'object') {
                this.video = ''
            }
            if (val == 1 && this.video && typeof this.video === 'object') {
                this.video = null
            }
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            domain: 'site/getDomain'
        }),
        isSelectedAll() {
            return this.selectedMenus.length >= this.menus.length;
        },
        isAnyItemSelected() {
            return (
                this.selectedMenus.length > 0 &&
                this.selectedMenus.length < this.menus.length
            );
        },
    },
    methods: {
        loadItems() {
            Axios.post(this.apiBase + 'get', {id: this.siteId}).then(res => {
                this.menus = res.data.menus
                this.assets = res.data.resource
                this.header = res.data.header
                for (let i = 0; i < this.header.length; i++) {
                    this.icons[i] = null
                }
                this.selectedMenus = []
                this.event = res.data.domain.title
                this.logo = res.data.domain.logo

                this.start = res.data.domain.start
                this.end = res.data.domain.end
                this.video = res.data.domain.video
                if (this.video) {
                    if (this.validURL(this.video)) {
                        this.videoOption = 0
                    } else {
                        this.videoOption = 1
                        this.video = res.data.domain.video
                    }
                }
                this.darkLogo = res.data.domain.darkLogo
                if (res.data.domain.style) {
                    this.headerStyle = JSON.parse(res.data.domain.style)
                }
                this.posterData = res.data.posterData
                this.sponsorData = res.data.sponsorData
                this.boothData = res.data.boothData
            })
        },
        hideModal(refname) {
            this.$refs[refname].hide();
        },
        subListSet(value) {
            this.newItem.type = value.value
        },
        confirm() {
            this.isConfirmed = true
            this.setStatus(2)
        },
        setStatus(status) {
            if (status == 2 && !this.isConfirmed) {
                this.$refs['confirm_modal'].show()
            } else {
                Axios.put(this.apiBase + 'status', {status: status, items: this.selectedMenus, id: this.siteId}).then(res => {
                    if (res.statusText == 'OK') {
                        if (status != 2) {
                            var text = 'Successfully Saved!'
                        } else {
                            var text = 'Successfully Deleted!'
                        }
                        this.$notify('primary filled', '', text, { duration: 3000, permanent: false })
                        this.menus = res.data.menus
                        this.assets = res.data.resource
                        this.header = res.data.header
                        this.selectedMenus = []
                        this.isConfirmed = false
                        this.hideModal('confirm_modal')
                    } else {
                        this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                    }
                })
            }

        },
        addNewItem(item) {
            item.editId = this.editId
            item.id = this.siteId
            Axios.post(this.apiBase + 'set', item).then(res => {
                if (res.data.resource && res.data.header) {
                    this.$notify('primary filled', 'You\'ve Successfully Created a New Page', '', { duration: 3000, permanent: false })
                    this.menus = res.data.menus
                    this.assets = res.data.resource
                    this.header = res.data.header
                    this.selectedMenus = []
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                }
                this.$refs['add_modal'].hide();
            })
        },
        autoUrl() {
            let str = this.newItem.title.replace(/^\s+|\s+$/g, '')
            str = str.toLowerCase()
            const from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            const to   = "aaaaeeeeiiiioooouuuunc------";
            for (let i=0, l=from.length ; i<l ; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }
            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            this.newItem.url = str
        },
        setHead() {
            Axios.post(this.apiBase + 'head', {style: this.headerStyle, id: this.siteId}).then(res => {
                if (res.statusText == 'OK') {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                    this.$store.dispatch('site/saveSite', res.data )
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                }
            })
        },
        selectAll(isToggle) {
            if (this.selectedMenus.length >= this.menus.length) {
                if (isToggle) this.selectedMenus = [];
            } else {
                this.selectedMenus = this.menus.map(x => x.id);
            }
        }, 
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                break;
                case "undo":
                    this.selectedMenus = [];
                break;
            }
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedMenus.length > 0) {
                let itemsForToggle = this.menus;
                var start = this.getIndex(itemId, itemsForToggle, "id");
                var end = this.getIndex(
                    this.selectedMenus[this.selectedMenus.length - 1],
                    itemsForToggle,
                    "id"
                );
                itemsForToggle = itemsForToggle.slice(
                    Math.min(start, end),
                    Math.max(start, end) + 1
                );
                this.selectedMenus.push(
                    ...itemsForToggle.map(item => {
                        return item.id;
                    })
                );
            } else {
                if (this.selectedMenus.includes(itemId)) {
                    this.selectedMenus = this.selectedMenus.filter(x => x !== itemId);
                } else this.selectedMenus.push(itemId);
            }
        },
        edit(item) {
            this.editData = item
            this.submit = 'Update Page'
        },
        initData() {
            this.editData = {}
            this.submit = 'Create Page'
        },
        getHeader() {
            let headerArray = []
            let sourceArray = []
            this.header.forEach(menu => {
                headerArray.push(menu.id)
            })
            this.assets.forEach(menu => {
                sourceArray.push(menu.id)
            })
            Axios.put(this.apiBase + 'order', {header: headerArray, source: sourceArray})
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        },
        getHost() {
            this.title = this.capitalizeFirstLetter(this.domain) + ' Page Manager'
            this.host = window.location.host + '/'
        },
        getHeaderStyle() {
            return {
                'background-color': this.headerStyle.bg,
                'height': this.headerStyle.height + 'px'
            }
        },
        getButtonStyle() {
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
        getIconStyle() {
            this.iconStyle = {
                'width': this.headerStyle.btn.fontSize * 1.5 + 'px',
                'height': this.headerStyle.btn.fontSize * 1.5 + 'px'
            }
            return {
                'width': this.headerStyle.btn.fontSize * 1.5 + 'px',
                'height': this.headerStyle.btn.fontSize * 1.5 + 'px'
            }
        },
        onFileChange(e) {
            const file = e.target.files[0]
            if (file.size / 10000 > 5) {
                this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
            }
            this.url = URL.createObjectURL(file)
            this.formData.set('file', file)
        },
        onVideoChange(e) {
            var file = e.target.files[0]
            var extension = file.name.split('.').pop()
            if (extension.toLowerCase() != 'mp4') {
                this.$notify('primary filled', '', 'Please Insert Valid Video Url!', { duration: 3000, permanent: false })
            } else {
                if (file.size / 1000 > 1) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
                }
                this.video = file
            }
        },
        onDarkChange(e) {
            const file = e.target.files[0]
            if (file.size / 10000 > 5) {
                this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
            }
            this.darkUrl = URL.createObjectURL(file)
            this.formData.set('file1', file)
        },
        setting() {
            if (!this.start || !this.end) {
                this.$notify('primary filled', '', 'Please Insert Event Time', { duration: 3000, permanent: false })
            } else {
                this.formData.append('title', this.event)
                this.formData.append('id', this.siteId)
                this.formData.append('start', this.start)
                this.formData.append('end', this.end)
                if (this.videoOption == 1 && this.video) {
                    this.formData.append('video', this.video)
                }
                var flag = true
                if (this.videoOption == 0 && this.video != '') {
                    if (this.validURL(this.video)) {
                        this.formData.append('video', this.video)
                    } else {
                        flag = false
                        this.$notify('primary filled', '', 'Please Insert Valid Video Url!', { duration: 3000, permanent: false })
                    }
                }
                if (flag) {
                    Axios.post(this.apiBase + 'title', this.formData).then(res => {
                        if (res.statusText == 'OK') {
                            this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                            this.$store.dispatch('site/saveSite', res.data)
                        }
                    })
                }
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
        uploadIcon(e, index) {
            const file = e.target.files[0]
            const fileData = URL.createObjectURL(file)
            this.setIcon(fileData, index)
        },
        setIcon(data, index) {
            this.icons[index] = data
        }
    },
    mounted() {
        this.getHost()
        this.loadItems()
    }
}
</script>

<style lang="css">
    .header {
        min-width: 40px;
    }
    .header-btn {
        font-weight: 400;
        min-width: 64px;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        line-height: 1.75;
        letter-spacing: 0.02857em;
        text-transform: capitalize;
        justify-content: center;
    }
    .custom-select {
        height: 36px!important;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .input-group-text {
        border-right: 0;
        background: #f4f6f8;
        padding: 4px;
        font-size: 14px;
    }
</style>