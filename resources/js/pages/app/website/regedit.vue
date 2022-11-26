<template>
    <div>
        <b-header
            title="Attendee Settings"
            :selectAll="selectAll"
            :isSelectedAll="isSelectedAll"
            :keymap="keymap"
            :isAnyItemSelected="isAnyItemSelected"
            :setStatus="setStatus"
            :initData="initData"
            :status="status"
            type="modal"
        />
        <div class="container">
            <b-tabs card v-model="tabIndex">
                <b-tab title="Form Setting">
                    <b-row>
                        <b-colxx md="6" xs="12" class="mb-3">
                            <b-card class="mb-4" title="Required Fields">
                                <div class="d-flex mb-2">
                                    <b-form-checkbox disabled />
                                    <div class="my-auto">Email*</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <b-form-checkbox disabled />
                                    <div class="my-auto">Password*</div>
                                </div>
                                <template v-for="(item, index) in items">
                                    <div class="d-flex mb-2" :key="index" v-if="item.required" @click.prevent="toggleItem($event, index)" >
                                        <b-form-checkbox :checked="selectedItems.includes(index)" class="itemCheck mb-auto" />
                                        <div style="margin-top: -5px" v-if="item.disabled">
                                            <hr style="border-bottom: 1px solid black; margin-bottom: -10px">{{getLabel(item)}}
                                        </div>
                                        <div class="my-auto" v-else>{{getLabel(item)}}</div>
                                    </div>
                                </template>
                            </b-card>
                        </b-colxx>
                        <b-colxx md="6" xs="12" class="mb-3">
                            <b-card class="mb=4" title="Optional Fields">
                                <template v-for="(item, index) in items">
                                    <div class="d-flex mb-2" :key="index" v-if="!item.required" @click.prevent="toggleItem($event, index)" >
                                        <b-form-checkbox :checked="selectedItems.includes(index)" class="itemCheck mb-auto" />
                                        <div style="margin-top: -5px" v-if="item.disabled">
                                            <hr style="border-bottom: 1px solid black; margin-bottom: -10px">{{getLabel(item)}}
                                        </div>
                                        <div class="my-auto" v-else>{{getLabel(item)}}</div>
                                    </div>
                                </template>
                            </b-card>  
                        </b-colxx>
                        <b-button variant="primary" class="ml-auto" @click="tabIndex++">Next</b-button>
                    </b-row>
                </b-tab>
                <b-tab title="Sort Setting">
                    <div class="container" card>
                        <draggable class="row mb-2" :list="sortItems">
                            <b-colxx md="6" xs="12" v-for="(item, index) in sortItems" :key="index">
                                <div class="d-flex">
                                    <div class="mb-auto mr-2">
                                        <span class="badge badge-pill badge-info handle"><i class="simple-icon-cursor-move" /></span>
                                    </div>
                                    <b-form-group :label="getLabel(item)" class="w-100">
                                        <b-form-input />
                                    </b-form-group>
                                </div>
                            </b-colxx>
                        </draggable>
                        <div class="d-flex justify-content-between">
                            <b-button variant="primary" @click="tabIndex--">Previous</b-button>
                            <b-button variant="primary" class="ml-auto" @click="tabIndex++">Next</b-button>
                        </div>
                    </div>
                </b-tab>
                <b-tab title="Text">
                    <div class="container">
                        <b-form-group>
                            <b-form-textarea style="height: 120px" v-model="text"></b-form-textarea>
                        </b-form-group>
                        <div class="d-flex justify-content-between">
                            <b-button variant="primary" @click="tabIndex--">Previous</b-button>
                            <b-button variant="primary" class="ml-auto" @click="setRegister">Save</b-button>
                        </div>
                    </div>
                </b-tab>
            </b-tabs>
        </div>

        <b-modal
            id="add_modal"
            ref="add_modal"
            centered
            :title="modalTitle"
        >
            <b-form>
                <b-form-group label="Label">
                    <b-form-input v-model="newItem.label" />
                </b-form-group>
                <b-form-group label="Option">
                    <b-select v-model="newItem.required">
                        <option :value="true">Required</option>
                        <option :value="false">Optional</option>
                    </b-select>
                </b-form-group>
            </b-form>
            <template slot="modal-footer">
                <b-button variant="outline-primary" size="sm" @click="hideModal('add_modal')">
                    {{ $t('cancel') }}
                </b-button>
                <b-button variant="primary" size="sm" @click="addNewItem(newItem)">
                    {{submitBtnText}}
                </b-button>
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
import Draggable from 'vuedraggable'
import Axios from "axios"

