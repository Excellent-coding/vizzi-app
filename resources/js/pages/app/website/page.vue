<template>
    <div>
        <b-header
            :title="capitalizeFirstLetter(page.title)"
            :selectAll="selectAll"
            :isSelectedAll="isSelectedAll"
            :keymap="keymap"
            :isAnyItemSelected="isAnyItemSelected"
            :setStatus="setStatus"
            :status="status"
            :type="page.type"
            :initData="initData"
        />

        <b-row v-if="page.type == 'content'">
            <div class="container">
                <b-colxx xxs="12" v-if="!isPreview">
                    <b-card no-body>
                        <b-card-body class="wizard wizard-default">
                            <form-wizard nav-class="justify-content-between" :submit="submit">
                                <tab :name="'Upload Media'" desc="Please upload the media file" :selected="true">
                                    <div class="w-md-50 w-100 mx-auto">
                                        <div class="wizard-basic-step">
                                            <b-form-radio-group v-model="bgType" class="mt-2">
                                                <b-form-radio :value="0">Upload</b-form-radio>
                                                <b-form-radio :value="1">Link</b-form-radio>
                                            </b-form-radio-group>
                                            <b-input-group class="mb-3 w-100" v-if="bgType">
                                                <b-form-input v-model="bg" @change="formData.set('file', bg)" />
                                            </b-input-group>
                                            <b-input-group :prepend="$t('input-groups.upload')" class="mb-3 w-100" v-else>
                                                <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onFileChange" />
                                            </b-input-group>

                                            <div class="container">
                                                <template v-if="bgType">
                                                    <iframe :src="bg + '?background=1'" style="width: 100%;height: 31vw" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                                </template>
                                                <template v-else>
                                                    <img v-if="url && extension !== 'mp4'" :src="url" class="edit-size mb-2"/>
                                                    
                                                    <video v-if="url && extension === 'mp4'" class="edit-size" height="auto" autoplay="autoplay" loop="loop" muted="">
                                                        <source :src="url" type="video/mp4">
                                                    </video>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </tab>
                                <tab :name="'Insert Positions'" desc="Insert events">
                                    <div class="wizard-basic-step">
                                        <b-row class="mb-2" v-if="url || bg">
                                            <b-col md="4" sm="12">
                                                <b-row class="mb-3">
                                                    <b-colxx sm="12">
                                                        <b-form-group label="Link to Page" class="my-2">
                                                            <multiselect
                                                                v-model="pageOption"
                                                                :options="pageOptions"
                                                                label="text"
                                                            />
                                                        </b-form-group>
                                                        <b-form-group class="mt-3" v-if="pageOption.value < 2">
                                                            <v-select v-model="newItem.link" :options="links" v-if="pageOption.value" />
                                                            <b-form-input type="text" v-model="newLink" v-else/>
                                                        </b-form-group>
                                                        <b-form-group class="mt-3" v-if="pageOption.value == 3">
                                                            <v-select v-model="day" :options="dayList" />
                                                        </b-form-group>
                                                    </b-colxx>
                                                    <b-colxx sm="6">
                                                        <b-form-group label="Start">
                                                            <b-form-input type="text" v-model="startPoint"/>
                                                        </b-form-group>
                                                    </b-colxx>
                                                    <b-colxx sm="6">
                                                        <b-form-group label="End">
                                                            <b-form-input type="text" v-model="endPoint"/>
                                                        </b-form-group>
                                                    </b-colxx>
                                                </b-row>
                                                <template v-if="pageOption.value < 3">
                                                    Upload
                                                    <b-form-radio-group v-model="option" class="mt-2">
                                                        <b-form-radio value="upload">Upload</b-form-radio>
                                                        <b-form-radio value="link">Link</b-form-radio>
                                                    </b-form-radio-group>
                                                    <b-input-group :prepend="$t('input-groups.upload')" class="my-3" v-if="option == 'upload'">
                                                        <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onFileUpload" />
                                                    </b-input-group>
                                                    <b-form-input v-model="newItem.media" placeholder="Insert Url" class="my-3" v-else />
                                                </template>
                                                <template v-if="pageOption.value == 5">
                                                    <b-form-radio-group v-model="chatType" class="my-2">
                                                        <b-form-radio :value="0">Help Desk</b-form-radio>
                                                        <b-form-radio :value="1">Any Desk</b-form-radio>
                                                    </b-form-radio-group>
                                                </template>
                                                <template v-if="pageOption.value == 6">
                                                    <b-form-group>
                                                        <b-form-input v-model="newItem.link"></b-form-input>
                                                    </b-form-group>
                                                </template>
                                                <b-button variant="primary" class="my-1" size="sm" @click="addItem">Add LInk</b-button>
                                                <input id="media" type="file" class="d-none" />
                                                <input id="changeMedia" type="file" class="d-none" />
                                            </b-col>
                                            <b-col md="8" sm="12" class="text-center">
                                                <div class="w-100 position-relative">
                                                    <template v-if="bgType">
                                                        <iframe :src="bg + '?background=1'" style="width: 100%;height: 31vw" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                                        <div id="content" class="w-100 bg-transparent position-absolute" style="height: 31vw; top: 0" @click="getPosition"></div>
                                                    </template>
                                                    <template v-else>
                                                        <img v-if="url && extension !== 'mp4'" :src="url" class="w-100" @click="getPosition" id="content"/>
                                                        <video v-if="url && extension === 'mp4'" width="100%" height="auto" autoplay="autoplay" id="content" loop="loop" muted="" @click="getPosition">
                                                            <source :src="url" type="video/mp4">
                                                        </video>
                                                    </template>

                                                    <div
                                                        class="position-absolute point"
                                                        v-for="(point, index) in points" :key="index"
                                                        :style="pointStyle(point.position, true)"
                                                        @click="getPoint(index)"
                                                    >
                                                        <template v-if="media[index] && point.link != 'Embed Video'">
                                                            <img class="upload-img" :src="'/data/'+media[index]" v-if="media[index].substr(0,4) != 'http'" />
                                                            <img :src="media[index]" alt="" class="upload-img" v-else>
                                                        </template>
                                                        <img class="upload-img" :src="files[index]" v-else-if="files[index] && point.link != 'Embed Video'" />
                                                        <div class="upload-img" style="background: rgba(0 ,0, 0, .2)" />
                                                    </div>
                                                    <div
                                                        class="position-absolute point"
                                                        :style="pointStyle(newItem.position)"
                                                    >
                                                        <img class="upload-img" :src="file" v-if="newItem.file && pageOption.value < 2" />
                                                        <img class="upload-img" :src="newItem.media" v-else-if="newItem.media && pageOption.value < 2">
                                                    </div>
                                                </div>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-button variant="outline-primary" class="my-2" @click="removeItems" v-if="isSelected">Remove</b-button>
                                            <div class="table-responsive">
                                                <b-table
                                                    selectable
                                                    select-mode="multi"
                                                    hover
                                                    :items="points"
                                                    :fields="fields"
                                                    @row-selected="onRowSelected"
                                                >
                                                    <template v-slot:cell(#)="data">{{data.index + 1}}</template>
                                                    <template v-slot:cell(linkToPage)="data">
                                                        <div v-if="data.item.link != 'Agenda By Day'">{{data.item.link}}</div>
                                                        <div v-else>Agenda - {{dayList[data.item.value].label}}</div>
                                                    </template>
                                                    <template v-slot:cell(start)="data">
                                                        ({{Math.round(data.item.position.x * 1000) / 1000}}, {{Math.round(data.item.position.y * 1000) / 1000}})
                                                    </template>
                                                    <template v-slot:cell(end)="data">
                                                        ({{Math.round(data.item.position.ex * 1000) / 1000}}, {{Math.round(data.item.position.ey * 1000) / 1000}})
                                                    </template>
                                                    <template v-slot:cell(media)="data">
                                                        {{data.item.media}}
                                                    </template>
                                                    <template v-slot:cell(selected)="data">
                                                        <template v-if="selected.includes(data.index)">
                                                            True
                                                        </template>
                                                    </template>
                                                </b-table> 
                                            </div>
                                        </b-row>
                                    </div>
                                </tab>
                                <tab name="Submit" type="done">
                                    <div class="w-100 position-relative mb-2">
                                        <template v-if="bgType">
                                            <iframe :src="bg + '?background=1'" style="width: 100%;height: 44vw" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                            <div id="content" class="w-100 bg-transparent position-absolute" style="height: 44vw; top: 0" @click="getPosition"></div>
                                        </template>
                                        <template v-else>
                                            <img v-if="url && extension !== 'mp4'" :src="url" class="w-100" @click="getPosition" id="content"/>
                                            <video v-if="url && extension === 'mp4'" width="100%" height="auto" autoplay="autoplay" id="content" loop="loop" muted="" @click="getPosition">
                                                <source :src="url" type="video/mp4">
                                            </video>
                                        </template>
                                        <div
                                            class="position-absolute point"
                                            v-for="(point, index) in points" :key="index"
                                            :style="pointStyle(point.position, true)"
                                            @click="getPoint(index)"
                                        >
                                            <template v-if="media[index] && point.link != 'Embed Video'">
                                                <img class="upload-img" :src="'/data/'+media[index]" v-if="media[index].substr(0,4) != 'http'" />
                                                <img :src="media[index]" alt="" class="upload-img" v-else>
                                            </template>
                                            <img class="upload-img" :src="files[index]" v-else-if="files[index] && point.link != 'Embed Video'" />
                                            <div class="upload-img" style="background: rgba(0, 0, 0, .2)" />
                                        </div>
                                        <div
                                            class="position-absolute point"
                                            :style="pointStyle(newItem.position)"
                                        >
                                            <img class="upload-img" :src="file" v-if="newItem.file" />
                                            <img class="upload-img" :src="newItem.media" v-else-if="newItem.media">
                                            <div class="upload-img" style="background: rgba(0, 0, 0, .2)" />
                                        </div>
                                    </div>
                                </tab>
                            </form-wizard>
                        </b-card-body>
                    </b-card>
                </b-colxx>
                <b-colxx xxs="12" v-else>
                    <div class="w-100 position-relative">
                        <img v-if="url && extension !== 'mp4'" :src="url" class="w-100" id="content"/>
                        <video v-if="url && extension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
                            <source :src="url" type="video/mp4">
                        </video>
                        <a
                            class="position-absolute area text-left"
                            v-for="(point, index) in points" :key="index"
                            :style="pointStyle(point.position, true)"
                        >{{point.desc}}</a>
                    </div>
                </b-colxx>
            </div>
        </b-row>

        <b-row v-else-if="page.type == 'modal'">
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
                                v-b-modal.add_modal
                                @click="edit(item)"
                            >Edit</button>
                        </div>
                    </div>
                </b-card>
            </b-colxx>
        </b-row>

        <b-row v-else-if="page.type == 'exhibit'">
            <div class="container">
                <b-colxx xxs="12">
                    <b-card no-body>
                        <b-card-body class="wizard wizard-default">
                            <f-wizard>
                                <tab :name="$t('wizard.step-exhibit-1')" desc="Please Choose the Image file" :selected="true">
                                    <div class="wizard-basic-step w-75 mx-auto">
                                        <b-input-group :prepend="$t('input-groups.upload')" class="mb-3">
                                            <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onFileChange" />
                                        </b-input-group>
                                        <img v-if=exhibitUrl :src="'/data/' + exhibitUrl" class="mb-2 w-100" />
                                        <img v-else-if="url && extension !== 'mp4'" :src="url" class="mb-2 w-100" />
                                    </div>
                                </tab>
                                <tab :name="$t('wizard.step-exhibit-2')" desc="Set Menu Header">
                                    <div class="wizard-basic-step" v-if="url || exhibitUrl">
                                        <b-row>
                                            <b-colxx xxs="12" class="header text-center" :style="getHeaderStyle()">
                                                <div class="d-inline-flex">
                                                    <div class="header-btn" v-for="(item, index) in menus" :key="index" :style="getButtonStyle()">{{item.title}}</div>
                                                </div>
                                            </b-colxx>
                                        </b-row>

                                        <b-row class="my-5">
                                            <b-colxx xs="12" md="6">
                                                <label class="form-group has-float-label">
                                                    <input type="color" class="form-control" v-model="headerStyle.bg" />
                                                    <span>Header Background</span>
                                                </label>
                                                <label class="form-group has-float-label">
                                                    <input type="number" class="form-control" v-model="headerStyle.height" />
                                                    <span>Header Height</span>
                                                </label>
                                                <label class="form-group has-float-label">
                                                    <input type="color" class="form-control" v-model="headerStyle.btn.bg" />
                                                    <span>Button Background</span>
                                                </label>   
                                                <label class="form-group has-float-label">
                                                    <input type="color" class="form-control" v-model="headerStyle.btn.color" />
                                                    <span>Button Text Color</span>
                                                </label>
                                                <label class="form-group has-float-label">
                                                    <input type="number" class="form-control" v-model="headerStyle.btn.radius" />
                                                    <span>Button Boder radius</span>
                                                </label>
                                                <label class="form-group has-float-label">
                                                    <input type="number" class="form-control" v-model="headerStyle.btn.space" />
                                                    <span>Button Space</span>
                                                </label>                                         
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <template v-for="(item, index) in items">
                                                    <div :key="index" :id="item.id" class="p-2 my-1" v-if="page.id != item.id">
                                                        <div :class="{'d-flex flex-row':true,'active' : selectedItems.includes(item.id)}">
                                                            <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                                                <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event, item.id)">
                                                                    <b-form-checkbox :checked="selectedItems.includes(item.id)" class="itemCheck mb-0" />
                                                                </div>
                                                                <div class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </b-colxx>
                                        </b-row>
                                    </div>
                                </tab>
                                <tab :name="$t('wizard.step-exhibit-3')" desc="Set Content">
                                    <div class="custom-tab">
                                        <b-tabs content-class="my-3">
                                            <b-tab v-for="item in menus" :key="item.id" :title="item.title" @click="getTab(item.id)">
                                                <b-quill-editor
                                                    :onEditorChange="onEditorChange"
                                                >
                                                </b-quill-editor>
                                            </b-tab> 
                                        </b-tabs>
                                    </div>
                                </tab>
                                <tab type="done">
                                    <div class="wizard-basic-step text-center">
                                        <b-button variant="primary" class="mb-2" @click="exhibitSave" v-if="(url || exhibitUrl) && menus">Save</b-button>
                                    </div>
                                </tab>
                            </f-wizard>
                        </b-card-body>
                    </b-card>
                </b-colxx>
            </div>
        </b-row>

        <b-row v-else-if="page.type == 'iframe'">
            <div class="container">
                <b-row>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Title">
                            <b-form-input v-model="page.title" />
                        </b-form-group>
                    </b-colxx>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Url">
                            <b-form-input v-model="page.url" />
                        </b-form-group>
                    </b-colxx>
                    <b-colxx xxs="12">
                        <b-form-group label="Code">
                            <b-form-textarea v-model="page.media" />
                        </b-form-group>
                    </b-colxx>
                </b-row>
                <b-button variant="primary" @click="addPage(page)">Save</b-button>
            </div>
        </b-row>

        <b-row v-else-if="page.type == 'cart'"></b-row>
        <b-modal
            id="add_modal"
            ref="add_modal"
            :title="assetTitle"
            modal-class="modal-right"
        >
            <b-form>
                <b-form-group label="Title">
                    <b-form-input v-model="newAssetItem.title" />
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
                <b-button variant="primary" @click="addNewItem(newAssetItem)" class="mr-1">{{ $t('submit') }}</b-button>
            </template>
        </b-modal>
        <b-modal id="view_modal" size="lg" :title="viewItem.title" centered hide-footer>
            <video v-if="extension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
                <source :src="'/data/'+viewItem.item" type="video/mp4">
            </video>

            <iframe class="w-100" :src="'/data/'+viewItem.item" v-else-if="extension === 'pdf'" style="height: 75vh" />

            <img class="w-100" :src="'/data/'+viewItem.item" v-else />
        </b-modal>
        <b-modal
            id="linkModal"
            ref="linkModal"
            title="Create New Page"
            modal-class="modal-center"
        >
            <b-form>
                <b-form-group :label="'Title'">
                    <b-form-input v-model="newLinkItem.title" />
                </b-form-group>
                <b-form-group :label="'Page Style'">
                    <b-select class="form-control" v-model="newLinkItem.type">
                        <option :value="t.value" v-for="(t, index) in types" :key="index">{{t.label}}</option>
                    </b-select>
                </b-form-group>
                <label v-if="newLinkItem.type != 'modal'">Url</label>
                <b-input-group v-if="newLinkItem.type != 'modal'">
                    <b-input-group-append>
                        <b-input-group-text>
                            {{host}}
                        </b-input-group-text>
                        <b-form-input v-model="newLink"/>
                    </b-input-group-append>
                </b-input-group>
            </b-form>

            <template slot="modal-footer">
                <b-button
                    variant="outline-secondary"
                    @click="hideModal('linkModal')"
                >{{ $t('cancel') }}</b-button>
                <b-button variant="primary" @click="addNewLinkItem(newLinkItem.title, newLinkItem.type, newLink, 0)" class="mr-1">Create</b-button>
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
import Axios from "axios";

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import Multiselect from 'vue-multiselect'

