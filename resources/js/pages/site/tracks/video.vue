<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <breadcrumb heading="Video Manager" />
            </b-colxx>
        </b-row>

        <b-card v-if="!addNew">
            <div class="d-md-flex justify-content-between align-items-center">
                <h2 class="my-auto mr-auto">Videos</h2>
                <b-card class="mb-4 text-center mr-1 ml-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Total Videos</h6>
                    <h2>{{items.length}}</h2>
                </b-card>
                <b-card class="mb-4 text-center mx-1">
                    <h6 class="card-text font-weight-semibold mb-0">Live Streaming Videos</h6>
                    <h2>{{liveCount}}</h2>
                </b-card>
                <b-card class="mb-4 text-center ml-1 mr-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Pre-Record Videos</h6>
                    <h2>{{recordedCount}}</h2>
                </b-card>
                <div class="ml-auto my-auto">
                    <b-button
                        variant="primary"
                        style="border-radius: 6px"
                        @click="addNewItem()"
                    >Add Page</b-button>
                </div>
            </div>
            <div class="table-responsive">
                <b-table
                    hover
                    striped
                    :fields="fields"
                    :items="items"
                    v-if="items.length"
                    class="my-5"
                >
                    <template v-slot:cell(#)="data">
                        <div class="mt-2">{{data.index + 1}}</div>
                    </template>
                    <template v-slot:cell(title)="data">
                        <div class="mt-2">{{data.item.title}}</div>
                    </template>
                    <template v-slot:cell(code)="data">
                        <div class="mt-2">{{data.item.code}}</div>
                    </template>
                    <template v-slot:cell(track)="data">
                        <div class="mt-2 w-100" :style="'background:' + data.item.track.color + ';height:1rem'" />
                    </template>
                    <template v-slot:cell(description)="data">
                        <div class="mt-2">{{data.item.description}}</div>
                    </template>
                    <template v-slot:cell(pageType)="data">
                        <div class="mt-2" v-if="data.item.kind == 0">Video Js</div>
                        <div class="mt-2" v-else-if="data.item.kind == 1">Youtube</div>
                        <div class="mt-2" v-else>Vimeo</div>
                    </template>
                    <template v-slot:cell(status)="data">
                        <div class="mt-2">{{getStatus(data.item)}}</div>
                    </template>
                    <template v-slot:cell(management)="data">
                        <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret>
                            <template v-slot:button-content>
                                <i class="simple-icon-options manage-btn"></i>
                            </template>
                            <b-dropdown-item @click="edit(data.item)">Edit</b-dropdown-item>
                            <b-dropdown-item @click="remove(data.item)">Delete</b-dropdown-item>
                        </b-dropdown>
                    </template>
                </b-table>
            </div>
        </b-card>

        <b-card v-else>
            <div class="container mb-2 mx-auto">
                <h4>Add New Video Page</h4>
                <b-row>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Video Title*" class="my-2">
                            <b-form-input v-model="item.title" />
                        </b-form-group>
                        <b-form-group label="Session Category*" class="my-2">
                            <b-form-select v-model="item.category_id">
                                <option :value="data.id" v-for="data in categoryData" :key="data.id">
                                    {{data.name}}
                                </option>
                            </b-form-select>
                        </b-form-group>
                        <b-row>
                            <b-colxx sm="6">
                                <b-form-group label="Session Date" class="my-2">
                                    <v-date-picker
                                        v-model="item.date"
                                        :input-props='{
                                            placeholder: "Select Date"
                                        }'
                                    />
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="Session Track*" class="my-2">
                                    <b-form-select v-model="item.track_id">
                                        <option :value="data.id" v-for="data in trackData" :key="data.id">
                                            {{data.title}}
                                        </option>
                                    </b-form-select>
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="Start Time*" class="my-2">
                                    <vue-timepicker 
                                        format="hh:mm:A"
                                        placeholder="Please Insert Start Time"
                                        v-model="item.start"
                                        class="custom-time"
                                    />
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="End Time*" class="my-2">
                                    <vue-timepicker 
                                        format="hh:mm:A"
                                        placeholder="Please Insert End Time"
                                        v-model="item.end"
                                        class="custom-time"
                                    />
                                </b-form-group>
                            </b-colxx>
                        </b-row>
                        <b-row>
                            <b-colxx sm="6">
                                <b-form-group label="Page Format*" class="my-2">
                                    <b-form-select v-model="item.format">
                                        <option value='1'>Live</option>
                                        <option value='0'>Recorded</option>
                                    </b-form-select>
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="Player Kind" class="my-2">
                                    <b-form-select v-model="item.kind">
                                        <option value="0">Video Js</option>
                                        <option value="1">Youtube</option>
                                        <option value="2">Vimeo</option>
                                    </b-form-select>
                                </b-form-group>
                            </b-colxx>
                        </b-row>
                        <b-form-group label="Embed Link*" class="my-2">
                            <b-form-input v-model="item.link" />
                        </b-form-group>
                        <b-row>
                            <b-colxx sm="6">
                                <b-form-group label="Enable Chat" class="my-2">
                                    <b-form-select v-model="item.isChat">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </b-form-select>
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="Enable Notes" class="my-2">
                                    <b-form-select v-model="item.isNote">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </b-form-select>
                                </b-form-group>
                            </b-colxx>
                        </b-row>
                    </b-colxx>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Video Description" class="my-2">
                            <b-form-textarea v-model="item.description" />
                        </b-form-group>
                        <b-form-group label="Video Hashtags" class="my-2">
                            <b-form-textarea v-model="item.hashtag" />
                        </b-form-group>
                        <b-form-group label="Video presenters" class="my-2">
                            <multiselect
                                v-model="item.presenter"
                                :options="options"
                                :multiple="true"
                            />
                        </b-form-group>
                        <b-form-group label="Read More" class="my-2">
                            <b-form-textarea v-model="item.readMore" />
                        </b-form-group>
                        <b-form-group label="Auto Generated Analytics Code" class="my-2">
                            <b-input-group>
                                <b-input-group>
                                    <b-input-group-prepend>
                                        <b-input-group-text
                                            class="generate-btn"
                                            @click="codeGenerate()"
                                        >Generate</b-input-group-text>
                                        </b-input-group-prepend>
                                    <b-form-input v-model="item.code" />
                                </b-input-group>
                            </b-input-group>
                        </b-form-group>
                    </b-colxx>
                </b-row>
            </div>
            <b-button variant="primary" style="border-radius: 6px" class="float-right" @click="addSession">Add Video Page</b-button>
            <b-button variant="outline-primary" style="border-radius: 6px" class="mr-auto" @click="addNew = false">Cancel</b-button>
        </b-card>

        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?" centered>
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal('confirm_modal')">Cancel</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
import Axios from 'axios'
import { mapGetters } from 'vuex'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'

