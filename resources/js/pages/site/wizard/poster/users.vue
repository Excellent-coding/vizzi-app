<template>
<div>
    <b-row>
        <b-colxx xxs="12">
            <breadcrumb heading="Poster Presentations" />
            <div class="top-right-button-container d-flex">
                <a href="/assets/sample/poster.xlsx" download class="btn btn-outline-primary mr-2">Sample file Download</a>
                <label for="file" class="btn btn-primary mb-0">Upload</label>
            </div>
            <div class="separator mb-5"></div>
        </b-colxx>
    </b-row>
    <b-card>
        <div class="table-responsive">
            <b-table
                :fields="fields"
                :items="booths"
                hover
            >
                <template v-slot:cell(#)="data">{{data.index + 1}}</template>
                <template v-slot:cell(title)="data">
                    <div style="width: 180px">
                        {{data.item.title.substring(0, 120)}}
                    </div>
                </template>
                <template v-slot:cell(status)="data">
                    <b-badge class="mt-2" pill :variant="'danger'" v-if="data.item.status == 0">Inactive</b-badge>
                    <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status == 1">Active</b-badge>
                </template>
                <template v-slot:cell(category)="data">
                    <div class="mt-2" v-if="data.item.category" style="width: 180px">
                        {{data.item.category.category}}
                    </div>
                </template>
                <template v-slot:cell(mainManager)="data">
                    <div class="d-flex" v-if="data.item.user">
                        <img :src="'/assets/img/avatar/' + data.item.user.avatar" class="user-avatar mr-2" :alt="data.item.user.name" />
                        <div class="my-auto mr-auto">
                            <h6>{{data.item.user.name}}</h6>
                            <p class="mb-0">{{data.item.user.email}}</p>
                        </div>
                        <button class="btn btn-outline-primary" @click="data.toggleDetails" style="width: 180px">
                            {{data.detailsShowing ? 'Hide' : 'Show'}} Team Members
                        </button>
                    </div>
                </template>
                <template v-slot:row-details="row">
                    <b-table
                        :fields="memberFields"
                        :items="row.item.members"
                        hover
                    >
                        <template v-slot:cell(#)="data">{{data.index + 1}}</template>
                        <template v-slot:cell(status)="data">
                            <b-badge class="mt-2" pill :variant="'danger'" v-if="data.item.status == 0">Pending</b-badge>
                            <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status == 1">Active</b-badge>
                        </template>
                        <template v-slot:cell(action)="data">
                            <b-dropdown variant="primary" right text="Action">
                                <b-dropdown-item @click="userStatus(data.item.id, 1)">Active</b-dropdown-item>
                                <b-dropdown-item @click="userStatus(data.item.id, 0)">InActive</b-dropdown-item>
                            </b-dropdown>
                        </template>
                    </b-table>
                </template>
                <template v-slot:cell(action)="data">
                    <div class="w-100 text-right">
                        <b-dropdown variant="primary" right text="Action">
                            <b-dropdown-item @click="boothStatus(data.item.id, 1)">Active</b-dropdown-item>
                            <b-dropdown-item @click="boothStatus(data.item.id, 0)">InActive</b-dropdown-item>
                            <b-dropdown-item @click="boothStatus(data.item.id, 2)">Delete</b-dropdown-item>
                        </b-dropdown>
                    </div>
                </template>
            </b-table>
        </div>
        <paginate-nav
            :lastPage='lastPage'
            :page='page'
            :changePage='changePage'
        />
        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal()">Cancel</b-button>
            </template>
        </b-modal>
    </b-card>
    <input type="file" @change="onFileChange" id="file" class="d-none" />
</div>

</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'
import PaginateNav from '../../../../components/Common/PaginateNav'

export default {
    metaInfo () {
        return { title: `Booth Members` }
    },
    components: {
        PaginateNav
    },
    data() {
        return {
            apiBase: '/wizard/',
            managers: [],
            members: [],
            users: [],
            booths: [],
            fields: [
                '#',
                { key: 'title', sortable: true },
                { key: 'status', sortable: true },
                { key: 'category', sortable: true },
                { key: 'mainManager', sortable: true },
                { key: 'action', sortable: false}
            ],
            memberFields: [
               '#',
                { key: 'name', sortable: true },
                { key: 'email', sortable: true },
                { key: 'status', sortable: true },
                { key: 'action', sortable: false}
            ],
            categoryData: [],
            userData: [],
            posterData: [],
            lastPage: 0,
            page: 0
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
        async init() {
            const { data } = await Axios.post(this.apiBase + 'getPosterData', {id: this.siteId})
            this.page = data.current_page
            this.lastPage = data.last_page
            this.booths = data.data
        },
        getMember(id) {
            this.members = []
            this.users.forEach(element => {
                if (id == element.id) {
                    this.members.push(element)
                }
            })
        },
        boothStatus(id, status) {
            Axios.post(this.apiBase + 'posterStatus', {id: id, siteId: this.siteId, status: status}).then(res => {
                if (res.status == 200) {
                    this.booths = res.data
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        userStatus(id, status) {
            Axios.post(this.apiBase + 'userStatus', {id: id, siteId: this.siteId, status: status, type: 2}).then(res => {
                if (res.status == 200) {
                    this.booths = res.data
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
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
        async submit(file) {
            var no = 2
            while(file['C' + no] && file['D' + no] && file['E' + no] && file['F' + no]) {
                this.categoryData.push({category: file['C' + no].v})
                this.userData.push({
                    name: file['D' + no].v,
                    email: file['E' + no].v
                })
                this.posterData.push({
                    entry_id: file['A' + no].v,
                    title: file['F' + no].v,
                })
                no++
            }
            console.log(this.posterData)
            console.log(this.userData)
            console.log(this.categoryData)
            const res = await Axios.post(this.apiBase + 'poster-upload', {categoryData: this.categoryData, userData: this.userData, posterData: this.posterData, id: this.siteId})
            if (res.data.errors) {
                res.data.errors.forEach(element => {
                    this.$notify('primary filled', '', element, { duration: 3000, permanent: false })
                });
            }
            if (res.status == 200) {
                this.booths = res.data
            }
        },
        async changePage(pageNum) {
            this.page = pageNum
            const { data } = await Axios.get(
                this.apiBase + 'get-poster-data?page=' + pageNum, 
                {params: {
                    id: this.siteId
                }}
            )
            this.booths = data.data
            this.page = data.current_page
            this.lastPage = data.last_page
        }
    }
}
</script>