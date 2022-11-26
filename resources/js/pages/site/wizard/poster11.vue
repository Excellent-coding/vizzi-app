<template>
<div>
    <b-row>
        <b-header
            title="Poster"
            :selectAll="selectAll"
            :isSelectedAll="isSelectedAll"
            :keymap="keymap"
            :isAnyItemSelected="isAnyItemSelected"
            :setStatus="setStatus"
            :initData="initData"
            :status="status"
            :type="modalType"
        />
    </b-row>

    <b-tabs>
        <b-tab title="Upload" @click="type = 'upload'">
            <b-row>
                <b-colxx xxs="12">
                    <b-card class="d-flex flex-row mb-3" no-body v-for="(item, index) in items" :key="index" :id="item.id">
                        <img class="list-thumbnail responsive border-0" :src="getType(item.media)">
                        <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                            <div
                                class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center"
                            >
                                <p class="list-item-heading mb-1 truncate">{{item.title}}</p>
                                <div class="w-15 w-sm-100">
                                    <b-badge variant="primary" pill v-if="item.status == 1">Active</b-badge>
                                    <b-badge variant="danger" pill v-else>Inactive</b-badge>
                                </div>
                            </div>
                            <div class="custom-control custom-checkbox pl-1 align-self-center pr-4">
                                <div class="custom-control custom-checkbox mb-0" @click.prevent="toggleItem($event, item.id)">
                                    <b-form-checkbox :checked="selectedItems.includes(item.id)" />
                                </div>
                            </div>
                            <div class="my-auto mr-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary mb-1"
                                    v-b-modal.view_modal
                                    @click="view(item)"
                                >View</button>
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
            </b-row>
        </b-tab>
        <b-tab title="Header" @click="type = 'modal'">
            <b-card>
                <b-row>
                    <b-form-group>
                        <b-form-radio-group>
                            <b-form-radio v-model="setting" value="menus" :checked="true">Menus</b-form-radio>
                            <b-form-radio v-model="setting" value="setting">Setting</b-form-radio>
                        </b-form-radio-group>
                    </b-form-group>
                </b-row>

                <template v-if="setting == 'menus'">
                    <b-row>
                        <b-colxx xs="12" md="6">
                            <h3 class="text-center mt-4">Pages</h3>
                            <draggable class="list-group" :list="header" group="menus" @change="getHeader">
                                <b-colxx xxs="12" class="mb-3" v-for="(item, index) in header" :key="index" :id="item.id">
                                    <b-card :class="{'d-flex flex-row':true,'active' : selectedMenus.includes(item.id)}" no-body>
                                        <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                            <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                                <b-button variant="link">
                                                    <p class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</p>
                                                </b-button>
                                                <div class="w-15 w-sm-100">
                                                    <b-badge pill :variant="'primary'">Header</b-badge>
                                                </div>
                                            </div>
                                            <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event, item.id)">
                                                <b-form-checkbox :checked="selectedMenus.includes(item.id)" class="itemCheck mb-0" />
                                            </div>
                                            <div class="my-auto mr-2">
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
                        <b-colxx xs="12" md="6">
                            <h3 class="text-center mt-4">Page Assets (Non Menu Items)</h3>
                            <draggable class="list-group" :list="menus" group="menus" @change="getHeader">
                                <b-colxx xxs="12" class="mb-3" v-for="(item, index) in menus" :key="index" :id="item.id">
                                    <b-card :class="{'d-flex flex-row':true,'active' : selectedMenus.includes(item.id)}" no-body>
                                        <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                            <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                                <b-button variant="link">
                                                    <p class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</p>
                                                </b-button>
                                                <div class="w-15 w-sm-100">
                                                    <b-badge pill :variant="'danger'">Inactive</b-badge>
                                                </div>
                                            </div>
                                            <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event, item.id)">
                                                <b-form-checkbox :checked="selectedMenus.includes(item.id)" class="itemCheck mb-0" />
                                            </div>
                                            <div class="my-auto mr-2">
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
                </template>
                <template v-else>
                    <b-row>
                        <b-col class="header text-center w-100" :style="getHeaderStyle()">
                            <div class="d-inline-flex">
                                <div class="header-btn" v-for="(item, index) in header" :key="index" :style="getButtonStyle()">{{item.title}}</div>
                            </div>
                        </b-col>
                    </b-row>

                    <div class="my-4">
                        <h6>Background</h6>
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
                        <h6>Button</h6>
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
                </template>
            </b-card>
        </b-tab>
        <b-tab title="Content" @click="type = 'content'">
            <b-card>
                <div class="wizard-basic-step">
                    <b-tabs content-class="my-3">
                        <b-tab v-for="item in header" :key="item.id" :title="item.title" @click="getTab(item.id)">
                            <b-quill-editor
                                :onEditorChange="onEditorChange"
                            />
                        </b-tab> 
                    </b-tabs>
                </div>
                <button class="btn mt-4 btn-primary" @click="setContent">Save</button>
            </b-card>
        </b-tab>
    </b-tabs>

    <b-add-modal :addNewItem="addNewItem" :editData="editData" v-if="type == 'upload'" />

    <b-modal id="view_modal" size="lg" :title="viewItem.title" centered hide-footer>
        <video v-if="extension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
            <source :src="'/data/'+viewItem.media" type="video/mp4">
        </video>

        <iframe class="w-100" :src="'/data/'+viewItem.media" v-else-if="extension === 'pdf'" style="height: 75vh" />

        <img class="w-100" :src="'/data/'+viewItem.media" v-else />
    </b-modal>

    <b-asset-modal
        :addNewItem="addNewItem"
        :editData="editData"
        v-if="type == 'modal'"
    />
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from "axios"
import Draggable from 'vuedraggable'

