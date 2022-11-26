<template>

<div>
    <b-row>
        <b-header
            title="Booth"
            :selectAll="selectAll"
            :isSelectedAll="isSelectedAll"
            :keymap="keymap"
            :isAnyItemSelected="isAnyItemSelected"
            :setStatus="setStatus"
            :initData="initData"
            :status="status"
            :type="type"
        />
    </b-row>

    <div class="arrow-btn" @click="returnTab" v-if="type && type != 'upload'">
        Return
    </div>
    <div class="arrow-btn" style="right: 3.6vw" @click="nextTab" v-if="type && type != 'publish'">
        Next
    </div>

    <b-tabs v-model="tabIndex">
        <b-tab title="Upload" @click="type = 'upload'">
            <template v-if="isUpload">
                <b-row>
                    <b-colxx xxs="12"></b-colxx>
                    <b-colxx lg="3" md="12">
                        <b-card no-body v-for="(item, key) in data" :key="key" class="mb-3">
                            <b-badge variant="primary" pill class="position-absolute badge-top-left">{{key + 1}}</b-badge>
                            <b-card-body>
                                <i class="simple-icon-close float-right del-btn" @click="delData(key)"></i>
                                <h4>Option {{key + 1}}</h4>
                                <h6 class="text-muted">Upload Your Design</h6>
                                <b-form-group class="my-2">
                                    <b-form-radio-group v-model="isOnline[key]">
                                        <b-form-radio :value="false">Upload</b-form-radio>
                                        <b-form-radio :value="true">Online</b-form-radio>
                                    </b-form-radio-group>
                                </b-form-group>
                                <div class="text-center">
                                    <b-button variant="primary" block size="sm" class="mb-2" @click="isSelect[key] = true, currentIndex = key">Select the Area</b-button>
                                </div>
                                <div class="text-center" v-if="isOnline[key] == false">
                                    <label for="media" class="btn btn-sm btn-primary btn-block" @click="currentIndex = key">Upload Media</label>
                                </div>
                                <b-form-input 
                                    v-model="item.url" placeholder="Insert Url" 
                                    @change="url = null, isSelect[key] = true, currentIndex = key, urls[key] = null" class="mb-2" 
                                    v-if="isOnline[key] == true" 
                                />
                            </b-card-body>
                        </b-card>
                        <div class="d-flex justify-content-end align-items-center">
                            <b-button variant="primary" size="sm" @click="addOption">
                                <i class="simple-icon-plus mr-2"></i>Add Option
                            </b-button>
                            <b-button variant="secondary" size="sm" class="ml-auto" @click="setBooth">Save</b-button>
                        </div>
                    </b-colxx>
                    <b-colxx lg="9" md="12">
                        <b-card class="text-center position-fixed w-60">
                            <div class="w-75 mx-auto">
                                <div class="w-100 position-relative">
                                    <img :src="'/data/' + temp.media" alt="Booth" class="w-100" v-if="temp && temp.media" id="content" @click="getArea" />
                                    <img :src="design" alt="Booth" class="w-100" v-else id="content" @click="getArea" />
                                    <div
                                        class="position-absolute area"
                                        :style="areaStyle(item)"
                                        v-for="(item, key) in data"
                                        :key="key"
                                        @click="currentIndex = key"
                                    >
                                        <template v-if="item.url">
                                            <img :src="item.url" class="w-100 h-100" style="object-fit: cover" v-if="item.url.substring(0, 4) == 'http'" />
                                            <img :src="'/data/'+item.url" class="w-100 h-100" style="object-fit: cover" v-else />
                                        </template>
                                        <img :src="urls[key]" class="w-100 h-100" style="object-fit: cover" v-if="urls[key]" />
                                    </div>
                                </div>
                            </div>
                            <input id="media" type="file" @change="onFileChange" onclick="this.value = null" style="display:none" />
                        </b-card>
                    </b-colxx>
                </b-row>
            </template>
            <template v-else>
                <b-row>
                    <b-colxx xxs="12">
                        <b-card class="text-center">
                            <div class="w-75 mx-auto">
                                <div class="d-flex mx-auto" style="width: 360px">
                                    <b-form-group label="Title" class="has-float-label w-100">
                                        <b-form-input v-model="booth.title" />
                                    </b-form-group>
                                    <b-button variant="primary" class="ml-2 mb-4" @click="setTitle(booth.id)">Update</b-button>
                                </div>
                                <img :src="'/data/' + temp.media" alt="Booth" class="w-100" v-if="temp && temp.media" />
                                <img :src="design" alt="Booth" class="w-100" v-else-if="design" />
                            </div>
                            <div class="d-flex justify-content-end align-items-center mt-4">
                                <button class="btn btn-outline-primary ml-auto mr-2" @click="choose">Choose another booth</button>
                                <button class="btn btn-primary ml-2 mr-auto" @click="isUpload = true" v-if="temp && temp.type == 0">Upload Booth Images</button>
                                <template v-else>
                                    <button class="btn btn-primary ml-2 mr-auto" @click="isUpload = true" v-if="design || temp">Upload Booth Images</button>
                                    <label for="file" class="btn btn-primary ml-2 mr-auto">Upload Custom Design</label>
                                </template>
                            </div>
                        </b-card>
                    </b-colxx>
                </b-row>
            </template>
            <input type="file" id="file" @change="customUpload" onclick="this.value = null" class="d-none" />
        </b-tab>
        <b-tab title="Booth Menu" @click="type = 'modal'" class="pb-5">
            <b-row>
                <b-colxx xs="12" md="6">
                    <h3 class="text-center mt-4">Pages</h3>
                    <draggable class="list-group" :list="header" group="menus" @change="getHeader">
                        <b-colxx xxs="12" class="mb-3" v-for="(item, index) in header" :key="index" :id="item.id">
                            <b-card :class="{'d-flex flex-row':true,'active' : selectedMenus.includes(item.id)}" no-body>
                                <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                    <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event, item.id)">
                                        <b-form-checkbox :checked="selectedMenus.includes(item.id)" class="itemCheck mb-0" />
                                    </div>
                                    <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                        <b-button variant="link">
                                            <p class="list-item-heading mb-0 truncate text-capitalize">{{item.title}}</p>
                                        </b-button>
                                        <div class="w-15 w-sm-100">
                                            <b-badge pill :variant="'primary'">Header</b-badge>
                                        </div>
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
                                    <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event, item.id)">
                                        <b-form-checkbox :checked="selectedMenus.includes(item.id)" class="itemCheck mb-0" />
                                    </div>
                                    <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                        <b-button variant="link">
                                            <p class="list-item-heading mb-0 truncate text-capitalize">{{item.title}}</p>
                                        </b-button>
                                        <div class="w-15 w-sm-100">
                                            <b-badge pill :variant="'danger'">Inactive</b-badge>
                                        </div>
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
        </b-tab>
        <b-tab title="Content" @click="type = 'content'" class="pb-5">
            <div class="wizard-basic-step mt-2">
                <b-tabs v-model="tabMenu">
                    <b-tab v-for="(menu, index) in header" :key="menu.id" :title="menu.title" @click="getTab(menu.id, index)">
                        <b-card no-body class="pt-3">
                            <b-tabs pills card v-model="tabModel">
                                <b-tab v-for="tab in tabs[tabMenu]" :key="tab.id">
                                    <template v-slot:title>
                                        {{capitalizeFirstLetter(tab.title)}}
                                    </template>
                                    <div class="d-flex justify-content-end align-items-center mb-2">
                                        <div class="my-auto mr-2" style="font-weight: bold">Tab Title</div>
                                        <b-form-input v-model="tab.title" style="width: 120px" class="mr-2" />
                                        <b-button variant="primary" size="sm" class="mr-auto" @click="updateTitle(tab.title)">Update</b-button>
                                        <b-button size="sm" variant="danger" class="ml-auto" @click="closeTab()" v-if="tab.length != 1">
                                            Close tab
                                        </b-button>
                                    </div>
                                    <b-quill-editor
                                        :onEditorChange="onEditorChange"
                                    />
                                    <b-row>
                                        <b-colxx xxs="12">
                                            <b-card class="d-flex flex-row mb-3" no-body v-for="(item, index) in items" :key="index" :id="item.id">
                                                <div class="custom-control custom-checkbox pl-1 align-self-center pr-4">
                                                    <div class="custom-control custom-checkbox mb-0" @click.prevent="toggleItem($event, item.id)">
                                                        <b-form-checkbox :checked="selectedItems.includes(item.id)" />
                                                    </div>
                                                </div>
                                                <div class="position-relative">
                                                    <div class="position-absolute type-text" :style="getStyle(item.item)">{{getTypeText(item.item)}}</div>
                                                    <img class="list-thumbnail responsive border-0" :src="getType(item.item)" />
                                                </div>
                                                <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                                    <div
                                                        class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center pl-1"
                                                    >
                                                        <p class="list-item-heading mb-1 truncate text-capitalize">{{item.title}}</p>
                                                        <div class="w-15 w-sm-100">
                                                            <b-badge variant="primary" pill v-if="item.status == 1">Active</b-badge>
                                                            <b-badge variant="danger" pill v-else>Inactive</b-badge>
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
                                                            v-b-modal.asset_modal
                                                            @click="edit(item)"
                                                        >Edit</button>
                                                    </div>
                                                </div>
                                            </b-card>
                                        </b-colxx>
                                    </b-row>
                                </b-tab>
                                <template v-slot:tabs-end>
                                    <b-nav-item role="presentation" @click.prevent="addTab(index)" href="#"><b>+</b></b-nav-item>
                                </template>
                            </b-tabs>
                        </b-card>
                    </b-tab>
                <button class="btn btn-primary mt-2 mb-5" @click="setContent">Save</button>
                </b-tabs>
            </div>
        </b-tab>
        <b-tab title="Booth Menu Settings" @click="type = 'settings'" class="pb-5">
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
            <div class="mb-2">
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

            <button class="btn btn-primary" @click="setHead">Save</button>
        </b-tab>
        <b-tab title="Publish" @click="type = 'publish'" class="pb-5">
            <div class="w-75 mx-auto">
                <div class="w-100 position-relative">
                    <img :src="'/data/' + temp.media" alt="Booth" class="w-100" v-if="temp && temp.media" id="content" @click="getArea" />
                    <img :src="design" alt="Booth" class="w-100" v-else id="content" />
                    <div
                        class="position-absolute"
                        :style="itemStyle(item)"
                        v-for="(item, key) in data"
                        :key="key"
                    >
                        <template v-if="item.url">
                            <img :src="item.url" class="w-100 h-100" style="object-fit: cover" v-if="item.url.substring(0, 4) == 'http'" />
                            <img :src="'/data/'+item.url" class="w-100 h-100" style="object-fit: cover" v-else />
                        </template>
                        <img :src="urls[key]" class="w-100 h-100" style="object-fit: cover" v-if="urls[key]" />
                    </div>
                </div>
                <button class="btn btn-primary mt-2" @click="publish" v-if="(temp && temp.media) || design">Publish</button>
            </div>
        </b-tab>
    </b-tabs>  

    <b-modal
        id="add_modal"
        ref="add_modal"
        :title="titleText"
        modal-class="modal-center"
    >
        <b-form>
            <b-form-group label="Title">
                <b-form-input v-model="menuTitle" />
            </b-form-group>
        </b-form>
        <template slot="modal-footer">
            <b-button
                variant="outline-secondary"
                @click="hideModal('add_modal')"
            >{{ $t('cancel') }}</b-button>
            <b-button
                variant="primary"
                @click="addNewItem(menuTitle, editId)"
                class="mr-1">
                {{submitText}}
            </b-button>
        </template>
    </b-modal>

    <b-modal
        id="asset_modal"
        ref="asset_modal"
        :title="assetTitle"
        modal-class="modal-center"
    >
        <b-form>
            <b-form-group label="Title">
                <b-form-input v-model="assetItem.title" />
            </b-form-group>
            <b-form-group label="File">
                <b-input-group :prepend="$t('input-groups.upload')">
                    <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onAssetFileUpload" />
                </b-input-group>
            </b-form-group>
        </b-form> 

        <template slot="modal-footer">
            <b-button
                variant="outline-secondary"
                @click="$refs.asset_modal.hide(), assetItem = {}"
            >{{ $t('cancel') }}</b-button>
            <b-button variant="primary" @click="addNewAssetItem(assetItem)" class="mr-1">{{ $t('submit') }}</b-button>
        </template>
    </b-modal>

    <b-modal id="view_modal" size="lg" title="Asset Preview" centered hide-footer>
        <video v-if="extension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
            <source :src="'/data/'+viewItem.item" type="video/mp4">
        </video>

        <iframe class="w-100" :src="'/data/'+viewItem.item" v-else-if="extension === 'pdf'" style="height: 75vh" />

        <img class="w-100" :src="'/data/'+viewItem.item" v-else />
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
import Axios from 'axios'
import exhibitMixins from '../exhibitMixins'

