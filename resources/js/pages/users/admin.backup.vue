<template>
    <div>
        <b-row class="app-row survey-app">
            <b-colxx xxs="12">
                <b-header
                    title="Admin"
                    :actions="actions"
                    :selectAll="selectAll"
                    :isSelectedAll="isSelectedAll"
                    :isAnyItemSelected="isAnyItemSelected"
                    :setStatus="setStatus"
                    :keymap="keymap"
                />
            </b-colxx>
            <b-colxx xxs="12">
                <b-card class="mb-4" title="Admin Users">
                    <b-table
                        hover
                        :items="adminUsers"
                        :fields="fields"
                    >
                        <template v-slot:cell(#)="data">
                            <div @click.prevent="toggleItem($event, data.item.id)" class="mt-2">
                                <b-form-checkbox
                                    :checked="selectedAdminUsers.includes(data.item.id)"
                                    class="itemCheck mb-0"
                                />
                            </div>
                        </template>
                        <template v-slot:cell(name)="data">
                            <div class="mt-2">{{data.item.name}}</div>
                        </template>
                        <template v-slot:cell(email)="data">
                            <div class="mt-2">{{data.item.email}}</div>
                        </template>
                        <template v-slot:cell(accountStyle)="data">
                            <div class="mt-2" v-if="data.company">{{data.company}}</div>
                            <div class="mt-2" v-else>Individual</div>
                        </template>
                        <template v-slot:cell(status)="data">
                            <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status">Active</b-badge>
                            <b-badge class="mt-2" pill :variant="'danger'" v-else>Inactive</b-badge>
                        </template>
                        <template v-slot:cell(dateCreated)="data">
                            <div class="mt-2">{{ data.item.created_at | moment("dddd, MMMM Do YYYY") }}</div>
                        </template>
                        <template v-slot:cell(action)="data">
                            <b-button size="sm" variant="secondary" @click="viewUser(data.item)">View</b-button>
                            <b-button size="sm" variant="primary" @click="approveAll(data.item, 0)" v-if="status(data.item)">Deactive</b-button>
                            <b-button size="sm" variant="primary" @click="approveAll(data.item, 1)" v-else>Approve</b-button>
                        </template>
                    </b-table>
                </b-card>
            </b-colxx>
        </b-row>

        <todo-application-menu :items="adminUsers" :categories="categories" :labels="labels"></todo-application-menu>

        <b-modal
            ref="view_modal"
            title="Company / User Information"
            size="lg"
        >
            <h5 class="mb-2">Company</h5>
            <b-table stacked :items="[viewItem]" :fields="mainFields" />
            <h5 class="mt-4 mb-2">User</h5>
            <b-table striped :items="managers" :fields="userFields">
                <template v-slot:cell(status)="row">
                    <b-form-checkbox v-model="row.status" @change="setUserStatus(row)">Disable</b-form-checkbox>
                </template>
            </b-table>
            <template v-slot:modal-footer="{ cancel }">
                <b-button size="md" variant="primary" @click="approve()">
                    Approve
                </b-button>
                <b-button size="md" variant="danger" @click="cancel()">
                    Delete
                </b-button>
            </template>
        </b-modal>
        <b-modal
            id="userModal"
            ref="userModal"
            :title="$t('users.add-new-user')"
            modal-class="modal-center"
        >
            <b-form>
                <b-form-group :label="$t('name')">
                    <b-form-input v-model="newItem.name" />
                </b-form-group>
                <b-form-group :label="$t('email')">
                    <b-form-input v-model="newItem.email" />
                </b-form-group>
                <b-form-group :label="$t('verify-code')">
                    <b-input-group>
                    <b-input-group-prepend @click="generate()">
                        <b-input-group-text>Generate</b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input v-model="newItem.code" />
                    </b-input-group>
                </b-form-group>
            </b-form>
            <template slot="modal-footer">
                <b-button variant="outline-secondary" @click="hideModal('userModal')">{{ $t('cancel') }}</b-button>
                <b-button variant="primary" @click="addUser(newItem)" class="mr-1">{{ $t('submit') }}</b-button>
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
import axios from 'axios'
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"
import Header from "../../containers/Users/Header"
import TodoApplicationMenu from "../../containers/Users/TodoApplicationMenu"
import AddNewAdminModal from "../../containers/Users/AddNewAdminModal"

export default {
    components: {
        "todo-application-menu": TodoApplicationMenu,
        "b-add-modal": AddNewAdminModal,
        "b-header": Header,
        "v-select": vSelect
    },
    metaInfo () {
        return { title: `Admin Users` }
    },
    data() {
        return {
            apiBase: '/admin/',
            adminUsers: [],
            selectedAdminUsers: [],
            categories: ['Company', 'Individual'],
            labels: [],
            statuses: [
                { text: 'Active', value: 1 },
                { text: 'Inactive', value: 0 }
            ],
            actions: [
                { text: 'Active', value: 1 },
                { text: 'Inactive', value: 0 },
                { text: 'Delete', value: 2 }
            ],
            newItem: { name: "", email: "", password: "", status: "", code: "" },
            title: "Admin",
            managers: [],
            viewItem: [],
            mainFields: [ 'name', 'email', 'company', 'title', 'phone', 'address', 'zipcode' ],
            userFields: [ 'name', 'email', 'phone', 'status' ],
            users: [],
            adminId: 0,
            isConfirmed: false,
            fields: [
                '#',
                { key: 'name', sortable: true },
                { key: 'email', sortable: true },
                { key: 'accountStyle', sortable: true },
                { key: 'status', sortable: true },
                { key: 'dateCreated', sortable: true },
                { key: 'action', sortable: false }
            ]
        }
    },
    methods: {
        loadItems() {
            axios.post(this.apiBase + 'get').then(res => {
                this.adminUsers = res.data.adminData
                this.users = res.data.users
            })
        },
        status(item) {
            let flag = true
            if (!item.status) {
                flag = false
            } else {
                for (let i = 0; i < this.users.length; i++) {
                    if (this.users[i].parent === item.id) {
                        if (item.status == 0) {
                            flag = false
                        }
                    }
                }
            }
            return flag
        },
        setStatus(status) {
            if (status == 2 && !this.isConfirmed) {
                this.$refs['confirm_modal'].show()
            } else {
                axios.post(this.apiBase + 'status', {users: this.selectedAdminUsers, status: status}).then(res => {
                    if (res.statusText == 'OK') {
                        var text = 'Successfully Changed!'
                        if (status == 2) {
                            text = 'Successfully Deleted!'
                        }
                    }
                    this.$notify('primary filled', '', text, { duration: 3000, permanent: false });
                    this.adminUsers = res.data
                    this.isConfirmed = false
                    this.$refs['confirm_modal'].hide()
                })
            }
        },
        addUser(item) {
            axios.post(this.apiBase + 'add', item).then(res => {
                this.adminUsers = res.data
                this.newItem = { name: "", email: "", password: "", status: "", code: "" }
                this.$notify('primary filled', '', 'Successfully Done!', { duration: 3000, permanent: false })
                this.$refs['userModal'].hide()
            })
        },
        confirm() {
            this.isConfirmed = true
            this.setStatus(2)
        },
        viewUser(item) {
            this.adminId = item.id
            this.managers = []
            for (let i = 0; i < this.users.length; i++) {
                let parentId = this.users[i].parent
                if (parentId === item.id) {
                    this.managers.push(this.users[i])
                }
            }
            this.viewItem = item
            this.$refs['view_modal'].show()
        },

        approve() {
            axios.post(this.apiBase + 'set', {users: this.managers, id: this.adminId, status: 1}).then(res => {
                this.managers = res.data
                this.$refs['view_modal'].hide()
                this.$notify('primary filled', '', 'Successfully Done!', { duration: 3000, permanent: false });
            })
        },
        approveAll(item, status) {
            this.adminId = item.id
            this.managers = []
            for (let i = 0; i < this.users.length; i++) {
                let parentId = this.users[i].parent
                if (parentId === item.id) {
                    this.managers.push(this.users[i])
                }
            }
            axios.post(this.apiBase + 'set', {users: this.managers, id: this.adminId, status: status}).then(res => {
                this.managers = res.data.users
                this.adminUsers = res.data.adminUsers
                this.$refs['view_modal'].hide()
                this.$notify('primary filled', '', 'Successfully Done!', { duration: 3000, permanent: false });
            })
        },
        cancel() {
        },

        selectAll(isToggle) {
            if (this.selectedAdminUsers.length >= this.adminUsers.length) {
                if (isToggle) {
                this.selectedAdminUsers = [];
                }
            } else {
                this.selectedAdminUsers = this.adminUsers.map(x => x.id);
            }
        },
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                this.selectAll(false);
                break;
                case "undo":
                this.selectedAdminUsers = [];
                break;
            }
        },
        isAnyItemSelected() {
            return (
                this.selectedAdminUsers.length > 0 &&
                this.selectedAdminUsers.length < this.adminUsers.length
            )
        },
        isSelectedAll() {
            return this.selectedAdminUsers.length >= this.adminUsers.length;
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedAdminUsers.length > 0) {
                let itemsForToggle = this.adminUsers;
                var start = this.getIndex(itemId, itemsForToggle, "id");
                var end = this.getIndex(
                    this.selectedAdminUsers[this.selectedAdminUsers.length - 1],
                    itemsForToggle,
                    "id"
                );
                itemsForToggle = itemsForToggle.slice(
                    Math.min(start, end),
                    Math.max(start, end) + 1
                );
                this.selectedAdminUsers.push(
                    ...itemsForToggle.map(item => {
                        return item.id;
                    })
                );
            } else {
                if (this.selectedAdminUsers.includes(itemId)) {
                    this.selectedAdminUsers = this.selectedAdminUsers.filter(x => x !== itemId);
                } else this.selectedAdminUsers.push(itemId);
            }
        },
        setUserStatus(item) {
            if(item.status == undefined) {
                item.status = false
            }
            this.managers[item.index] = {id: item.item.id, disable: !item.status}
        },
        hideModal(refname) {
            this.$refs[refname].hide();
        },
        generate() {
            this.newItem.code = Math.round(Math.random() * 1000000)
        }
    },
    mounted() {
        this.loadItems()
    }
}
</script>

<style lang="css">
    .table.b-table.b-table-stacked > tbody > tr > [data-label]::before {
        text-align: left;
    }
    .input-group-text {
        cursor: pointer;
        background: #f4f6f8;
    }
    .input-group-text:hover {
        background: greenyellow;
    }
</style>