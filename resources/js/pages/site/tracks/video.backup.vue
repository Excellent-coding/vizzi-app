<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <breadcrumb heading="Agenda Videos" />
            </b-colxx>
        </b-row>

        <b-card v-if="!addNew">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="my-auto mr-auto">Pages</h2>
                <b-card class="mb-4 text-center mr-1 ml-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Total Video Pages</h6>
                    <h2>3</h2>
                </b-card>
                <b-card class="mb-4 text-center mx-1">
                    <h6 class="card-text font-weight-semibold mb-0">Live Streaming Pages</h6>
                    <h2>3</h2>
                </b-card>
                <b-card class="mb-4 text-center ml-1 mr-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Pre-Record Pages</h6>
                    <h2>3</h2>
                </b-card>
                <div class="ml-auto my-auto">
                    <b-button
                        variant="primary"
                        style="border-radius: 6px"
                        @click="addNew = true"
                    >Add Video</b-button>
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
                    <template v-slot:cell(sessionTitle)="data">
                        <div class="mt-2">{{data.item.title}}</div>
                    </template>
                    <template v-slot:cell(sessionDescription)="data">
                        <div class="mt-2">{{data.item.description}}</div>
                    </template>
                    <template v-slot:cell(sessionTime)="data">
                        <div class="mt-2">
                            {{data.item.date | moment('dddd, MMMM Do YYYY')}}
                            <p class="mb-0">{{data.item.date + ' ' + data.item.start | moment('h:mm A')}} ~ {{data.item.date + ' ' + data.item.end | moment('h:mm A')}}</p> 
                        </div>
                    </template>
                    <template v-slot:cell(track)="data">
                        <div class="mt-2 w-100" :style="'background:' + data.item.track.color + ';height:1rem'" />
                    </template>
                    <template v-slot:cell(status)="data">
                        <div class="mt-2">{{getStatus(data.item)}}</div>
                    </template>
                    <template v-slot:cell(pageType)="data">
                        <div class="mt-2" style="text-transform: capitalize">{{data.item.page.type}}</div>
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
                <b-row>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Video Title*" class="my-2">
                            <b-form-input v-model="item.title" />
                        </b-form-group>
                        <b-form-group label="Video Category*" class="my-2">
                            <b-form-select v-model="item.category_id">
                                <option :value="data.id" v-for="data in categoryData" :key="data.id">
                                    {{data.name}}
                                </option>
                            </b-form-select>
                        </b-form-group>
                        <b-row>
                            <b-colxx sm="6">
                                <b-form-group label="Video Day*" class="my-2">
                                    <v-date-picker
                                        v-model="item.date"
                                    />
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="Video Track*" class="my-2">
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
                            <b-colxx sm="6">
                                <b-form-group label="Active Button Time" class="my-2">
                                    <b-form-input />
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="Video Style" class="my-2">
                                    <b-form-input />
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
                        <b-form-group label="Tag Presenters" class="my-2">
                            <b-form-input v-model="item.presenter" />
                        </b-form-group>
                    </b-colxx>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Page Format" class="my-2">
                            <b-form-select v-model="format">
                                <option :value="type" :key="index" v-for="(type, index) in typeData">
                                    {{type}}
                                </option>
                            </b-form-select>
                        </b-form-group>
                    </b-colxx>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Link to Existing Page" class="my-2">
                            <b-form-select v-model="item.page_id">
                                <option :value="data.id" :key="index" v-for="(data, index) in pageEnableData">
                                    {{data.url}}
                                </option>
                            </b-form-select>
                        </b-form-group>
                    </b-colxx>
                </b-row>
                <p class="text-muted">
                    Video Page, Live Session, Expo page, Exhibit, Networking
                </p>
            </div>
            <b-button variant="primary" style="border-radius: 6px" class="float-right" @click="addSession">Add Session</b-button>
            <b-button variant="outline-primary" style="border-radius: 6px" class="mr-auto" @click="addNew = false">Cancel</b-button>
        </b-card>

        <b-modal
            centered
            hide-header
            id="agenda_modal"
            ref="agenda_modal"
            title="Add Event Track"
            size="lg"
        >
            <h2 class="text-center">Add Event Day</h2>
            <hr>
            <b-row>
                <b-colxx md="6" sm="12">
                    <b-form-group label="Session Title" class="my-2">
                        <b-form-input v-model="sessionItem.title" />
                    </b-form-group>
                    <b-form-group label="Session Time" class="my-2">
                        <v-date-picker
                            v-model="sessionItem.date"
                            :input-props='{
                                placeholder: "Select Date"
                            }'
                        />
                        <vue-timepicker format="hh:mm:A"
                            placeholder="Start Time"
                            v-model="sessionItem.start"
                            class="my-2 custom-time"
                        />
                        <vue-timepicker format="hh:mm:A"
                            placeholder="End Time"
                            v-model="sessionItem.end"
                            class="end-time custom-time my-2"
                        />
                    </b-form-group>
                    <b-form-group label="Session Track" class="my-2">
                        <b-select v-model="sessionItem.track_id">
                            <b-select-option :value="data.id" v-for="data in trackData" :key="data.id">{{data.title}}</b-select-option>
                        </b-select>
                    </b-form-group>
                </b-colxx>
                <b-colxx md="6" sm="12">
                    <b-form-group label="Session Page Type" class="my-2">
                        <b-select v-model="sessionItem.type">
                            <b-select-option :value="data" v-for="(data, index) in pageData" :key="index">{{data}}</b-select-option>
                        </b-select>
                    </b-form-group>
                    <b-form-group label="Session Descripition" class="my-2">
                        <b-form-textarea v-model="sessionItem.description" />
                    </b-form-group>
                </b-colxx>
            </b-row>
            <template slot="modal-footer">
                <div class="mx-auto">
                    <b-button variant="primary" style="border-radius: 6px" class="mx-1" @click="hideModal('agenda_modal')">Cancel</b-button>
                    <b-button variant="primary" style="border-radius: 6px" class="mx-1" @click="addSession">Add Session</b-button>
                </div>
            </template>
        </b-modal>

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

