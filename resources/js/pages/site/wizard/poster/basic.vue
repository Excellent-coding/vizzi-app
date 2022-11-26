<template>
    <div class="container rounded">
        <div>
            <breadcrumb heading="Basic" />
            <div class="top-right-button-container d-flex" v-if="isCategory">
                <b-button
                    v-b-modal.categoryModal
                    variant="primary"
                    size="lg"
                    class="top-right-button"
                >Add New</b-button>
                <b-status-button
                    :selectAll="selectAll"
                    :isSelectedAll="isSelectedAll"
                    :keymap="keymap"
                    :isAnyItemSelected="isAnyItemSelected"
                    :setStatus="setStatus"
                    :status="status"
                />
            </div>
            <div class="separator mb-5"></div>
        </div>

        <b-card no-body>
            <b-tabs card>
                <b-tab title="Welcome text" @click="isCategory = false">
                    <div class="w-50 mx-auto">
                        <b-form-group label="Title">
                            <b-form-input v-model="text.title" />
                        </b-form-group>
                        <b-form-group label="Content">
                            <b-textarea v-model="text.content" class="welcomeArea"/>
                        </b-form-group>
                        <button class="btn btn-primary" @click="submit">Submit</button>
                    </div>
                </b-tab>
                <b-tab title="Category" @click="isCategory = true">
                    <b-row>
                        <b-colxx xxs="12" class="mb-3" v-for="(item, index) in categories" :key="index" :id="item.id">
                            <b-card @click.prevent="toggleItem($event,item.id)" :class="{'d-flex flex-row':true,'active' : selectedCategories.includes(item.id)}" no-body>
                                <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                    <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                        <p class="mb-0 text-muted text-small w-15 w-sm-100">{{index + 1}}</p>
                                        <p class="list-item-heading mb-0 truncate mr-auto">{{item.name}}</p>
                                        <div class="w-15 w-sm-100">
                                            <b-badge pill variant="primary" v-if="item.status">Active</b-badge>
                                            <b-badge pill variant="secondary" v-else>InActive</b-badge>
                                        </div>
                                        <button class="btn btn-outline-primary" @click="edit(item)">Edit</button>
                                    </div>
                                    <div class="custom-control custom-checkbox pl-1 align-self-center pr-4">
                                        <b-form-checkbox :checked="selectedCategories.includes(item.id)" class="itemCheck mb-0" />
                                    </div>
                                </div>
                            </b-card>
                        </b-colxx>
                    </b-row>
                </b-tab>  
            </b-tabs>
        </b-card>

        <b-modal
            id="categoryModal"
            ref="categoryModal"
            :title="modalTitle"
            modal-class="modal-center"
        >
            <b-form-group label="Category">
                <b-form-input v-model="categoryItem" />
            </b-form-group>
            <template slot="modal-footer">
                <b-button
                    variant="outline-secondary"
                    @click="hideModal()"
                >{{ $t('cancel') }}</b-button>
                <b-button variant="primary" @click="addNewItem()" class="mr-1">{{ $t('submit') }}</b-button>
            </template>
        </b-modal>

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
import Axios from 'axios'
import { mapGetters } from 'vuex'

import StatusButton from '../../../../components/Pages/StatusButton'

export default {
    components: {
        'b-status-button': StatusButton
    },
    metaInfo () {
        return { title: `Poster Setting` }
    },
    data() {
        return {
            apiBase: 'wizard/poster/',
            text: {
                title: '',
                content: '',
                id: null
            },
            isCategory: false,
            status: ['Active', 'InActive', 'Delete'],

            categories: [],
            selectedCategories: [],

            modalTitle: 'Add New Category',
            categoryItem: '',
            categoryId: 0,
            onDelete: false,
            currentStatus: 0
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        }),
        isSelectedAll() {
            return this.selectedCategories.length >= this.categories.length;
        },
        isAnyItemSelected() {
            return (
                this.selectedCategories.length > 0 &&
                this.selectedCategories.length < this.categories.length
            );
        }
    },
    mounted() {
        Axios.post(this.apiBase + 'getData', {id: this.siteId}).then(res => {
            if (res.data.introduction && res.data.introduction.title && res.data.introduction.content) {
                this.text.title = res.data.introduction.title
                this.text.content = res.data.introduction.content
            }
            if (res.data.categories) {
                this.categories = res.data.categories
            }
        })
    },
    methods: {
        submit() {
            if (this.text.title == '' || this.text.content == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert all fields', { duration: 3000, permanent: false })
            } else {
                this.text.id = this.siteId
                Axios.post(this.apiBase + 'setText', this.text).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', '', 'Successfully Created!', { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Ooop!', 'Something went wrong', { duration: 3000, permanent: false })
                    }
                })
            }
        },
        addNewItem() {
            if (this.categoryItem == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert field', { duration: 3000, permanent: false })
            } else {
                Axios.post(this.apiBase + 'setCategory', {item: this.categoryItem, id: this.siteId, categoryId: this.categoryId}).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', '', 'Successfully saved!', { duration: 3000, permanent: false })
                        this.categories = res.data
                        this.categoryItem = ''
                        this.$refs['categoryModal'].hide()
                        this.categoryId = 0
                    } else {
                        this.$notify('primary filled', 'Ooop!', 'Something went wrong', { duration: 3000, permanent: false })
                    }
                })
            }
        },
        hideModal() {
            this.$refs['categoryModal'].hide()
            this.$refs['confirm_modal'].hide()
        },

        selectAll(isToggle) {
            if (this.selectedCategories.length >= this.categories.length) {
                if (isToggle) this.selectedCategories = [];
            } else {
                this.selectedCategories = this.categories.map(x => x.id);
            }
        },
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                break;
                case "undo":
                    this.selectedCategories = [];
                break;
            }
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedCategories.length > 0) {
                let itemsForToggle = this.items;
                var start = this.getIndex(itemId, itemsForToggle, "id");
                var end = this.getIndex(
                    this.selectedCategories[this.selectedCategories.length - 1],
                    itemsForToggle,
                    "id"
                );
                itemsForToggle = itemsForToggle.slice(
                    Math.min(start, end),
                    Math.max(start, end) + 1
                );
                this.selectedCategories.push(
                    ...itemsForToggle.map(item => {
                        return item.id;
                    })
                );
            } else {
                if (this.selectedCategories.includes(itemId)) {
                    this.selectedCategories = this.selectedCategories.filter(x => x !== itemId);
                } else this.selectedCategories.push(itemId);
            }
        },
        confirm() {
            this.onDelete = true
            this.setStatus(this.currentStatus)
        },
        setStatus(status) {
            this.currentStatus = status
            if (status == 2 && !this.onDelete) {
                this.$refs['confirm_modal'].show()
            } else {
                Axios.post(this.apiBase + 'categoryStatus', {status: status, items: this.selectedCategories, id: this.siteId}).then(res => {
                    this.categories = res.data
                    this.onDelete = false
                    this.$refs['confirm_modal'].hide()
                })
            }

        },
        edit(item) {
            this.categoryItem = item.name
            this.categoryId = item.id
            this.$refs['categoryModal'].show()
        }
    }
}
</script>

<style scoped>
    .welcomeArea {
        height: 360px;
    }
</style>