export default {
    metaInfo () {
        return { title: `Booth Edit` }
    },
    mixins: [exhibitMixins],
    data() {
        return {
            apiBase: '/wizard/booth/',
            booth: [],
            title: '',
            type: 'upload'
        }
    },
    mounted() {
        this.id = this.$route.params.id
        this.init()
    },
    methods: {
        init() {
            Axios.post(this.apiBase + 'getItem', {id: this.id}).then(res => {
                if (res.status == 200) {
                    this.booth = res.data.page
                    if (this.booth.data) {
                        this.data = JSON.parse(this.booth.data)
                    }
                    this.data.forEach((item, index) => {
                        this.isSelect[index] = true
                        if (item.url && item.url.substring(0, 4) == 'http') {
                            this.isOnline[index] = true
                        } else {
                            this.isOnline[index] = false
                        }
                    });

                    this.menus = res.data.resource
                    this.header = this.booth.header
                    this.temp = this.booth.template
                    if (this.booth.header_style) {
                        this.headerStyle = JSON.parse(this.booth.header_style)
                    }
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        onFileChange(e) {
            this.data[this.currentIndex].url = null
            const file = e.target.files[0]
            e.target.value = ''
            this.extension = file.name.split('.').pop();
            if (this.extension != 'mp4') {
                if (file.size / 10000 > 5) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
                }
            } else {
                if (file.size / 100000 > 1) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
                }
            }
            this.url = URL.createObjectURL(file)
            this.urls[this.currentIndex] = this.url
            this.formData.set('media'+this.currentIndex, file)
            var data = this.data
            this.data = []
            this.data = data
        },
        edit(item) {
            if (this.type == 'content') {
                this.assetItem = item
            } else {
                this.editData = item
            }
        },
        confirm() {
            this.isConfirmed = true
            if (this.isDelStatus == 2) {
                this.closeTab()
            } else {
                this.setStatus(2)
            }
        },
    }
}
</script>

<style lang="css">
    .area {
        background: rgba(0, 0, 0, .4);
    }
    .img-area {
        background: transparent;
    }
    .img-area button {
        display: none;
    }
    .img-area:hover {
        background: rgba(0, 0, 0, .4);
    }
    .img-area:hover button {
        display: block;
    }
    .align-center {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

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
    .del-btn {
        font-size: 1rem;
        color: #922c88;
        cursor: pointer;
        font-weight: 900;
    }
    .nav-pills .nav-link {
        border-radius: 8px;
    }
    .arrow-btn {
        bottom: 30px;
        z-index: 1;
        background: rgba(146, 44, 136, .8);
        padding: 8px;
        transform: rotate(0);
        border-radius: 8px;
        color: white;
        font-weight: 900;
        cursor: pointer;
        width: 60px;
        text-align: center;
        position: fixed;
    }
    .arrow-btn:hover {
        background: gray;
    }
    .type-text {
        padding: 2px 9px;
        top: 19px;
        font-size: 12px;
        border-radius: 0 40px 40px 0;
        text-transform: uppercase;
        font-weight: bold;
        color: white
    }
</style>