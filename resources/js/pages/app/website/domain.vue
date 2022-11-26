<template>
    <b-row>
        <b-header
            :title="title"
            :selectAll="selectAll"
            :isSelectedAll="isSelectedAll"
            :keymap="keymap"
            :isAnyItemSelected="isAnyItemSelected"
            :setStatus="setStatus"
            :status="status"
            :initData="initData"
            type="modal"
        />

        <b-colxx xxs="12" class="mb-3" v-for="(item, index) in domains" :key="index" :id="item.id">
            <b-card :class="{'d-flex flex-row':true,'active' : selecteDomains.includes(item.id)}" no-body>
                <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                    <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event, item.id)" >
                        <b-form-checkbox :checked="selecteDomains.includes(item.id)" class="itemCheck mb-0" />
                    </div>
                    <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                        <p class="list-item-heading truncate my-1">{{item.title}}</p>
                        <router-link :to="`?p=${item.id}`" class="w-20 w-sm-100 my-1">
                            <p class="list-item-heading truncate mb-0">{{item.domain}}.vizzi.live</p>
                        </router-link>
                        <p class="text-muted text-small w-35 w-sm-100 my-1">{{item.created_at | moment('dddd, MMMM Do YYYY')}}</p>
                        <div class="w-10 w-sm-100 my-1">
                            <b-badge pill :variant="'primary'" v-if="item.status == 1">Active</b-badge>
                            <b-badge pill :variant="'danger'" v-else>Inactive</b-badge>
                        </div>
                        <div class="my-auto mr-2 d-none d-sm-block">
                            <button type="button" class="btn btn-primary mb-1" @click="viewSite(item.domain)">View</button>
                            <button type="button" class="btn btn-outline-primary mb-1" @click="setHost(item)">Setting</button>
                            <button v-b-modal.sendModal class="btn btn-outline-primary mb-1" v-if="users.length" @click="getItem(item)">Send Links</button>
                            <b-dropdown variant="primary" right text="Upload" class="mb-1" v-else> 
                                <b-dropdown-item href="/data/sample.xlsx" download>
                                    Download Sample csv
                                </b-dropdown-item>
                                <label for="file" class="dropdown-item">Upload your csv</label>
                            </b-dropdown>
                        </div>
                        <b-dropdown variant="primary" size="sm" block right text="Action" class="d-block d-sm-none">
                            <b-dropdown-item @click="viewSite(item.domain)">View</b-dropdown-item>
                            <b-dropdown-item @click="setHost(item)">Setting</b-dropdown-item>
                            <b-dropdown-item v-if="users.length" @click="getItem(item)">Send Links</b-dropdown-item>
                            <template v-else>
                                <b-dropdown-item href="/data/sample.xlsx" download>Download Sample csv</b-dropdown-item>
                                <label for="file" class="dropdown-item">Upload your csv</label>
                            </template>
                        </b-dropdown>
                    </div>
                </div>  
            </b-card>
        </b-colxx>

        <b-modal
            id="add_modal"
            ref="add_modal"
            title="Create A New Event"
            modal-class="modal-right"
        >
            <b-form>
                <b-form-group label="Event Title">
                    <b-form-input v-model="newItem.title" />
                </b-form-group>
                <b-form-group :label="$t('menu.domain-name')">
                    <b-input-group>
                        <b-input-group-prepend><b-input-group-text>https://</b-input-group-text></b-input-group-prepend>
                        <b-form-input v-model="newItem.domain" />
                        <b-input-group-append><b-input-group-text>.vizzi.live</b-input-group-text></b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-form>

            <template slot="modal-footer">
                <b-button
                    variant="outline-secondary"
                    @click="hideModal()"
                >{{ $t('cancel') }}</b-button>
                <b-button variant="primary" @click="addNewItem(newItem)" class="mr-1">{{ $t('submit') }}</b-button>
            </template>
        </b-modal>
        <b-send-modal :title="url" :users="users" :id="itemId" />

        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal()">Cancel</b-button>
            </template>
        </b-modal>

        <input type="file" @change="onFileChange" id="file">
    </b-row>
</template>

<script>
import axios from "axios";
import { mapGetters } from 'vuex'

import Header from "../../../containers/Web/DomainHeader"
import AddModal from '../../../components/Pages/AddDomainModal'
import SendModal from '../../../components/Pages/SendModal'