import Header from "../../../containers/Web/DomainHeader"
import addModal from '../../../components/Pages/AddNewItemModal'
import AddAssetModal from '../../../components/Wizard/AddAssetModal'
import QuillEditor from '../../../components/Pages/QuillEditor'

export default {
    middleware: 'may',
    components: {
        'b-header': Header,
        "b-add-modal": addModal,
        'draggable': Draggable,
        'b-asset-modal': AddAssetModal,
        "b-quill-editor" : QuillEditor
    },
    data() {
        return {
            items: [],
            selectedItems: [],
            status: ['Active', 'InActive', 'Delete'],
            editData: {},
            extension: '',
            apiBase: 'wizard/poster/',
            viewItem: {},

            header: [],
            menus: [],
            selectedMenus: [],
            editAssetData: {},
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
            submitText: 'Create Page',
            type: 'upload',
            setting: 'menus',
            editorId: null,
            modalType: 'modal'
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        }),
        isSelectedAll() {
            if (this.type == 'upload') {
                return this.selectedItems.length >= this.items.length
            } else {
                return this.selectedMenus.length >= this.menus.length;
            }
        },
        isAnyItemSelected() {
            if (this.type == 'upload') {
                return (
                    this.selectedItems.length > 0 &&
                    this.selectedItems.length < this.items.length
                )
            } else {
                return (
                    this.selectedMenus.length > 0 &&
                    this.selectedMenus.length < this.menus.length
                )
            }
        }
    },
    methods: {
        loadItems() {
            axios.post(this.apiBase + 'get', {id: this.siteId}).then(res => {
                this.items = res.data.source
                this.menus = res.data.resource
                this.header = res.data.header
                if (res.data.style) {
                    this.headerStyle = JSON.parse(res.data.style)
                }
            })
        },
        view(item) {
            this.viewItem = item
            this.extension = this.viewItem.media.split('.').pop()
        },
        addNewItem(item) {
            if (this.type == 'upload') {
                let onAdd = true
                if (item.title == '') {
                    this.$notify('primary filled', 'Warn!', 'Please insert file title', { duration: 3000, permanent: false })
                    onAdd = false            }
                if (!item.file) {
                    this.$notify('primary filled', 'Warn!', 'Please insert file', { duration: 3000, permanent: false })
                    onAdd = false
                }

                if (onAdd) {
                    let formData = new FormData()
                    formData.append('title', item.title)
                    formData.append('file', item.file)
                    formData.append('editId', item.id)
                    formData.append('siteId', this.siteId)
                    axios.post(this.apiBase + "upload", formData).then(res => {
                        if (res.statusText == 'OK') {
                            this.items = res.data
                        } else {
                            this.$notify('primary filled', 'Sorry!', 'Data is not correct!', { duration: 3000, permanent: false })
                        }
                    })
                }
            } else {
                axios.post(this.apiBase + 'setHeader', {title: item, id: this.siteId, editId: this.editId}).then(res => {
                    if (res.data.resource && res.data.header) {
                        this.menus = res.data.resource
                        this.header = res.data.header
                        this.selectedMenus = []
                    } else {
                        this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                    }
                })
            }
        },
        setStatus(value) {
            if (this.type == 'upload') {
                axios.post(this.apiBase + "status", {items: this.selectedItems, status: value, id: this.siteId}).then(res => {
                    this.items = res.data
                })
            } else {
                axios.put(this.apiBase + 'menuStatus', {status: value, items: this.selectedMenus, id: this.siteId}).then(res => {
                    this.menus = res.data.resource
                    this.header = res.data.header
                    this.selectedMenus = []
                })
            }

        },

        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                break;
                case "undo":
                    if (this.type == 'upload') {
                        this.selectedItems = [];
                    } else {
                        this.selectedMenus = [];
                    }
                break;
            }
        },
        initData() {
            this.editData = {}
        },
        edit(item) {
            this.editData = item
        },
        getType(file) {
            const extension = file.split('.').pop()
            if (extension == 'mp4') {
                return '/assets/img/mp4.png'
            } else if (extension == 'pdf') {
                return '/assets/img/pdf.png'
            } else {
                return '/assets/img/pic.png'
            }
        },
        toggleItem(event, itemId) {
            if (this.type == 'upload') {
                if (event.shiftKey && this.selectedItems.length > 0) {
                    let itemsForToggle = this.items;
                    var start = this.getIndex(itemId, itemsForToggle, "id");
                    var end = this.getIndex(
                        this.selectedItems[this.selectedItems.length - 1],
                        itemsForToggle,
                        "id"
                    );
                    itemsForToggle = itemsForToggle.slice(
                        Math.min(start, end),
                        Math.max(start, end) + 1
                    );
                    this.selectedItems.push(
                        ...itemsForToggle.map(item => {
                        return item.id;
                    })
                    );
                } else {
                    if (this.selectedItems.includes(itemId)) {
                        this.selectedItems = this.selectedItems.filter(x => x !== itemId);
                    } else this.selectedItems.push(itemId);
                }
            } else {
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
            }
        },
        selectAll(isToggle) {
            if (this.type == 'upload') {
                if (this.selectedItems.length >= this.items.length) {
                    if (isToggle) this.selectedItems = [];
                } else {
                    this.selectedItems = this.items.map(x => x.id);
                }
            } else {
                if (this.selectedMenus.length >= this.menus.length) {
                    if (isToggle) this.selectedMenus = [];
                } else {
                    this.selectedMenus = this.menus.map(x => x.id);
                }
            }

        },

        onEditorChange({ editor, html, text }) {
            this.header.forEach(menu => {
                if (this.editorId ==  menu.id) {
                    menu.contentData = html
                }
            })
        },
        getHeader() {
            let headerArray = []
            let sourceArray = []
            this.header.forEach(menu => {
                headerArray.push(menu.id)
            })
            this.menus.forEach(menu => {
                sourceArray.push(menu.id)
            })
            axios.put(this.apiBase + 'order', {header: headerArray, source: sourceArray})
        },
        setHead() {
            axios.put(this.apiBase + 'head', {style: this.headerStyle, id: this.siteId})
        },

        setContent() {
            axios.put(this.apiBase + 'content', {data: this.header})
        },

        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
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

        getTab(id) {
            this.editorId = id;
        },
    },
    watch: {
        header(val) {
            if (val.length) {
                this.editorId = val[0].id
            }
        },
        type(val) {
            if (val != 'content') {
                this.modalType = 'modal'
            }
        }
    },
    mounted() {
        this.loadItems()
    }
}
</script>

<style lang="css">
    .header {
        min-width: 40px;
    }
    .header .header-btn {
        padding: 13px 10px;
        font-weight: 400;
        min-width: 64px;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        line-height: 1.75;
        letter-spacing: 0.02857em;
        text-transform: capitalize;
        box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12);
    }
</style>