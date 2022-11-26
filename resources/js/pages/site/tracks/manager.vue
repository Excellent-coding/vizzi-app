<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <breadcrumb heading="Agenda Manager" />
            </b-colxx>
        </b-row>
        <b-card>
            <div class="d-md-flex justify-content-between align-items-center">
                <h2 class="my-auto mr-auto">Event Days</h2>
                <b-card class="mb-4 text-center mr-1 ml-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Total Event Days</h6>
                    <h2>{{items.length}}</h2>
                </b-card>
                <b-card class="mb-4 text-center mx-1">
                    <h6 class="card-text font-weight-semibold mb-0">Countdown to Start</h6>
                    <h2>{{countStart}}</h2>
                </b-card>
                <b-card class="mb-4 text-center ml-1 mr-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Countdown to End</h6>
                    <h2>{{countEnd}}</h2>
                </b-card>
                <div class="ml-auto my-auto">
                    <b-button
                        variant="primary"
                        v-b-modal.agenda_modal
                        style="border-radius: 6px"
                    >Add Day</b-button>
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
                    <template v-slot:cell(date)="data">
                        <div class="mt-2">{{data.item.date | moment('dddd, MMMM Do YYYY')}}</div>
                    </template>
                    <!-- <template v-slot:cell(timezone)="data">
                        <div class="mt-2">{{timeZoneJson[data.item.timezone].abbr}}</div>
                    </template> -->
                    <template v-slot:cell(eventStartTime)="data">
                        <div class="mt-2">{{data.item.date + ' ' + data.item.start | moment('h:mm A')}}</div>
                    </template>
                    <template v-slot:cell(eventEndTime)="data">
                        <div class="mt-2">{{data.item.date + ' ' + data.item.end | moment('h:mm A')}}</div>
                    </template>
                    <template v-slot:cell(status)="data">
                        <div class="mt-2">{{getStatus(data.item)}}</div>
                    </template>
                    <template v-slot:cell(location)="data">
                        <div class="mt-2">USA</div>
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
        <b-modal
            centered
            hide-header
            id="agenda_modal"
            ref="agenda_modal"
            size="lg"
        >
            <h2 class="text-center my-4">Add Event Day</h2>
            <hr>
            <div class="d-flex justify-content-between align-items-center my-2">
                <v-date-picker
                    v-model="event.date"
                    :popover="{ placement: 'bottom', visibility: 'click' }"
                    class="ml-auto mr-1 date-picker"
                    :input-props='{
                        placeholder: "Select Date"
                    }'
                />
                <vue-timepicker format="hh:mm:A"
                    placeholder="Start Time"
                    v-model="event.start"
                    class="mx-1"
                />
               <vue-timepicker format="hh:mm:A"
                    placeholder="End Time"
                    v-model="event.end"
                    class="mr-auto ml-1"
                />
            </div>
            <div class="text-center mt-2">
                <select v-model="event.category" class="select-class">
                    <option value="">--Select Category--</option>
                    <option :value="1">Default Category</option>
                </select>
                <select v-model="location" class="select-class">
                    <option value="">--Select Location--</option>
                    <option value="0">USA</option>
                </select>
            </div>
            <template slot="modal-footer">
                <div class="mx-auto">
                    <b-button variant="primary" style="border-radius: 6px" class="mx-1" @click="hideModal('agenda_modal')">Cancel</b-button>
                    <b-button variant="primary" style="border-radius: 6px" class="mx-1" @click="addDate">Add Date</b-button>
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
// import timeZoneJson from "../../../constants/timezone"
import Axios from 'axios'
import { mapGetters } from 'vuex'
import moment from 'vue-moment'

export default {
    metaInfo () {
        return { title: `Agenda Manager` }
    },
    components: {
        VueTimepicker
    },
    data() {
        return {
            apiBase: '/agenda/manager/',
            fields: [
                '#',
                { key: 'date', sortable: true },
                { key: 'eventStartTime', sortable: true },
                { key: 'eventEndTime', sortable: true },
                { key: 'status', sortable: true },
                { key: 'location', sortable: true },
                { key: 'management', sortable: false }
            ],
            items: [],
            event: {id: 0, category: '', date: null, start: '', end: ''},
            timeZoneJson: [],
            categoryData: [],
            notifyText: 'Successfully Created!',
            isConfirmed: false,
            selectedItem: {},
            location: 0,
            countStart: 0,
            countEnd: 0
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        })
    },
    mounted() {
        this.init()
    },
    methods: {
        init() {
            Axios.post(this.apiBase + 'get', {id: this.siteId}).then(res => {
                this.items = res.data.items
                this.categoryData = res.data.categoryData
                this.countStart = res.data.countStart
                this.countEnd = res.data.countEnd
            })
        },
        addDate() {
            var flag = false
            for (const [key, value] of Object.entries(this.event)) {
                if (value == '' && key != 'id') {
                    flag = true
                }
            }
            if (flag) {
                this.$notify('primary filled', '', 'Please insert All fields!', { duration: 3000, permanent: false })
            } else {
                this.event.siteId = this.siteId
                this.event.date = Math.round(this.event.date.getTime()/1000)
                Axios.post(this.apiBase + 'set', this.event).then(res => {
                    if (res.status == 200) {
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                        this.items = res.data
                    } else {
                        this.$notify('primary filled', 'Server Error!', 'Please try again a few hours later!', { duration: 3000, permanent: false })
                    }
                })
                this.notifyText = 'Successfully Created!'
                this.$refs.agenda_modal.hide()
            }
        },
        edit(item) {
            this.notifyText = 'Successfully Changed!'
            var s = item.start.split(':')
            if (s[0] == 0) {s[0] = 12}
            if (s[0] > 12) {
                s[2] = 'PM'
                s[0] = s[0] - 12
            } else {
                s[2] = 'AM'
            }
            var e = item.end.split(':')
            if (e[0] == 0) {e[0] = 12}
            if (e[0] > 12) {
                e[2] = 'PM'
                e[0] = e[0] - 12
            } else {
                e[2] = 'AM'
            }
            this.event = {
                id: item.id,
                date: new Date(item.date),
                start: s[0] + ':' + s[1] + ':' + s[2],
                end: e[0] + ':' + e[1] + ':' + e[2],
                category: item.id
            }
            this.$refs.agenda_modal.show()
        },
        confirm() {
            this.notifyText = 'Successfully Deleted!'
            this.isConfirmed = true
            this.remove(this.selectedItem)
        },
        async remove(item) {
            this.selectedItem = item
            if (!this.isConfirmed) {
                this.$refs['confirm_modal'].show()
            } else {
                const { data } = await Axios.post(this.apiBase + 'del', {id: this.selectedItem.id, siteId: this.siteId})
                this.items = data
                this.isConfirmed = false
                this.$refs['confirm_modal'].hide()
                this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
            }
        },
        hideModal(refName) {
            this.$refs[refName].hide()
            this.event = {id: 0}
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
        getTimeZoneOffSet(timezone) {
            var iso = new Date().toLocaleString('en-CA', { timezone, hour12: false }).replace(', ', 'T')
            iso += '.' + new Date().getMilliseconds().toString().padStart(3, '0');
            const lie = new Date(iso + 'Z');
            return -(lie - new Date()) / 60 / 1000;
        }
    }
}
</script>

<style>
    .date-picker input {
        height: 42px;
        background: #f4f6f8
    }
</style>

