<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <breadcrumb heading="Agenda Tracks" />
            </b-colxx>
        </b-row>
        <b-card>
            <div class="d-md-flex justify-content-between align-items-center">
                <h2 class="my-auto mr-auto">Event Tracks</h2>
                <b-card class="mb-4 text-center mr-1 ml-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Total Event Tracks</h6>
                    <h2>{{items.length}}</h2>
                </b-card>
                <b-card class="mb-4 text-center mx-1">
                    <h6 class="card-text font-weight-semibold mb-0">Sessions Per Track 1</h6>
                    <h2>{{count}}</h2>
                </b-card>
                <b-card class="mb-4 text-center ml-1 mr-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Track Color Code</h6>
                    <h2></h2>
                </b-card>
                <div class="ml-auto my-auto">
                    <b-button
                        variant="primary"
                        v-b-modal.agenda_modal
                        style="border-radius: 6px"
                    >Add Track </b-button>
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
                    <template v-slot:cell(trackTitle)="data">
                        <div class="mt-2 text-capitalize">{{data.item.title}}</div>
                    </template>
                    <template v-slot:cell(trackDescription)="data">
                        <div class="mt-2">{{data.item.description}}</div>
                    </template>
                    <template v-slot:cell(trackColor)="data">
                        <div class="mt-2 w-100" :style="'background:' + data.item.color + ';height:1rem'" />
                    </template>
                    <template v-slot:cell(trackCategory)="data">
                        <template v-if="data.item.category">
                            <div class="mt-2" v-if="data.item.category.name">{{data.item.category.name}}</div>
                            <div class="mt-2" v-else>{{data.item.category}}</div>
                        </template>
                        <template v-else>
                            <div class="mt-2">Not Set</div>
                        </template>
                    </template>
                    <template v-slot:cell(status)="data">
                        <b-badge pill :variant="'primary'" class="mt-2" v-if="data.item.status">Active</b-badge>
                        <b-badge pill :variant="'danger'" class="mt-2" v-else>Inactive</b-badge>
                    </template>
                    <template v-slot:cell(location)="data"><div class="mt-2">USA</div></template>
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
            title="Add Event Track"
            size="lg"
        >
            <h2 class="text-center my-4">Add Event Track</h2>
            <hr>
            <b-row class="mx-4">
                <b-colxx xxs="6">
                    <b-form-group label="Track Title" class="my-2">
                        <b-form-input v-model="trackItem.title" />
                    </b-form-group>
                    <b-form-group label="Track Category" class="my-2">
                        <b-form-input v-model="trackItem.category" />
                    </b-form-group>
                    <b-form-group label="Track Color" class="my-2">
                        <b-form-input type="color" v-model="trackItem.color" />
                    </b-form-group>
                </b-colxx>
                <b-colxx xxs="6">
                    <b-form-group label="Track Description" class="my-2">
                        <b-form-textarea v-model="trackItem.description" />
                    </b-form-group>
                    <b-form-group label="Track Hashtag" class="my-2">
                        <b-form-textarea v-model="trackItem.hashtag" />
                    </b-form-group>
                </b-colxx>
            </b-row>
            <template slot="modal-footer">
                <div class="mx-auto">
                    <b-button variant="primary" style="border-radius: 6px" class="mx-1" @click="hideModal('agenda_modal')">Cancel</b-button>
                    <b-button variant="primary" style="border-radius: 6px" class="mx-1" @click="addTrack">Add Track</b-button>
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

import Axios from 'axios'
import { mapGetters } from 'vuex'

export default {
    metaInfo () {
        return { title: `Agenda Tracks` }
    },
    data() {
        return {
            apiBase: '/agenda/track/',
            fields: [
                '#',
                { key: 'trackTitle', sortable: true },
                { key: 'trackDescription', sortable: true },
                { key: 'trackColor', sortable: true },
                { key: 'trackCategory', sortable: true },
                { key: 'status', sortable: true },
                { key: 'location', sortable: true },
                { key: 'management', sortable: false }
            ],
            items: [],
            trackItem: {
                id: 0,
                title: '',
                category: '',
                color: '#922c88',
                description: '',
                hashtag: '',
                siteId: 0
            },
            selectedItem: {},
            isConfirmed: false,
            notifyText: 'Successfully Saved!',
            count: 0
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
                this.count = res.data.count
            })
        },
        async addTrack() {
            var flag = false
            for (const [key, value] of Object.entries(this.trackItem)) {
                if (value == '' && key != 'id' && key != 'siteId') {
                    flag = true
                }
            }
            if (flag) {
                this.$notify('primary filled', '', 'Please insert All fields!', { duration: 3000, permanent: false })
            } else {
                this.trackItem.siteId = this.siteId
                const { data } = await Axios.post(this.apiBase + 'set', this.trackItem)
                this.items = data
                this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                this.notifyText = 'Successfully Saved!'
                this.$refs['agenda_modal'].hide()
                this.trackItem = {id: 0}
            }
        },
        edit(item) {
            this.selectedItem = item
            this.notifyText = 'Successfully Changed!'
            this.trackItem = this.selectedItem
            if (this.selectedItem.category && this.selectedItem.category.name) {
                this.trackItem.category = this.selectedItem.category.name
            }
            this.$refs['agenda_modal'].show()
        },
        confirm() {
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
            }
        },
        hideModal(refName) {
            this.$refs[refName].hide()
            this.trackItem = {id: 0}
        }
    }
}
</script>
