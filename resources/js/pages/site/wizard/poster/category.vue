<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
            <breadcrumb heading="Poster Category" />
            <div class="top-right-button-container d-flex">
                <b-button
                    v-b-modal.posterModal
                    variant="primary"
                >Add New</b-button>
            </div>
            <div class="separator mb-5"></div>
            </b-colxx>
        </b-row>
        <b-tabs>
            <b-tab title="Category Setting">
                <b-card class="table-responsive">
                    <b-table
                        hover
                        :fields="fields"
                        :items="boothModels"
                    >
                        <template v-slot:cell(background)="data">
                            <img :src="'/assets/img/poster-backdrop/default.png'" style="width: 200px" alt="Backdrop" v-if="data.item.bg == 1">
                            <img :src="'/assets/img/poster-backdrop/'+data.item.backdrop.media" style="width: 200px" alt="Backdrop" v-else>
                        </template>
                        <template v-slot:cell(title)="data">{{data.item.category}}</template>
                        <template v-slot:cell(status)="data">
                            <b-badge class="my-auto" pill :variant="'danger'" v-if="data.item.status == 0">Inactive</b-badge>
                            <b-badge class="my-auto" pill :variant="'primary'" v-if="data.item.status == 1">Active</b-badge>
                        </template>
                        <template v-slot:cell(priority)="data">
                            <div v-if="!data.item.order || data.item.order == 0">0</div>
                            <div v-else>{{data.item.order}}</div>
                        </template>
                        <template v-slot:cell(action)="data">
                            <b-dropdown variant="primary" right text="Action">
                                <b-dropdown-item @click="setStatus(data.item.id, 1)">Active</b-dropdown-item>
                                <b-dropdown-item @click="setStatus(data.item.id, 0)">InActive</b-dropdown-item>
                                <hr>
                                <b-dropdown-item @click="edit(data.item)" v-b-modal.posterModal>Edit</b-dropdown-item>
                                <b-dropdown-item @click="confirmDel(data.item.id)" v-b-modal.confirm_modal>Delete</b-dropdown-item>
                                <hr>
                                <b-dropdown-item @click="setType(data.item.id, 1)">Backdrop 1</b-dropdown-item>
                                <b-dropdown-item @click="setType(data.item.id, backdrop.id)" v-for="(backdrop, index) in backdrops" :key="backdrop.id">Backdrop {{index + 2}}</b-dropdown-item>
                            </b-dropdown>
                        </template>
                    </b-table>
                    <paginate-nav
                        :lastPage="lastPage"
                        :page="page"
                        :changePage="changePage"
                    />
                </b-card>
            </b-tab>
            <b-tab title="Background Setting">
                <b-row class="mt-4">
                    <b-colxx md="6" sm="12" lg="3" class="my-4">
                        <b-card title="Default">
                            <img src="/assets/img/poster-backdrop/default.png" class="w-100" />
                        </b-card>
                    </b-colxx>
                    <b-colxx md="6" sm="12" lg="3" class="my-4" v-for="(backdrop, index) in backdrops" :key="backdrop.created_at">
                        <b-card :title="'Setting' + (index + 1)" class="position-relative">
                            <img :src="img_0" class="w-100" v-if="index == 0 && img_0" />
                            <img :src="img_1" class="w-100" v-else-if="index == 1 && img_1" />
                            <img :src="img_2" class="w-100" v-else-if="index == 2 && img_2" />
                            <img :src="'/assets/img/poster-backdrop/' + backdrop.media" class="w-100" v-else />
                            <label for="image" class="btn btn-outline-primary middle-center" @click="dropCurrent = index">Upload</label>
                            <button class="btn btn-primary mt-2" v-if="index == 0 && img_0" @click="changeCategory(index, backdrop.id)">Confirm</button>
                            <button class="btn btn-primary mt-2" v-if="index == 1 && img_1" @click="changeCategory(index, backdrop.id)">Confirm</button>
                            <button class="btn btn-primary mt-2" v-if="index == 2 && img_2" @click="changeCategory(index, backdrop.id)">Confirm</button>
                        </b-card>
                    </b-colxx>
                    <b-colxx md="6" sm="12" lg="3" class="my-4" v-for="n in (3 - backdrops.length)" :key="n">
                        <b-card class="h-100 position-relative" :title="'Setting ' + (backdrops.length + n)">
                            <img :src="url_1" class="w-100" v-if="n == 1 && url_1" />
                            <img :src="url_2" class="w-100" v-if="n == 2 && url_2" />
                            <img :src="url_3" class="w-100" v-if="n == 3 && url_3" />
                            <label for="file" class="btn btn-outline-primary middle-center" @click="current = n">Upload</label>
                            <button class="btn btn-primary mt-2" v-if="n == 1 && url_1" @click="confirmCategory(n)">Confirm</button>
                            <button class="btn btn-primary mt-2" v-if="n == 2 && url_2" @click="confirmCategory(n)">Confirm</button>
                            <button class="btn btn-primary mt-2" v-if="n == 3 && url_3" @click="confirmCategory(n)">Confirm</button>
                        </b-card>
                    </b-colxx>
                </b-row>
            </b-tab>
        </b-tabs>

        <b-modal
            id="posterModal"
            ref="posterModal"
            :title="modalTitle"
            modal-class="modal-center"
        >
            <b-form-group label="Title">
                <b-form-input v-model="category" />
            </b-form-group>
            <b-form-group label="Weight">
                <b-form-input type="number" v-model="order" />
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
        <input type="file" @change="onFileChange" id="file" class="d-none" />
        <input type="file" @change="onImageChange" id="image" class="d-none" />
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'
import PaginateNav from '../../../../components/Common/PaginateNav'