export default {
    components: {
        "b-header": Header,
        'b-add-modal': AddModal,
        'b-send-modal': SendModal
    },
    metaInfo () {
        return { title: `Vizzi Domain` }
    },
    data() {
        return {
            apiBase: '/domain/',
            domains: [],
            selecteDomains: [],
            title: 'Event Manager',
            status: ['Active', 'Inactive', 'Delete'],
            users: [],
            itemUsers: [],
            url: '',
            itemId: null,
            isConfirmed: false,
            newItem: {
                title: "",
                domain: ""
            }
        }
    },
    methods: {
        loadItems() {
            axios.post(this.apiBase + 'get', {id: this.siteId}).then(res => {
                if (res.data.domains) {
                    this.domains = res.data.domains
                } else if (res.data.domain) {
                    this.domains[0] = res.data.domain
                }
                this.users = res.data.users
                this.selecteDomains = []
            })
        },
        selectAll(isToggle) {
            if (this.selecteDomains.length >= this.domains.length) {
                if (isToggle) this.selecteDomains = [];
            } else {
                this.selecteDomains = this.domains.map(x => x.id);
            }
        },
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                break;
                case "undo":
                    this.selecteDomains = [];
                break;
            }
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selecteDomains.length > 0) {
                let itemsForToggle = this.items;
                var start = this.getIndex(itemId, itemsForToggle, "id");
                var end = this.getIndex(
                    this.selecteDomains[this.selecteDomains.length - 1],
                    itemsForToggle,
                    "id"
                );
                itemsForToggle = itemsForToggle.slice(
                    Math.min(start, end),
                    Math.max(start, end) + 1
                );
                this.selecteDomains.push(
                    ...itemsForToggle.map(item => {
                        return item.id;
                    })
                );
            } else {
                if (this.selecteDomains.includes(itemId)) {
                    this.selecteDomains = this.selecteDomains.filter(x => x !== itemId);
                } else this.selecteDomains.push(itemId);
            }
        },
        setStatus(status) {
            if (status == 2 && !this.isConfirmed) {
                this.$refs['confirm_modal'].show()
            } else {
                axios.put(this.apiBase + 'status', {status: status, items: this.selecteDomains}).then(res => {
                    this.loadItems()
                    this.$refs['confirm_modal'].hide()
                    this.isConfirmed = false
                })
            }
        },
        hideModal() {
            this.$refs['confirm_modal'].hide()
            this.$refs['add_modal'].hide()
        },
        confirm() {
            this.isConfirmed = true
            this.setStatus(2)
        },
        addNewItem(item) {
            if (item.title == '' || item.domain == '') {
                this.$notify('primary filled', 'Please Fill the Blank Fields', '', { duration: 3000, permanent: false });
            } else {
                axios.post(this.apiBase + 'set', {title: item.title, domain: item.domain}).then(res => {
                    if (res.data) {
                        if (res.data.errors) {
                            this.$notify('primary filled', 'Sorry!', res.data.errors.domain[0], { duration: 3000, permanent: false });
                        } else {
                            this.$notify('primary filled', 'Event Successfully Created', '', { duration: 3000, permanent: false });
                            this.domains = res.data
                            this.selecteDomains = []
                            this.$refs['add_modal'].hide()
                        }
                    }
                })
            }
        },
        viewSite(domain) {
            window.open('http://'+ domain + '.vizzi.live')
        },
        async setHost(item) {
            if (this.user.role == 1 || (this.user.role == 2 && item.status == 1)) {
                this.$store.dispatch('site/saveSite', item ).then(
                    this.$router.push(item.domain + '/menus')
                )
            } else {
                this.$notify('primary filled', 'Sorry!', 'Action is not allowed!', { duration: 3000, permanent: false });
            }
        },
        getItem(item) {
            this.itemId = item.id
            this.url = item.domain
        },
        initData() {},
        onFileChange(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length) return;
            this.createInput(files[0]);
        },
        createInput(file) {
            var reader = new FileReader();
            reader.onload = (e) => {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, {type: 'array'});
                let sheetName = workbook.SheetNames[0]
                let worksheet = workbook.Sheets[sheetName];
                this.submit(worksheet);
            }
            reader.readAsArrayBuffer(file);
        },
        submit(file) {
            let no = 1
            let adminData = {}
            while(file['B' + no]) {
                if (file['D' + no]) {
                adminData[file['B' + no].v.toLowerCase()] = file['D' + no].v
                }
                no++
            }

            let userData = []
            no = no + 1
            const field_1 = file['C' + no].v.toLowerCase()
            const field_2 = file['D' + no].v.toLowerCase()
            const field_3 = file['E' + no].v.toLowerCase()
            no = no + 1
            while(file['C' + no]) {
                let data = {}
                data[field_1] = file['C' + no].v
                data[field_2] = file['D' + no].v
                data[field_3] = file['E' + no].v
                userData.push(data)
                no++
            }

            if (adminData['email'] != this.user.email) {
                this.$notify('primary filled', '', 'Admin Email doesn\'t match, please use your current Email', { duration: 3000, permanent: false })
            } else {
                axios.post(this.apiBase + 'csv-register', {adminData: adminData, userData: userData, id: this.itemId}).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', 'We are validating your data!', 'Once complete you will receive confirmantion by email.', { duration: 10000, permanent: false })
                        this.users = res.data
                    } else {
                        this.$notify('primary filled', 'Sorry!', 'Something went wrong!, please try again', { duration: 3000, permanent: false })
                    }
                })
            }
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            user: 'auth/user'
        }),
        isSelectedAll() {
            return this.selecteDomains.length >= this.domains.length;
        },
        isAnyItemSelected() {
            return (
                this.selecteDomains.length > 0 &&
                this.selecteDomains.length < this.domains.length
            );
        },
        host() {
            return window.location.host;
        }
    },
    mounted() {
        this.loadItems();
    }
};
</script>

<style lang="css">
    input[type="file"] {
        width: .1px;
        height: .1px;
        opacity: 0;
        position: absolute;
    }
</style>
