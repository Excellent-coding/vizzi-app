<template>
    <div>
        <div v-if="!isPreview">
            <b-row>
                <b-header
                    title="Sponsor"
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
            <div class="arrow-btn" style="right: 50px" @click="nextTab" v-if="type && type != 'publish'">
                Next
            </div>
            <div class="arrow-btn" style="right: 3.6vw" @click="isPreview = true" v-if="type && type == 'publish'">
                Preview
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
                                        <b-form-input
                                            v-model="item.link" placeholder="Hyperlink"
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
                                            <img :src="'/data/' + temp.media" alt="Sponsor" class="w-100" v-if="temp && temp.media" id="content" @click="getArea" />
                                            <img :src="design" alt="Sponsor" class="w-100" v-else id="content" @click="getArea" />
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
                                    <input id="media" type="file" @change="onFileChange" class="d-none" onclick="this.value = null" style="display:none" />
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
                                        <img :src="'/data/' + temp.media" alt="Sponsor" class="w-100" v-if="temp && temp.media" />
                                        <img :src="design" alt="Sponsor" class="w-100" v-else-if="design" />
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center mt-4">
                                        <button class="btn btn-outline-primary ml-auto mr-2" @click="choose">Choose another Sponsor</button>
                                        <button class="btn btn-primary ml-2 mr-auto" @click="isUpload = true" v-if="temp && temp.type == 1">Upload Sponsor Images</button>
                                        <template v-else>
                                            <button class="btn btn-primary ml-2 mr-auto" @click="isUpload = true" v-if="design || temp">Upload Sponsor Images</button>
                                            <label for="file" class="btn btn-primary ml-2 mr-auto">Upload Custom Design</label>
                                        </template>
                                    </div>
                                </b-card>
                            </b-colxx>
                        </b-row>
                    </template>
                    <input type="file" id="file" class="d-none" @change="customUpload" onclick="this.value = null" />
                </b-tab>
                <b-tab title="Sponsor Menu" @click="type = 'modal'">
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
                                                    <p class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</p>
                                                </b-button>
                                                <div class="w-15 w-sm-100">
                                                    <b-badge pill :variant="'primary'">Header</b-badge>
                                                </div>
                                            </div>
                                            <div class="my-auto mr-2 w-sm-100">
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
                                                    <p class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</p>
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
                                        <b-tab v-for="(tab, thisIndex) in tabs[tabMenu]" :key="tab.id" @click="contentIndex = thisIndex">
                                            <template v-slot:title>
                                                {{tab.title}}
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
                                                    <div class="top-right-button-container d-flex my-2">
                                                        <b-button variant="primary" size="sm" v-b-modal.asset_modal>Add Asset Modal</b-button>
                                                        <b-status-button
                                                            :selectAll="selectAll"
                                                            :isSelectedAll="isSelectedAll" 
                                                            :keymap="keymap"
                                                            :isAnyItemSelected="isAnyItemSelected" 
                                                            :setStatus="setStatus"
                                                            :status="status"
                                                        />
                                                    </div>
                                                </b-colxx>
                                                <b-colxx xxs="12">
                                                    <b-card class="d-flex flex-row mb-3" no-body v-for="(item, index) in items" :key="index" :id="item.id">
                                                        <div class="custom-control custom-checkbox pl-1 align-self-center pr-4">
                                                            <div class="custom-control custom-checkbox mb-0" @click.prevent="toggleItem($event, item.id)">
                                                                <b-form-checkbox :checked="selectedItems.includes(item.id)" />
                                                            </div>
                                                        </div>
                                                        <img class="list-thumbnail responsive border-0" :src="getType(item.item)">
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
                            <button class="btn btn-primary mt-2" @click="setContent">Save</button>
                        </b-tabs>
                    </div>
                </b-tab>
                <b-tab title="Sponsor Menu Settings" @click="type = 'settings'" class="pb-5">
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
                </b-tab>
                <b-tab title="Publish" @click="type = 'publish'" class="pb-5">
                    <div class="w-75 position-relative mx-auto">
                        <div class="w-100 position-relative">
                            <img :src="'/data/' + temp.media" alt="Sponsor" class="w-100" v-if="temp && temp.media" id="content" @click="getArea" />
                            <img :src="design" alt="Sponsor" class="w-100" v-else id="content" />
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
                        @click="hideModal('add_modal')"
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
        <div class="w-100 position-relative" v-else>
            <div class="arrow-btn" @click="isPreview = false">
                Return
            </div>
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
            <div class="container" v-if="header && header.length">
                <b-row>
                    <b-col class="exhibit-header text-center w-100" :style="getHeaderStyle(headerStyle)">
                        <div class="d-inline-flex">
                            <div
                                class="header-btn"
                                v-for="(item, index) in header"
                                :key="index"
                                :style="getExhibitButtonStyle(headerStyle)"
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
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'
import Draggable from 'vuedraggable'

