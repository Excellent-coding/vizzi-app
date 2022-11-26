<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <b-header
                    title="User"
                    :actions="actions"
                    :selectAll="selectAll"
                    :isSelectedAll="isSelectedAll"
                    :isAnyItemSelected="isAnyItemSelected"
                    :setStatus="setStatus"
                    :keymap="keymap"
                />
            </b-colxx>
        </b-row>
        <b-row>
            <b-colxx xxs="12">
                <b-card class="mb-4" title="Users">
                    <b-table
                        hover
                        :items="users"
                        :fields="fields"
                    >
                        <template v-slot:cell(selected)="data">
                            <div @click.prevent="toggleItem($event, data.item.id)">
                                <b-form-checkbox
                                    :checked="selectedUsers.includes(data.item.id)"
                                    class="itemCheck mb-0"
                                />
                            </div>
                        </template>
                        <template v-slot:cell(#)="data">
                            {{ data.index + 1 }}
                        </template>
                        <template v-slot:cell(role)="data">
                            {{data.item.role == 3 ? 'Manager' : 'Client'}}
                        </template>
                        <template v-slot:cell(status)="status">
                            <b-badge class="mb-1" pill variant="primary" v-if="status">Active</b-badge>
                            <b-badge class="mb-1" pill variant="warning" v-else>Inactive</b-badge>
                        </template>
                    </b-table>
                </b-card>
            </b-colxx>
        </b-row>
        <!-- <b-add-modal :statuses="statuses" :newItem="newItem" :addUser="addUser" :isClose="isClose" /> -->
        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal()">Cancel</b-button>
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
                    <b-form-input type="email" v-model="newItem.email" />
                </b-form-group>
                <b-form-group :label="$t('phone')">
                    <b-form-input type="number" v-model="newItem.phone" />
                </b-form-group>
                <b-form-group label="Type">
                    <v-select v-model="newItem.role" :options="roles" />
                </b-form-group>
                <b-form-group :label="$t('verify-code')">
                    <b-input-group>
                        <b-input-group-prepend @click="generate()">
                            <b-input-group-text>Generate</b-input-group-text>
                        </b-input-group-prepend>
                        <b-form-input v-model="newItem.code" />
                    </b-input-group>
                </b-form-group>
                <b-form-group :label="$t('status')">
                    <b-form-radio-group stacked class="pt-2" :options="statuses" v-model="newItem.status" />
                </b-form-group>
            </b-form>
            <template slot="modal-footer">
                <b-button variant="outline-secondary" @click="hideModal('userModal')">{{ $t('cancel') }}</b-button>
                <b-button variant="primary" @click="addUser(newItem)" class="mr-1">{{ $t('submit') }}</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import axios from 'axios'
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"
import Header from "../../containers/Users/Header"
// import AddNewUserModal from "../../containers/Users/AddNewUserModal"

export default {
    components: {
        // "b-add-modal": AddNewUserModal,
        "b-header": Header,
        "v-select": vSelect
    },
    metaInfo () {
        return { title: `Users` }
    },
    data() {
        return {
            apiBase: '/users/',
            users: [],
            selectedUsers: [],
            statuses: [
                { text: 'Active', value: 1 },
                { text: 'Inactive', value: 0 }
            ],
            actions: [
                { text: 'Active', value: 1 },
                { text: 'Inactive', value: 0 },
                { text: 'Delete', value: 2 }
            ],
            newItem: { name: "", email: "", status: "", code: "", phone: '', role: '' },
            fields: [
                'selected',
                '#',
                { key: 'name', sortable: true },
                { key: 'email', sortable: true },
                { key: 'phone', sortable: true },
                { key: 'role', sortable: true },
                { key: 'status', sortable: true }
            ],
            isTrue: false,
            currentStatus: 0,
            roles: [
                { value: 3, label: "Manager" },
                { value: 4, label: "Client" }
            ]
        }
    },
    methods: {
        loadItems() {
            axios.post(this.apiBase + 'get').then(res => {
                this.users = res.data
            })
        },
        confirm() {
            this.isTrue = true
            this.setStatus(2)
        },
        hideModal() {
            this.$refs['confirm_modal'].hide()
        },
        setStatus(status) {
            if (status == 2 && !this.isTrue) {
                this.$refs['confirm_modal'].show()
            } else {
                axios.post(this.apiBase + 'status', {users: this.selectedUsers, status: status}).then(res => {
                    if (res.statusText == 'OK') {
                        var text = 'Successfully Changed!'
                        if (status == 2) {
                            var text = 'Successfully Deleted!'
                        }
                        this.$notify('primary filled', '', text, { duration: 3000, permanent: false })
                        this.users = res.data
                        this.isTrue = false
                        this.$refs['confirm_modal'].hide()
                    } else {
                        this.$notify('primary filled', 'Sorry!', 'Something went wrong', { duration: 3000, permanent: false })
                    }
                })
            }
        },
        addUser(item) {
            axios.post(this.apiBase + 'add', item).then(res => {
                if (res.statusText == 'OK') {
                    this.$notify('primary filled', '', 'Successfully Created!', { duration: 3000, permanent: false })
                    this.users = res.data
                    this.newItem = { name: "", email: "", password: "", status: "", code: "" }
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Something went wrong', { duration: 3000, permanent: false })
                }
                this.hideModal('userModal')
            })
        },
        selectAll(isToggle) {
            if (this.selectedUsers.length >= this.users.length) {
                if (isToggle) {
                this.selectedUsers = [];
                }
            } else {
                this.selectedUsers = this.users.map(x => x.id);
            }
        },
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                this.selectAll(false);
                break;
                case "undo":
                this.selectedUsers = [];
                break;
            }
        },
        isAnyItemSelected() {
            return (
                this.selectedUsers.length > 0 &&
                this.selectedUsers.length < this.users.length
            )
        },
        isSelectedAll() {
            return this.selectedUsers.length >= this.users.length;
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedUsers.length > 0) {
                let itemsForToggle = this.users;
                var start = this.getIndex(itemId, itemsForToggle, "id");
                var end = this.getIndex(
                    this.selectedUsers[this.selectedUsers.length - 1],
                    itemsForToggle,
                    "id"
                );
                itemsForToggle = itemsForToggle.slice(
                    Math.min(start, end),
                    Math.max(start, end) + 1
                );
                this.selectedUsers.push(
                    ...itemsForToggle.map(item => {
                        return item.id;
                    })
                );
            } else {
                if (this.selectedUsers.includes(itemId)) {
                    this.selectedUsers = this.selectedUsers.filter(x => x !== itemId);
                } else this.selectedUsers.push(itemId);
            }
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
  .input-group-text {
    cursor: pointer;
    background: #f4f6f8;
  }
  .input-group-text:hover {
    background: greenyellow;
  }
</style>