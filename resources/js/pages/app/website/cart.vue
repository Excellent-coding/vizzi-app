<template>
<div>
    <b-row>
        <b-colxx xxs="12">
            <breadcrumb heading="Title Settings" />
            <div class="separator mb-5"></div>
        </b-colxx>
    </b-row>
    <b-row class="mb-2">
        <b-colxx lg="3" md="12"></b-colxx>
        <b-colxx lg="6" md="12">
            <b-card>
                <div class="d-flex justify-content-end align-items-center">
                    <b-form-group label="Title" class="w-100 mr-2">
                        <b-form-input v-model="item.title" />
                    </b-form-group>
                    <b-form-group label="Custom Title" class="w-100 mx-2">
                        <b-form-input v-model="item.customTitle" />
                    </b-form-group>
                    <b-button variant="outline-primary" class="ml-2 mt-2" size="sm" @click="add" v-if="currentIndex == -1">Add</b-button>
                    <template v-else>
                        <b-button variant="primary" class="ml-2 mr-1 mt-2" size="sm" @click="add">Edit</b-button>
                        <b-button variant="outline-primary" class="ml-1 mt-2" size="sm" @click="item = {}, currentIndex = -1">Cancel</b-button>
                    </template>
                </div>
            </b-card>
        </b-colxx>
        <b-colxx lg="3" md="12"></b-colxx>
    </b-row>
    <b-row v-if="titleData.length">
        <b-colxx md="6" sm="12">
            <vuetable class="table-divided order-with-arrow" ref="vuetable" :fields="fields" :data="titleData" :api-mode="false">
                <template slot="actions" slot-scope="data">
                    <div class="d-flex">
                        <b-button variant="outline-primary" class="mx-1" @click="edit(data)">Edit</b-button>
                        <b-button variant="primary" class="mx-1" @click="del(data.rowIndex)">Remove</b-button>
                    </div>
                </template>
            </vuetable>
        </b-colxx>
    </b-row>
    <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
        If you click confirm, you can't recover this data anymore!
        <template slot="modal-footer">
            <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
            <b-button variant="secondary" @click="hideModal()">Cancel</b-button>
        </template>
    </b-modal>
</div>

</template>

<script>
import Vuetable from "vuetable-2/src/components/Vuetable"
import Axios from 'axios'
import { mapGetters } from 'vuex'
export default {
    components: {
        Vuetable
    },
    data() {
        return {
            item: {
                title: '', customTitle: ''
            },
            fields: [
                {
                    name: "title",
                    sortField: "title",
                    title: "Origin Title",
                    titleClass: "",
                    dataClass: "list-item-heading",
                    width: "40%"
                },
                {
                    name: "customTitle",
                    sortField: "customTitle",
                    title: "Custom Title",
                    titleClass: "",
                    dataClass: "list-item-heading",
                    width: "40%"
                },
                {
                    name: "__slot:actions",
                    title: "",
                    titleClass: "center aligned text-right",
                    dataClass: "center aligned text-right",
                    width: "20%"
                }
            ],
            titleData: [],
            apiBase: '/site/title/',
            isConfirmed: false,
            currentIndex: -1
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        init() {
            Axios.post(this.apiBase + 'get', {id: this.siteId}).then(res => {
                this.titleData = JSON.parse(res.data)
            })
        },
        edit(item) {
            this.item = item.rowData
            this.currentIndex = item.rowIndex
        },
        confirm() {
            this.isConfirmed = true
            this.del(this.currentIndex)
        },
        del(index) {
            this.currentIndex = index
            if (this.isConfirmed) {
                this.$refs.confirm_modal.show()
            }
            this.titleData.splice(this.currentIndex, 1)
            this.set()
        },
        add() {
            if (this.currentIndex == -1) {
                this.titleData.push(this.item)
            } else {
                this.titleData[this.currentIndex] = this.item
            }
            this.set()
        },
        async set() {
            Axios.put(this.apiBase + 'set', {titleData: this.titleData, id: this.siteId})
            await this.$store.dispatch('site/setCustomName', this.titleData)
            this.currentIndex = -1
            this.isConfirmed = false
            this.item = {}
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        })
    }
}
</script>