export default {
    metaInfo () {
        return { title: `Poster Models` }
    },
    components: {
        PaginateNav
    },
    data() {
        return {
            modalTitle: 'Add New Category',
            category: '',
            boothModels: [],
            backdrops: [],
            apiBase: '/wizard/',
            id: null,
            currentIndex: null,
            fields: [
                { key: 'background', sortable: true },
                { key: 'title', sortable: true },
                { key: 'status', sortable: true },
                { key: 'priority', sortable: true },
                { key: 'action', sortable: false}
            ],
            url_1: null,
            url_2: null,
            url_3: null,
            img_0: null,
            img_1: null,
            img_2: null,
            current: null,
            dropCurrent: null,
            formData: new FormData(),
            categoryId: 0,
            order: 0,

            lastPage: null,
            page: 0
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            user: 'auth/user',
            boothId: 'booth/getId'
        }),
    },  
    methods: {
        init() {
            Axios.post(this.apiBase + 'models', {siteId: this.siteId, type: 2}).then(res => {
                this.boothModels = res.data.models.data
                this.lastPage = res.data.models.last_page
                this.page = res.data.models.current_page
                this.backdrops = res.data.backdrops
            })
        },
        select(id) {
            Axios.post(this.apiBase + 'setTemp', {id: this.boothId, temp_id: id}).then(res => {
                if (res.status == 200) {
                    this.$router.push('/app/wizard/poster/edit/' + this.boothId)
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        hideModal() {
            this.$refs['confirm_modal'].hide()
            this.$refs['posterModal'].hide()
        },
        confirmDel(id) {
            this.id = id
        },
        async confirm() {
            const res = await Axios.post(this.apiBase + 'delCat', {id: this.id, siteId: this.siteId})
            if (res.status == 200) {
                this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                this.page = res.data.current_page
                this.lastPage = res.data.last_page
                this.boothModels = res.data.data
                this.hideModal()
            } else {
                this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
            }
        },
        setStatus(id, status) {
            Axios.post(this.apiBase + 'categoryStatus', {id: id, status: status, siteId: this.siteId}).then(res => {
                this.boothModels = res.data
            })
        },
        onFileChange(e) {
            const file = e.target.files[0]
            if (file.size / 10000 > 5) {
                this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
            }
            if (this.current == 1) {
                this.url_1 = URL.createObjectURL(file)
            } else if (this.current == 2) {
                this.url_2 = URL.createObjectURL(file)
            } else {
                this.url_3 = URL.createObjectURL(file)
            }
            this.formData.set('file' + this.current, file)
        },
        onImageChange(e) {
            const file = e.target.files[0]
            if (file.size / 10000 > 5) {
                this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
            }
            if (this.dropCurrent == 0) {
                this.img_0 = URL.createObjectURL(file)
            } else if (this.dropCurrent == 1) {
                this.img_1 = URL.createObjectURL(file)
            } else {
                this.img_2 = URL.createObjectURL(file)
            }
            this.formData.set('image' + this.dropCurrent, file)
        },
        confirmCategory(n) {
            this.formData.append('siteId', this.siteId)
            Axios.post(this.apiBase + 'setBackdrop', this.formData).then(res => {
                this.backdrops = res.data
                this.formData = new FormData()
                this.url_1 = null
                this.url_2 = null
                this.url_3 = null
            })
        },
        edit(item) {
            this.modalTitle = 'Edit Category'
            this.category = item.category
            this.order = item.order
            this.categoryId = item.id
        },
        changeCategory(n, id) {
            this.formData.append('siteId', this.siteId)
            this.formData.append('id', id)
            Axios.post(this.apiBase + 'editBackdrop', this.formData).then(res => {
                this.backdrops = res.data
                this.formData = new FormData()
                this.img_0 = null
                this.img_1 = null
                this.img_2 = null
            })
        },
        setType(id, backdropId) {
            Axios.post(this.apiBase + 'setType', {id: id, backdropId: backdropId, siteId: this.siteId}).then(res => {
                this.page = res.data.current_page
                this.lastPage = res.data.last_page
                this.boothModels = res.data.data
            })
        },
        addNewItem() {
            Axios.post(this.apiBase + 'setCat', {id: this.siteId, category: this.category, categoryId: this.categoryId, order: this.order}).then(res => {
                if (res.status == 200) {
                    this.page = res.data.current_page
                    this.lastPage = res.data.last_page
                    this.boothModels = res.data.data
                    this.$notify('primary filled', '', 'Successfully Created!', { duration: 3000, permanent: false })
                    this.categoryId = 0
                    
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
                this.hideModal()
            })
        },
        async changePage(pageNum) {
            this.page = pageNum
            const { data } = await Axios.get(
                this.apiBase + 'get-models?page=' + pageNum, 
                {params: {
                    id: this.siteId,
                    type: 2
                }}
            )
            this.boothModels = data.data
            this.lastPage = data.last_page
            this.page = data.current_page
        },
    },
    mounted() {
        this.init()
    }
}
</script>

<style scoped>
    .model {
        object-fit: cover;
        height: 160px
    }
    .middle-center {
        position: absolute;
        top: calc(50% + 22px);
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>