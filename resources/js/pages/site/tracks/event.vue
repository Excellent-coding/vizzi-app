<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <breadcrumb heading="Video Page Manager" />
            </b-colxx>
        </b-row>
        <b-card>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="my-auto mr-auto">Video Pages</h2>
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
                        v-b-modal.agenda_modal
                        style="border-radius: 6px"
                    >Add Page</b-button>
                </div>
            </div>
            <div class="table-responsive">
                <b-table
                    hover
                    striped
                    :fields="fields"
                    :items="items"
                > 
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
            <h2 class="text-center">Add Video Page</h2>
            <hr>
            <div class="d-flex justify-content-between align-items-center my-2">
                <v-date-picker
                    v-model="pageItem.date"
                    :popover="{ placement: 'bottom', visibility: 'click' }"
                    class="ml-auto mr-1"
                >
                    <button class="select-btn">
                        <i class="simple-icon-calendar mr-2"></i>
                        Select Date
                    </button>
                </v-date-picker>
                <vue-timepicker format="hh:mm:A"
                    placeholder="Start Time"
                    v-model="pageItem.start"
                    class="mx-1"
                />
               <vue-timepicker format="hh:mm:A"
                    placeholder="End Time"
                    v-model="pageItem.end"
                    class="mr-auto ml-1"
                />
            </div>
            <div class="text-center mt-2">
                <select v-model="pageItem.timezone" class="select-class">
                    <option value="">--Select Your Timezone--</option>
                    <option :value="index" v-for="(timezone, index) in timeZoneJson" :key="index">{{timezone.text}}</option>
                </select>
                <select v-model="pageItem.category" class="select-class">
                    <option value="">--Select Category--</option>
                </select>
                <select v-model="pageItem.location" class="select-class">
                    <option value="">--Select Location--</option>
                </select>
            </div>
            <template slot="modal-footer">
                <div class="mx-auto">
                    <b-button variant="primary" style="border-radius: 6px" class="mx-1" @click="hideModal('agenda_modal')">Cancel</b-button>
                    <b-button variant="primary" style="border-radius: 6px" class="mx-1" @click="addPage">Add Video</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
import timeZoneJson from "../../../constants/timezone";

export default {
    metaInfo () {
        return { title: `Video Page Manager` }
    },
    components: {
        VueTimepicker
    },
    data() {
        return {
            apiBase: '/agenda/',
            fields: [
                '#',
                { key: 'date', sortable: true },
                { key: 'timezone', sortable: true },
                { key: 'eventStartTime', sortable: true },
                { key: 'eventEndTime', sortable: true },
                { key: 'status', sortable: true },
                { key: 'location', sortable: true },
                { key: 'management', sortable: false }
            ],
            items: [],
            pageItem: {
                date: new Date(),
                start: '',
                end: '',
                timezone: '',
                category: '',
                location: ''
            },
            timeZoneJson
        }
    },
    methods: {
        addPage() {
        },
        hideModal(refName) {
            this.$refs[refName].hide()
        }
    }
}
</script>