import Header from "../../../containers/Web/DomainHeader"

export default {
    middleware: 'should',
    metaInfo () {
        return { title: `Vizzi Attendee Setting` }
    },
    components: {
        'b-header': Header,
        Draggable
    },
    data() {
        return {
            apiBase: '/register/',
            items: [],
            sortItems: [
                {
                    label: 'Email',
                    required: true,
                },
                {
                    label: 'Password',
                    required: true,
                },
                                {
                    label: 'Confirm Password',
                    required: true,
                }
            ],
            selectedItems: [],
            submitBtnText: '',
            status: ['Active', 'Disable', 'Delete', 'Required', 'Optional'],
            isConfirmed: false,
            notifyText: 'Successfully Saved!',
            editData: {},
            modalTitle: 'Add New Form',
            newItem: {
                label: '', required: null, disabled: false
            },
            tabIndex: 0,
            text: ''
        }
    },
    watch: {
        items: {
            deep: true,
            handler() {
                this.sortItems = [
                    {
                        label: 'Email',
                        required: true,
                    },
                    {
                        label: 'Password',
                        required: true,
                    }
                ]
                this.items.forEach(element => {
                    if (!element.disabled) {
                        this.sortItems.push(element)
                    }
                });
            }
        }
    },
    mounted() {
        Axios.post(this.apiBase + 'get', {id: this.siteId}).then(res => {
            res.data.items.forEach(item => {
                if (item.label != 'email' && item.label != 'password') {
                    this.items.push(item)
                }
            });
            this.text = res.data.text
        })
    },
    methods: {
        getLabel(setting) {
            var label = setting.label
            if (setting.required) {
                label += '*'
            }
            return label.charAt(0).toUpperCase() + label.slice(1)
        },
        addNewItem(item) {
            if (item.label == '' || item.required == null) {
                this.$notify('primary filled', '', 'Please Fill the Forms!', { duration: 3000, permanent: false })
            } else {
                this.items.push(item)
                this.newItem = {label: '', required: null, disabled: false}
                this.hideModal('add_modal')
            }
        },
        setRegister() {
            Axios.put(this.apiBase + 'set', {id: this.siteId, items: this.items, sortItems: this.sortItems, text: this.text}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try again a few hours later', { duration: 3000, permanent: false })
                }
            })
        },
        confirm() {
            this.isConfirmed = true
            this.setStatus(2)
        },
        setStatus(status) {
            if (status == 2 && !this.isConfirmed) {
                this.$refs['confirm_modal'].show()
            } else {
                this.selectedItems.forEach(index => {
                    if (status < 2) {
                        this.items[index].disabled = !status
                    } else if (status == 2) {
                        this.items.splice(index, 1)
                    } else if (status == 3) {
                        this.items[index].required = true
                    } else {
                        this.items[index].required = false
                    }
                })
                this.$refs['confirm_modal'].hide()
            }
        },
        selectAll(isToggle) {
            if (this.selectedItems.length >= this.items.length) {
                if (isToggle) this.selectedItems = [];
            } else {
                this.selectedItems = this.items.map(x => x.index);
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
        toggleItem(event, index) {
            if (event.shiftKey && this.selectedItems.length > 0) {
                let itemsForToggle = this.items;
                var start = this.getIndex(label, itemsForToggle, "label");

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
                if (this.selectedItems.includes(index)) {
                    this.selectedItems = this.selectedItems.filter(x => x !== index);
                } else this.selectedItems.push(index);
            }
        },
        initData() {
            this.editData = {}
            this.submitBtnText = 'Add New Field'
        },
        hideModal(refname) {
            this.$refs[refname].hide();
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        }),
        isSelectedAll() {
            return this.selectedItems.length >= this.items.length;
        },
        isAnyItemSelected() {
            return (
                this.selectedItems.length > 0 &&
                this.selectedItems.length < this.items.length
            );
        },
    },
}
</script>