import Header from "../../../containers/Web/DomainHeader"

import FormWizard from "../../../components/Form/Wizard/FormWizard"
import FWizard from "../../../components/Form/Wizard/FWizard"
import Tab from "../../../components/Form/Wizard/Tab"

import addModal from '../../../components/Pages/AddNewItemModal'
import QuillEditor from '../../../components/Pages/QuillEditor'
import 'vue-multiselect/dist/vue-multiselect.min.css'

export default {
    middleware: 'should',
    components: {
        'b-header': Header,
        "v-select": vSelect,
        "b-add-modal": addModal,
        "tab": Tab,
        "b-quill-editor" : QuillEditor,
        Multiselect,
        FWizard, FormWizard
    },
    metaInfo () {
        return { title: `Vizzi Page` }
    },
    data() {
        return {
            chatType: 0,
            bgType: 0,
            bg: '',
            pageOption: {text: 'Link', value: 1},
            pageOptions: [
                {text: 'Page Link', value: 1},
                {text: 'New Link', value: 0},
                {text: 'Video', value: 2},
                {text: 'Agenda', value: 3},
                {text: 'Agenda By Day', value: 4},
                {text: 'Chat', value: 5},
                {text: 'HyperLink', value: 6}
            ],
            newLink: '',
            id: {type: Number},
            apiBase:  '/page/',
            title: {type: String},
            assetTitle: 'Add New Item',
            option: 'upload',
            page: {},
            media: [],
            url: null,
            points: [],
            newAssetItem: {
                id: 0,
                title: "",
                file: null
            },
            day: '',
            newItem: {
                link: '',
                file: null,
                media: '',
                position: {
                    x: 0,
                    y: 0,
                    ex: 0,
                    ey: 0
                }
            },
            newLinkItem: {
                title: '',
                type: '',
                url: ''
            },
            isPreview: false,
            formData: new FormData(),
            editorOption: {
                placeholder: '',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                        [
                            { list: 'ordered' },
                            { list: 'bullet' },
                            { indent: '-1' },
                            { indent: '+1' }
                        ],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            },
            items: [],
            fields: ['#', 'linkToPage', 'Start', 'End', 'Media', 'selected'],
            selectedItems: [],
            extension: '',
            links: [],
            dayList: [
                {label: 'Sunday', value: 0},
                {label: 'Monday', value: 1},
                {label: 'Tuesday', value: 2},
                {label: 'Wednesday', value: 3},
                {label: 'Thursday', value: 4},
                {label: 'Friday', value: 5},
                {label: 'Saturday', value: 6}
            ],
            status: ['Active', 'InActive', 'Delete'],
            editData: {},
            viewItem: {},
            file: null,
            files: [],
            images: [],
            file_0: null, file_1: null, file_2: null, file_3: null, file_4: null, file_5: null, file_6: null, file_7: null, file_8: null, file_9: null, file_10: null, file_11: null, file_12: null, file_13: null, file_14: null, file_15: null, file_16: null, file_17: null, file_18: null, file_19: null, file_20: null,
            headerStyle: {
                bg: '#922c88',
                height: 32,
                btn: {
                    bg: '#d683ce',
                    color: '#ffffff',
                    radius: 4,
                    space: 8
                }
            },
            menus: [],
            contentData: [],
            editorId: null,
            isClick: false,
            startPoint: '(0, 0)',
            endPoint: '(0, 0)',
            selected: [],
            isSelected: false,
            modalType: '',
            types: [
                {label: "Page", value: "content"},
                {label: "Modal", value: "modal"},
                {label: "Exhibit Hall", value: "exhibit"},
                {label: "Video", value: "video"}
            ],
            urls: [],
            isConfirmed: false,
        }
    },
    methods: {
        loadItems() {
            Axios.post(this.apiBase + 'get', {id: this.id}).then(res => {
                this.page = res.data.page
                if (this.page.type == 'content') {
                    if (this.page.background) {
                        if (this.page.background.substr(0, 4).toLowerCase() == 'http') {
                            this.bgType = 1
                            this.bg = this.page.background
                            this.formData.append('file', this.bg)
                        } else {
                            this.extension = this.page.background.split('.').pop();
                            this.url = '/data/' + this.page.background
                        }
                    }
                    this.links = res.data.titles
                    if (this.page.media) {
                        this.media = this.page.media.split(', ')
                        this.media.splice(this.media.length - 1, 1)
                    }
                    this.urls = res.data.urls
                    if(this.page.point) {
                        this.points = JSON.parse(this.page.point)
                        this.points.forEach((item, index) => {
                            item.media = this.media[index]
                        })
                    }
                } else if (this.page.type == 'modal') {
                    this.items = res.data.items
                } else if (this.page.type == 'exhibit') {
                    this.exhibitUrl = this.page.background
                    if (this.page.point) {
                        this.menus = JSON.parse(this.page.point)
                        if (this.page.style) {
                            this.headerStyle = JSON.parse(this.page.style)
                        }
                    }
                    this.items = res.data.allPages
                } else {
                    this.items = res.data.allPages
                }
            })
        },
        hideModal(refname) {
            this.$refs[refname].hide();
        },
        getPosition(e) {
            this.isClick = !this.isClick
            var clientHeight = document.getElementById('content').clientHeight
            var clientWidth = document.getElementById('content').clientWidth
            const x = e.layerX / clientWidth * 100
            const y = e.layerY / clientHeight * 100
            if (this.isClick) {
                this.newItem.position.x = x
                this.newItem.position.y = y
                this.startPoint = '(' + Math.round(this.newItem.position.x * 1000) / 1000 + ', ' + Math.round(this.newItem.position.y * 1000) / 1000 + ')'
            } else {
                this.newItem.position.ex = x
                this.newItem.position.ey = y   
                this.endPoint = '(' + Math.round(this.newItem.position.ex * 1000) / 1000 + ', ' + Math.round(this.newItem.position.ey * 1000) / 1000 + ')'
            }
        },
        getPoint(index) {
            this.newItem = this.points[index]
            this.points.splice(index, 1)
        },
        toggleItem(event, itemId) {
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
        },
        selectAll(isToggle) {
            if (this.selectedItems.length >= this.items.length) {
                if (isToggle) this.selectedItems = [];
            } else {
                this.selectedItems = this.items.map(x => x.id);
            }
        },
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                    break;
                case "undo":
                    this.selectedItems = [];
                    break;
            }
        },

        onFileChange(e) {
            const file = e.target.files[0]
            this.extension = file.name.split('.').pop();
            if (this.page.type == 'exhibit' && (this.extension == 'mp4' || this.extension == 'pdf')) {
                this.$notify('primary filled', 'Warn!', 'File is not allowed!', { duration: 3000, permanent: false })
            } else {
                if (this.extension != 'mp4') {
                    if (file.size / 1000 > 5) {
                        this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
                    }
                } else {
                    if (file.size / 100000 > 1) {
                        this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
                    }
                }
                this.url = URL.createObjectURL(file)
                this.formData.set('file', file)
            }
        },
        onAssetFileUpload(e) {
            this.newAssetItem.file = e.target.files[0] 
        },
        onFileUpload(e) {
            const file = e.target.files[0]
            const extension = file.name.split('.').pop()
            if ((extension != 'png' && extension != 'jpg' && this.pageOption.value < 2) || (extension != 'mp4' && this.pageOption.value == 2)) {
                this.$notify('primary filled', '', 'File is not allowed!', { duration: 3000, permanent: false })
            } else {
                if (file.size / 100000 > 5) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
                }
                this.newItem.file = file
                this.newItem.media = file.name
                this.file = URL.createObjectURL(file)
                this.files[this.points.length] = this.file
                this.formData.set('file'+this.points.length, file)
            }
        },
        addItem() {
            if (this.pageOption.value == 1) {
                if (this.newItem.link === '') {
                    this.$notify('primary filled', 'Sorry!', 'Please set link to page', { duration: 3000, permanent: false })
                } else {
                    this.links.forEach((title, index) => {
                        if (title == this.newItem.link) {
                            this.newItem.url = this.urls[index]
                        }
                    })
                    this.points.push(this.newItem)
                    this.itemInit()
                }
            } else if (this.pageOption.value == 0) {
                if (this.newLink == '') {
                    this.$notify('primary filled', 'Sorry!', 'Please Insert New Page Link', { duration: 3000, permanent: false })
                } else {
                    let allowed = true
                    this.urls.forEach(element => {
                        if (element == '/' + this.newLink) {
                            allowed = false
                        }
                    })
                    if (allowed) {
                        this.$refs['linkModal'].show();
                    } else {
                        this.$notify('primary filled', 'Sorry!', 'Url is already used!', { duration: 3000, permanent: false })
                    }
                }
            } else if (this.pageOption.value == 2) {
                if (this.option == 'upload') {
                    if (!this.files[this.points.length]) {
                        this.$notify('primary filled', '', 'Please Upload File!', { duration: 3000, permanent: false })
                    } else {
                        this.newItem.link = 'Embed Video'
                        this.points.push(this.newItem)
                    }
                } else {
                    if (!this.validURL(this.newItem.media)) {
                        this.$notify('primary filled', '', 'Please Insert Valid Url!', { duration: 3000, permanent: false })
                    } else {
                        this.newItem.link = 'Embed Video'
                        this.points.push(this.newItem)
                    } 
                }
            } else if (this.pageOption.value == 5) {
                this.newItem.link = 'Chat'
                this.newItem.type = this.chatType
                this.points.push(this.newItem)
            } else if (this.pageOption.value == 6) {
                this.points.push(this.newItem)
            } else {
                if (this.day.value) {
                    this.newItem.value = this.day.value
                    this.newItem.link = 'Agenda By Day'
                    this.points.push(this.newItem)
                } else {
                    this.$notify('primary filled', '', 'Please Select Day!', { duration: 3000, permanent: false })
                }
            }
            this.itemInit()
        },
        validURL(str) {
            var pattern = new RegExp('^(https?:\\/\\/)?'+ 
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ 
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ 
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ 
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ 
                '(\\#[-a-z\\d_]*)?$','i'); 
            return !!pattern.test(str);
        },
        addNewLinkItem(title, type, url, editId) {
            let allowed = true
            this.urls.forEach(element => {
                if (element == '/' + this.newLink) {
                    allowed = false
                }
            })
            if (allowed) {
                Axios.post('/menu/set', {title: title, type: type, id: this.siteId, url: url, editId: editId}).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', 'You\'ve Successfully Created a New Page', '', { duration: 3000, permanent: false })
                        this.newItem.link = title
                        this.points.push(this.newItem)
                        this.itemInit()
                    } else {
                        this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                    }
                    this.$refs['linkModal'].hide();
                })
            } else {
                this.$notify('primary filled', 'Sorry!', 'Url is already used!', { duration: 3000, permanent: false })
            }
        },
        removeItems() {
            var points = []
            var files = []
            this.points.forEach((point, index) => {
                if (!this.selected.includes(point.index)) {
                    points.push(point)
                    files.push(this.files[index])
                }
            })
            this.points = points
            this.files = files
        },
        removeItem(index) {
            this.points.splice(index, 1)
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
        edit(item) {
            this.newAssetItem = item
        },
        initData() {
            this.editData = {}
        },
        view(item) {
            this.viewItem = item
            this.extension = this.viewItem.item.split('.').pop()
        },
        capitalizeFirstLetter(string) {
            if (string) {
                return string.charAt(0).toUpperCase() + string.slice(1)
            }
        },
        onEditorChange({ editor, html, text }) {
            this.menus.forEach(menu => {
                if (this.editorId ==  menu.id) {
                    menu.contentData = html
                }
            })
        },
        getTab(id) {
            this.editorId = id;
        },
        itemInit() {
            this.newItem = {
                link: '',
                file: null,
                media: '',
                position: {
                    x: 0,
                    y: 0,
                    ex: 0,
                    ey: 0
                }
            },
            this.startPoint = '(0, 0)'
            this.endPoint = '(0, 0)'
            this.newLink = ''
        },
        onRowSelected(items) {
            this.selected = []
            items.forEach(item => {
                this.selected.push(item.index)
            });
            if (this.selected.length) {
                this.isSelected = true
            } else {
                this.isSelected = false
            }
        },
        submit() {
            this.formData.set('id', this.id)
            this.formData.set('points', JSON.stringify(this.points))
            Axios.post(this.apiBase + 'set', this.formData).then(res => {
                if (res.data.success) {
                    this.$router.push('/app/website/' + this.domain + '/menus').then(() => {
                        this.$notify('primary filled', '', 'Event Updated!', { duration: 3000, permanent: false })
                    })
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Data is not correct!', { duration: 3000, permanent: false })
                }
            })
        },
        addNewItem(item) {
            let onAdd = true
            if (item.title == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert file title', { duration: 3000, permanent: false })
                onAdd = false            }
            if (!item.file) {
                this.$notify('primary filled', 'Warn!', 'Please insert file', { duration: 3000, permanent: false })
                onAdd = false
            }
            if (onAdd) {
                this.formData = new FormData()
                this.formData.append('title', item.title)
                this.formData.append('file', item.file)
                this.formData.append('editId', item.id)
                this.formData.append('id', this.id)
                Axios.post(this.apiBase + "upload", this.formData).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', 'Warn!', 'Successfully Created!', { duration: 3000, permanent: false })
                        this.items = res.data
                        this.$refs.add_modal.hide()
                    }
                })
            }
        },
        confirm() {
            this.isConfirmed = true
            this.setStatus(2)
        },
        setStatus(value) {
            if (value == 2 && !this.isConfirmed) {
                this.$refs['confirm_modal'].show()
            } else {
                Axios.post(this.apiBase + "status", {items: this.selectedItems, status: value, id: this.id}).then(res => {
                    this.items = res.data
                    this.isConfirmed = false
                    this.$refs['confirm_modal'].hide()
                })
            }
        },
        exhibitSave() {
            this.formData.append('id', this.page.id)
            this.formData.append('menus', JSON.stringify(this.menus))
            this.formData.append('style', JSON.stringify(this.headerStyle))
            Axios.post(this.apiBase + 'exhibit', this.formData).then(res => {
                if (res.status == 200) {
                    this.$router.push('/app/website/' + this.domain + '/menus')
                }
            })
        },
        pointStyle(data, flag) {
            if (!this.isClick || flag) {
                return {
                    'top': 'calc(' + Math.min(data.y, data.ey) + '%)',
                    'left': 'calc(' + Math.min(data.x, data.ex) + '%)',
                    'width': 'calc(' + Math.abs(data.ex - data.x) + '%)',
                    'height': 'calc(' + Math.abs(data.ey - data.y) + '%)',
                    'z-index': 999
                }
            }
        },
        getBack() {
            this.isPreview = false
        },
        preview() {
            this.isPreview = true
        },
        getHeaderStyle() {
            return {
                'background-color': this.headerStyle.bg,
                'height': this.headerStyle.height + 'px'
            }
        },
        getButtonStyle() {
            return {
                'background-color': this.headerStyle.btn.bg,
                'color': this.headerStyle.btn.color,
                'margin-left': this.headerStyle.btn.space + 'px',
                'margin-right': this.headerStyle.btn.space + 'px',
                'border-radius': this.headerStyle.btn.radius + 'px',
                'margin-top': ((this.headerStyle.height - 26) / 2) + 'px'
            }
        },
        autoUrl() {
            let str = this.newLinkItem.title.replace(/^\s+|\s+$/g, '')
            str = str.toLowerCase()
            const from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            const to   = "aaaaeeeeiiiioooouuuunc------";
            for (let i=0, l=from.length ; i<l ; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }
            str = str.replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');

            this.newLinkItem.url = str
        },
        subListSet(value) {
            this.newItem.type = value.value
        },
        addPage(page) {
            if (page.url == '' || page.title == '' || page.media == '') {
                this.$notify('primary filled', '', 'Please Insert All fields!', { duration: 3000, permanent: false })
            } else {
                page.siteId = this.siteId
                Axios.post(this.apiBase + 'set-iframe', page).then(res => {
                    if (res.status == 200) {
                        this.$router.push('/app/website/' + this.domain + '/menus').then(() => {
                            this.$notify('primary filled', '', 'Event Updated!', { duration: 3000, permanent: false })
                        })
                    } else {
                        this.$notify('primary filled', 'Server Error!', 'Please try a few hours later', { duration: 3000, permanent: false })
                    }
                })
            }
        }
    },
    mounted() {
        this.id = this.$route.params.id
        this.loadItems()
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            domain: 'site/getDomain'
        }),
        isSelectedAll() {
            return this.selectedItems.length >= this.items.length
        },
        isAnyItemSelected() {
            return (
                this.selectedItems.length > 0 &&
                this.selectedItems.length < this.items.length
            )
        },
        host() {
            return window.location.host + '/'
        }
    },
    watch:  {
        points(val) {
            val.forEach((point, index) => {
                point.index = index
            });
        },
        selectedItems: {
            deep: true,
            handler() {
                this.menus = []
                let setId = true
                this.items.forEach(element => {
                    this.selectedItems.forEach(item => {
                        if (element.id == item) {
                            if (setId) {
                                this.editorId = item
                                setId = false
                            }
                            this.menus.push({id: item, title: element.title})
                        }
                    })
                })
            }
        },
        editData: function(val) {
            if (val.id) {
                this.newItem = {
                    id: val.id,
                    title: val.title,
                    file: null
                }
                this.title = "Edit Item"
            } else {
                this.title = "Add New Item"
                this.newItem = {
                    id: 0,
                    title: '',
                    file: null
                }
            }
        }
    }
}
</script>

<style lang="css">
    .main-part {
        padding-bottom: 8rem;
    }
    .point:hover {
        background: rgba(255, 255, 255, .4);
    }
    .modal-lg {
        width: 60vw
    }
    .edit-size {
        width: 100%;
        height: 100%;
        max-height: 50vh;
        max-width: 75vw;
    }
    @media (min-width: 992px) {
        .modal-lg {
            max-width: 100%;
        }
        .w-md-50 {
            width: 50% !important;
        }
    }
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 100%;
        }
    }
    .header .header-btn {
        padding: 2px 6px;
        font-weight: 400;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        line-height: 1.75;
        letter-spacing: 0.02857em;
        text-transform: capitalize;
        box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12);
    }
    .area {
        background: transparent;
        border: 0;
    }
    .upload-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .middle-center {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .custom-tab .nav-tabs .nav-link.active::before, .custom-tab .nav-tabs .nav-item.show .nav-link::before {
        left: 50% !important
    }
</style>