export default {
    metaInfo () {
        return { title: `Agenda Sessions` }
    },
    components: {
        VueTimepicker
    },
    data() {
        return {
            apiBase: '/agenda/video/',
            fields: [
                '#',
                { key: 'sessionTitle',          sortable: true },
                { key: 'sessionDescription',    sortable: true },
                { key: 'sessionTime',           sortable: true },
                { key: 'Track',                 sortable: true },
                { key: 'status',                sortable: true },
                { key: 'pageType',              sortable: true },
                { key: 'management',            sortable: false}
            ],
            items: [],
            sessionItem: {
                id: 0
            },
            categoryData: [],
            trackData: [],
            pageData: [],
            pageEnableData: [],
            typeData: [],
            notifyText: 'Successfully Created!',
            selectedItem: {},
            isConfirmed: false,
            addNew: false,
            item: {id: 0},
            format: ''
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        async init() {
            const { data } = await Axios.post(this.apiBase + 'get', {id: this.siteId});
            this.items = data.items
            this.categoryData = data.categoryData
            this.trackData = data.trackData
            this.pageData = data.pageData
            this.typeData = data.typeData
        },
        addSession() {
            if (
                this.item.title == '' ||
                this.item.category_id == '' ||
                this.item.date == '' ||
                this.item.track_id == ''
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
                    this.item = {}
                })
            }
        },
        edit(editItem) {
            this.notifyText = 'Successfully Changed!'
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
                track_id: editItem.track_id,
                category_id: editItem.category_id,
                start: s[0] + ':' + s[1] + ':' + s[2],
                end: e[0] + ':' + e[1] + ':' + e[2],
                date: new Date(editItem.date),
                type: editItem.type,
                presenter: editItem.presenter,
                hashtag: editItem.hashtag,
                page_id: editItem.page_id,
                description: editItem.description
            }
            this.format = editItem.page.type
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
        }
    },
    watch: {
        format(val) {
            this.pageEnableData = []
            this.pageData.forEach(page => {
                if (page.type == val) {
                    this.pageEnableData.push(page)
                }
            });
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
</style>>