import Header from "../../../../containers/Web/DomainHeader"
import QuillEditor from '../../../../components/Pages/QuillEditor'
import StatusButton from '../../../../components/Pages/StatusButton'
import mixins from '../../../../mixins/mixins'

export default {
    components: {
        'b-header': Header,
        'draggable': Draggable,
        "b-quill-editor" : QuillEditor,
        'b-status-button': StatusButton
    },
    metaInfo () {
        return { title: `Sponsor Edit` }
    },
    data() {
        return {
            id: '',
            apiBase: '/wizard/booth/',
            booth: [],
            items: [],
            selectedItems: [],
            isUpload: false,
            isUploaded: false,
            currentIndex: 0,
            title: '',
            assetTitle: 'Add new Asset',
            assetItem: {
                id: 0,
                title: '',
                file: null
            },
            data: [
                {
                    x: 0,
                    ex: 0,
                    y: 0,
                    ey: 0,
                    url: null,
                    link: null
                }
            ],
            urls: [],
            isOnline: [],
            isConfirm: [],
            isConfirmed: false,
            isSelect: [],
            isClick: false,
            isArea: false,
            isUploaded: false,
            selected: false,
            url: null,
            formData: new FormData(),
            items: [],
            header: [],
            menus: [],
            selectedMenus: [],
            status: ['Active', 'Inactive', 'Delete'],
            editData: {},
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
            type: 'upload',
            setting: 'menus',
            editorId: null,
            temp: '',

            menuTitle: '',
            editId: 0,
            submitText: 'Create',
            notifyText: 'Successfully Created!',
            titleText: 'Create Menu',

            design: null,
            extension: '',
            viewItem: {},
            tabs: [],
            tabIndex: 0,
            totalItems: [],
            tabModel: 0,
            tabMenu: 0,
            isDelStatus: 0,
            contentIndex: 0,
            isPreview: false,
            exhibitHtml: '',
            currentTab: {}
        }
    },
    computed: {
        ...mapGetters({
            siteStyle: 'site/getStyle',
            siteId: 'site/getSiteId',
            user: 'auth/user'
        }),
        isSelectedAll() {
            if (this.type == 'modal') {
                return this.selectedMenus.length >= this.menus.length
            } else if (this.type == 'content') {
                if (this.items) {
                    return this.selectedItems.length >= this.items.length
                } else {
                    return true
                }
            }
        },
        isAnyItemSelected() {
            if (this.type == 'modal') {
                return (
                    this.selectedMenus.length > 0 &&
                    this.selectedMenus.length < this.menus.length
                )
            } else if (this.type == 'content') {
                return (
                    this.selectedItems.length > 0 &&
                    this.selectedItems.length < this.items.length
                )
            }
        },
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
        setBooth() {
            this.formData.append('id', this.id)
            this.formData.append('data', JSON.stringify(this.data))
            Axios.post(this.apiBase + 'set', this.formData).then(res => {
                if (res.status == 200) {
                  this.$notify('primary filled', '', 'Successfully Saved', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        publish() {
            this.formData.append('id', this.id)
            this.formData.append('siteId', this.siteId)
            Axios.post(this.apiBase + 'publish', this.formData).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', 'Thank you!', 'You have successfully completed your Sponsor, our team will review.', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        boothView(item) {
            this.viewItem = item
            this.fileExtension = this.viewItem.item.split('.').pop()
        },
        publish() {
            this.formData.append('id', this.id)
            this.formData.append('siteId', this.siteId)
            Axios.post(this.apiBase + 'publish', this.formData).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', 'Thank you!', 'You have successfully completed your Booth, our team will review.', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        onFileChange(e) {
            this.data[this.currentIndex].url = null
            const file = e.target.files[0]
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
        view(item) {
            this.viewItem = item
            this.extension = this.viewItem.item.split('.').pop()
        },
        onAssetFileUpload(e) {
            this.assetItem.file = e.target.files[0] 
        },
        customUpload(e) {
            const file = e.target.files[0]
            if (file.size / 10000 > 1) {
                this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
            }
            this.design = URL.createObjectURL(file)
            this.formData.append('template', file)
        },
        hideModal(refname) {
            this.$refs[refname].hide();
        },
        choose() {
            if (this.user.role < 3) {
                this.$router.push('/app/wizard/sponsor/models')
            } else {
                this.$router.push('/wizard/sponsor/models')
            }
        },
        getArea(e) {
            if (this.isSelect[this.currentIndex]) {
                this.isClick = !this.isClick
                var clientHeight = document.getElementById('content').clientHeight
                var clientWidth = document.getElementById('content').clientWidth
                const x = e.layerX / clientWidth * 100
                const y = e.layerY / clientHeight * 100 

                if (this.isClick) {
                    this.data[this.currentIndex].x = x
                    this.data[this.currentIndex].y = y
                    this.data[this.currentIndex].ex = x
                    this.data[this.currentIndex].ey = y
                } else {
                    this.data[this.currentIndex].ex = x
                    this.data[this.currentIndex].ey = y
                }
            }

        },
        areaStyle(data) {
            return {
                'top': 'calc(' + Math.min(data.y, data.ey) + '%)',
                'left': 'calc(' + Math.min(data.x, data.ex) + '%)',
                'width': 'calc(' + Math.abs(data.ex - data.x) + '%)',
                'height': 'calc(' + Math.abs(data.ey - data.y) + '%)',
                'cursor' : 'pointer'
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
        onEditorChange({ editor, html, text }) {
            this.tabs[this.tabMenu][this.contentIndex].content = html
        },
        selectAll(isToggle) {
            if (this.type == 'modal') {
                if (this.selectedMenus.length >= this.menus.length) {
                    if (isToggle) this.selectedMenus = [];
                } else {
                    this.selectedMenus = this.menus.map(x => x.id);
                }
            } else {
                if (this.selectedItems.length >= this.items.length) {
                    if (isToggle) this.selectedItems = [];
                } else {
                    this.selectedItems = this.items.map(x => x.id);
                }
            }
        }, 
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                break;
                case "undo":
                    if (this.type == 'modal') {
                        this.selectedMenus = [];
                    } else {
                        this.selectedItems = [];
                    }
                break;
            }
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedMenus.length > 0) {
                if (this.type == 'modal') {
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
                }
            } else {
                if (this.type == 'modal') {
                    if (this.selectedMenus.includes(itemId)) {
                        this.selectedMenus = this.selectedMenus.filter(x => x !== itemId);
                    } else this.selectedMenus.push(itemId);
                } else {
                    if (this.selectedItems.includes(itemId)) {
                        this.selectedItems = this.selectedItems.filter(x => x !== itemId);
                    } else this.selectedItems.push(itemId);
                }
            }
        },
        edit(item) {
            if (this.type == 'content') {
                this.assetItem = item
            } else {
                this.editData = item
            }
        },
        initData() {
            if (this.type == 'content') {
                this.assetItem = {}
            } else {
                this.editData = {}
            }
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
        getHeader() {
            let headerArray = []
            let sourceArray = []
            this.header.forEach(menu => {
                headerArray.push(menu.id)
            })
            this.menus.forEach(menu => {
                sourceArray.push(menu.id)
            })
            Axios.post(this.apiBase + 'order', {header: headerArray, source: sourceArray, id: this.id}).then(res => {
                if (res.status == 200) {
                    this.header = res.data.header
                }
            })
        },
        confirm() {
            this.isConfirmed = true
            if (this.isDelStatus == 2) {
                this.closeTab()
            } else {
                this.setStatus(2)
            }
        },
        closeTab() {
            if (this.isDelStatus == 0 && !this.isConfirmed) {
                this.isDelStatus = 2
                this.$refs['confirm_modal'].show()
            } else {
                var id = this.tabs[this.tabMenu][this.tabModel].id
                Axios.post(this.apiBase + 'delTab', {delId: id, id: this.id}).then(res => {
                   this.header = res.data.header
                })
                this.isDelStatus = 0
                this.isConfirmed = false
                this.$refs['confirm_modal'].hide()
            }
        },
        setStatus(status) {
            if (status == 2 && !this.isConfirmed) {
                this.isDelStatus = 1
                this.$refs['confirm_modal'].show()
            } else {
                if (this.type == 'modal') {
                    Axios.post(this.apiBase + 'status', {status: status, items: this.selectedMenus, id: this.id}).then(res => {
                        this.menus = res.data.resource
                        this.header = res.data.data.header
                        this.selectedMenus = []
                    })
                } else {
                    Axios.post(this.apiBase + 'mediaStatus', {status: status, items: this.selectedItems, id: this.id}).then(res => {
                        if (res.status == 200) {
                            this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                            this.header = res.data.header
                            this.selectedItems = []
                        } else {
                            this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                        }
                    })
                }
            }
            this.isConfirmed = false
            this.$refs['confirm_modal'].hide()
        },
        addTab(index) {
            this.tabs[index].push(
                {title: 'Tab'}
            )
            var tabs = this.tabs
            this.tabs = []
            this.tabs = tabs
            this.setContent() 
        },
        updateTitle(title) {
            if (title == '') {
                this.$notify('primary filled', '', 'Please Insert Title!', { duration: 3000, permanent: false })
            } else {
                var tabs = this.tabs
                this.tabs = []
                this.tabs = tabs
                this.setContent()
            }
        },
        addNewItem(title, editId) {
            Axios.post(this.apiBase + 'setHeader', {title: title, id: this.id, editId: editId}).then(res => {
                if (res.data.resource && res.data.data) {
                    this.menus = res.data.resource
                    this.header = res.data.data.header
                    this.selectedMenus = []
                    this.hideModal('add_modal')
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                }
            })
        },
        addNewAssetItem(item) {
            let onAdd = true
            if (item.title == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert file title', { duration: 3000, permanent: false })
                onAdd = false            }
            if (!item.file) {
                this.$notify('primary filled', 'Warn!', 'Please insert file', { duration: 3000, permanent: false })
                onAdd = false
            }
            if (onAdd) {
                var id = this.tabs[this.tabMenu][this.tabModel].id
                var formData = new FormData()
                formData.append('title', item.title)
                formData.append('file', item.file)
                formData.append('id', id)
                formData.append('boothId', this.id)
                formData.append('editId', this.editId)
                formData.append('type', 1)
                Axios.post(this.apiBase + "addAsset", formData).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                        this.header = res.data.header
                        this.hideModal('asset_modal')
                        this.assetItem = {
                            id: 0,
                            title: '',
                            file: null
                        }
                    }
                })
            }
        },
        setHead() {
            Axios.put(this.apiBase + 'head', {style: this.headerStyle, id: this.id}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                }
            })
        },
        setContent() {
            Axios.post(this.apiBase + 'content', {data: this.header, tabs: this.tabs, id: this.id}).then(res => {
                this.header = res.data.header
            })
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
        setTitle(id) {
            Axios.put(this.apiBase + 'setTitle', {id: id, title: this.booth.title, type: 1}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                }
            })
        },
        addOption() {
            this.data.push({
                x: 0,
                ex: 0,
                y: 0,
                ey: 0,
                url: null
            })
            this.url = null
        },
        delData(index) {
            this.data.splice(index, 1)
            this.formData.delete('media'+index)
        },
        getTab(id, index) {
            this.editorId = id;
        },
        returnTab() {
            this.tabIndex--
        },
        nextTab() {
            this.tabIndex++
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
        }
    },
    watch: {
        header(val) {
            if (val.length) {
                this.editorId = val[0].id
            }
            this.tabs = []
            val.forEach((element, index) => {
                this.tabs[index] = element.tab
                if (this.tabs[index].length == 0) {
                    this.tabs[index].push({
                        title: 'Resource'
                    })
                    this.tabs[index].push({
                        title: 'Video'
                    })
                    this.setContent()
                }
            })

            if (this.header.length) {
                this.exhibitHtml = this.header[0]
            }

            if (val.length && val[this.tabMenu] && val[this.tabMenu].tab && val[this.tabMenu].tab[this.tabModel]) {
                this.items = this.header[this.tabMenu].tab[this.tabModel].media
            } else {
                this.items = []
            }
        },
        editData: function(val) {
            if (val.id) {
                this.titleText = "Edit The Title"
                this.menuTitle = val.title
                this.submitText = 'Change'
                this.editId = val.id
            } else {
                this.titleText = "Create Menu"
                this.menuTitle = ''
                this.submitText = 'Create'
                this.editId = 0
            }
        },
        assetItem(val) {
            if (val.id) {
                this.titleText = "Edit The Asset"
                this.menuTitle = val.title
                this.submitText = 'Change'
                this.editId = val.id
                this.notifyText = 'Successfully Changed!'
            } else {
                this.titleText = "Create Menu"
                this.menuTitle = ''
                this.submitText = 'Create'
                this.editId = 0
                this.notifyText = 'Successfully Created!'
            }
        },
        tabMenu(val) {
            if (this.header[val] && this.header[val].tab && this.header[val].tab[this.tabModel]) {
                this.items = this.header[val].tab[this.tabModel].media
            } else {
                this.items = []
            }
        },
        tabModel(val) {
            if (this.header[this.tabMenu] && this.header[this.tabMenu].tab && this.header[this.tabMenu].tab[val]) {
                this.items = this.header[this.tabMenu].tab[val].media
            } else {
                this.items = []
            }
        },
        tabIndex(val) {
            if (val == 0) {
                this.type = 'upload'
            } else if (val == 1) {
                this.type = 'modal'
            } else if (val == 2) {
                this.type = 'content'
            } else if (val == 3) {
                this.type = 'settings'
            } else if (val == 4) {
                this.type = 'publish'
            }
        }
    },
    mounted() {
        this.id = this.$route.params.id
        this.init()
    }
}
</script>