export default {
    metaInfo () {
        return { title: `Agenda Sessions` }
    },
    components: {
        VueTimepicker, Multiselect
    },
    data() {
        return {
            apiBase: '/agenda/video/',
            fields: [
                '#',
                { key: 'title', sortable: true },
                { key: 'code', sortable: true },
                { key: 'track', sortable: true },
                { key: 'description', sortable: true },
                { key: 'pageType', sortable: true },
                { key: 'status', sortable: true },
                { key: 'management', sortable: false}
            ],
            items: [],
            item: {
                id: 0,
                title: '',
                category_id: '',
                date: null,
                track_id: '',
                start: '',
                end: '',
                format: '',
                kind: '',
                link: '',
                isChat: 1,
                isNote: 1,
                code: null
            },
            categoryData: [],
            trackData: [],
            userData: [],
            liveCount: 0,
            recordedCount: 0,

            options: [],
            pageData: [],
            pageEnableData: [],
            notifyText: 'Successfully Created!',
            selectedItem: {},
            isConfirmed: false,
            addNew: false,
            format: ''
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        async init() {
            const { data } = await Axios.post(this.apiBase + 'get', {id: this.siteId})
            this.items = data.items
            this.trackData = data.trackData
            this.recordedCount = data.recordedCount
            this.liveCount = data.liveCount
            this.categoryData = data.categoryData
            this.userData = data.domain.user
            this.userData.forEach(data => {
                this.options.push(data.email)
            });
            this.options.push(data.domain.admin.email)
            this.codeGenerate()
        },
        addSession() {
            if (
                this.item.title == '' ||
                this.item.category_id == '' || 
                !this.item.date ||
                this.item.track_id == '' ||
                this.item.link == '' ||
                this.item.code == ''
            ) {
                this.$notify('primary filled', '', 'Please insert Required(*) fields!', { duration: 3000, permanent: false })
            } else {
                this.item.siteId = this.siteId
                this.item.date = Math.round(this.item.date.getTime()/1000)
                Axios.post(this.apiBase + 'set', this.item).then(res => {
                    if (res.status == 200) {
                        this.items = res.data
                        this.addNew = false
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                    }
                    this.notifyText = 'Successfully Created!'
                    this.item = {
                        id: 0,
                        title: '',
                        category_id: '',
                        date: null,
                        track_id: '',
                        start: '',
                        end: '',
                        format: '',
                        kind: '',
                        link: '',
                        isChat: 1,
                        isNote: 1,
                        code: null
                    }
                    this.codeGenerate()
                })
            }
        },
        edit(editItem) {
            this.notifyText = 'Successfully Changed!'
            var presenter = ''
            if (editItem.presenter) {
                presenter = JSON.parse(editItem.presenter)
            }
            var s = editItem.start.split(':')
            if (s[0] == 0) {s[0] = 12}
            if (s[0] > 12) {
                s[2] = 'PM'
                s[0] = s[0] - 12
            } else {
                s[2] = 'AM'
            }
            var e = editItem.end.split(':')
            if (e[0] == 0) {e[0] = 12}
            if (e[0] > 12) {
                e[2] = 'PM'
                e[0] = e[0] - 12
            } else {
                e[2] = 'AM'
            }
            this.item = {
                id: editItem.id,
                title: editItem.title,
                category_id: editItem.category_id,
                date: new Date(editItem.date),
                start: s[0] + ':' + s[1] + ':' + s[2],
                end: e[0] + ':' + e[1] + ':' + e[2],
                track_id: editItem.track_id,
                format: editItem.format,
                kind: editItem.kind,
                link: editItem.link,
                isChat: editItem.isChat,
                isNote: editItem.isNote,
                description: editItem.description,
                presenter: presenter,
                hashtag: editItem.hashtag,
                readMore: editItem.readMore,
                code: editItem.code
            }
            this.addNew = true
        },
        confirm() {
            this.notifyText = 'Successfully Deleted!'
            this.isConfirmed = true
            this.remove(this.selectedItem)
        },
        remove(item) {
            this.selectedItem = item
            if (!this.isConfirmed) {
                this.$refs['confirm_modal'].show()
            } else {
                Axios.post(this.apiBase + 'del', {id: this.selectedItem.id, siteId: this.siteId}).then(res => {
                    if (res.status == 200) {
                        this.items = res.data
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Server Error!', 'Successfully Deleted!', { duration: 3000, permanent: false })
                    }
                })
                this.isConfirmed = false
                this.$refs['confirm_modal'].hide()
            }
        },
        hideModal(refName) {
            this.$refs[refName].hide()
            this.selectedItem = {id: 0}
        },
        getStatus(item) {
            var currentTime = new Date().getTime()
            var startTime = Date.parse(item.date + ' ' + item.start)
            var endTime = Date.parse(item.date + ' ' + item.end)
            if (currentTime < startTime) {
                return 'On Deck'
            } else if (currentTime > endTime) {
                return 'Complete'
            } else {
                return 'Active'
            }
        },
        codeGenerate() {
            var code = String(Math.round(new Date().getTime() / 100000))
            this.item.code = code.substring(0, 4) + '-' + code.substring(4, 8)
        },
        addNewItem() {
            this.addNew = true
            this.item = {
                id: 0,
                title: '',
                session_id: '',
                isChat: 1,
                isNote: 1,
                link: '',
                code: ''
            }
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        })
    },
}
</script>

<style lang="css">
    .vue__time-picker, .vue__time-picker input.display-time {
        width: auto!important;
    }
    .custom-time, .custom-time input.display-time {
        width: 100%!important
    }
    .generate-btn:hover {
        background: #922c88;
        color: white;
        cursor: pointer;
    }
</style>>
