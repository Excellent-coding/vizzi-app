<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <div class="top-right-button-container d-flex" v-if="user && user.role < 4">
                    <a href="/assets/sample/sample-exhibit.xlsx" download class="btn btn-outline-primary mr-2">Sample file Download</a>
                    <label for="file" class="btn btn-primary mb-0">Upload</label>
                </div>
                <breadcrumb :heading="title" />
                <div class="separator mb-5"></div>
            </b-colxx>
        </b-row>
        <b-card>
            <b-row>
                <b-colxx xxs="12">
                    <div v-html="note" class="text-center" style="font-size: 16px" v-if="note && note != ''" />
                    <h2 class="mt-4 text-center font-bold">Add Poster Information</h2>
                    <div class="separator"></div>
                </b-colxx>
            </b-row>
            <div class="w-md-60 w-100 mx-auto">
                <b-row>
                    <b-colxx lg="6" sm="12">
                        <h6 class="text-muted mt-3">Poster Name</h6>
                        <b-form-input v-model="booth" v-if=" user && user.role < 4" />
                        <h6 class="text-muted mt-3">Poster Category</h6>
                        <b-form-group>
                            <b-select v-model="category">
                                <option :value="item.id" v-for="item in categories" :key="item.id">
                                    {{item.category}}
                                </option>
                            </b-select>
                        </b-form-group>
                        <h6 class="text-muted mt-3">Poster Contact Name</h6>
                        <b-form-input v-model="contact.name" />
                        <h6 class="text-muted mt-3">Poster Email</h6>
                        <b-form-input v-model="contact.email" />
                    </b-colxx>
                    <b-colxx lg="6" sm="12">
                        <h6 class="text-muted mt-3">Poster Description</h6>
                        <b-form-textarea style="height: 122px" />
                        <h6 class="text-muted mt-3">Upload Poster Thumbnail</h6>
                        <b-input-group>
                            <b-input-group-append>
                                <b-input-group-text>Upload</b-input-group-text>
                            </b-input-group-append>
                            <b-form-input />
                        </b-input-group>
                    </b-colxx>
                </b-row>
                <h6 class="font-bold text-center mt-3">Add Team Members <small>(Optional)</small></h6>
                <div class="d-md-flex d-block justify-content-between align-items-center">
                    <div class="ml-auto mr-1 mt-3">
                        <b-form-group label="Name" class="has-float-label">
                            <b-form-input v-model="member.name" />
                        </b-form-group>
                    </div>
                    <div class="mx-md-1 mx-0 mt-3">
                        <b-form-group label="Email" class="has-float-label">
                            <b-form-input v-model="member.email" />
                        </b-form-group>
                    </div>
                    <b-button variant="primary" class="mr-auto ml-1" @click="addMember" v-if="buttonText == 'Add to Poster'">{{buttonText}}</b-button>
                    <template v-else>
                        <b-button variant="primary" class="mx-1" @click="addMember">{{buttonText}}</b-button>
                        <b-button variant="outline-primary" class="mr-auto ml-1" @click="cancelMember">Cancel</b-button>
                    </template>
                </div>
                <h4 class="mt-5 font-bold text-center">Select Your Poster Builder Options</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <b-button variant="primary" class="mx-auto" @click="getModel()">Poster Wizard</b-button>
                </div>
                <div class="table-responsive mt-4">
                    <b-table
                        hover
                        :items="members"
                        :fields="fields"
                        v-if="members.length"
                    >
                        <template v-slot:cell(#)="data">
                            <div class="mt-2">{{data.index + 1}}</div>
                        </template>
                        <template v-slot:cell(name)="data">
                            <div class="mt-2">{{data.item.name}}</div>
                        </template>
                        <template v-slot:cell(email)="data">
                            <div class="mt-2">{{data.item.email}}</div>
                        </template>
                        <template v-slot:cell(status)="data">
                            <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status">Active</b-badge>
                            <b-badge class="mt-2" pill :variant="'danger'" v-else>Inactive</b-badge>
                        </template>
                        <template v-slot:cell(action)="data">
                            <b-dropdown variant="primary" right text="Action">
                                <b-dropdown-item @click="editMember(data.item)">Edit</b-dropdown-item>
                                <b-dropdown-item @click="delMember(data)">Delete</b-dropdown-item>
                            </b-dropdown>
                        </template>
                    </b-table>
                </div>
            </div>
        </b-card>
        <b-modal id="guideModal" ref="guideModal" centered hide-footer hide-header>
            <h2 class="font-bold mb-4">{{text.title}}</h2>
            <h4 class="my-4 font-medium">{{text.content}}</h4>
            <button class="btn start-btn" @click="guideConfirm()">Create your Poster design</button>
        </b-modal>
        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal('confirm_modal')">Cancel</b-button>
            </template>
        </b-modal>
        <b-modal id="userModal" ref="userModal" centered title="Please Confirm the User Data">
            <b-table :fields="modalFields" :items="userData">
                <template v-slot:cell(#)="data">{{data.index + 1}}</template>
            </b-table>
            <template slot="modal-footer">
                <b-button variant="secondary" @click="hideModal('userModal')" class="mr-1">Cancel</b-button>
                <b-button variant="primary" @click="confirmMember()" class="ml-1">Confirm</b-button>
            </template>
        </b-modal>
        <input type="file" @change="onFileChange" id="file" class="d-none" />
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"

export default {
    metaInfo () {
        return { title: `Exhibit Poster Wizard` }
    },
    components: {
        "v-select": vSelect
    },
    data() {
        return {
            title: 'Exhibit Poster Wizard',
            note: '',
            member: {name: '', email: ''},
            members: [],
            apiBase: '/wizard/',
            users: [],
            booths: [],
            fields: [
                '#',
                { key: 'name', sortable: true },
                { key: 'email', sortable: true },
                { key: 'status', sortable: true },
                { key: 'action', sortable: false }
            ],
            modalFields: [
                '#',
                { key: 'name', sortable: true },
                { key: 'email', sortable: true }
            ],
            createType: 0,
            buttonText: 'Add to Poster',
            memberId: 0,
            isConfirmed: false,
            currentIndex: 0,
            userData: [],
            text: {title: '', content: ''},
            contact: {name: '', email: '', phone: ''},
            isContact: false,
            categories: [],
            category: '',
            booth: ''
        }
    },
    methods: {
        init() {
            Axios.post(this.apiBase + 'get', {id: this.siteId, type: 2}).then(res => {
                if (res.status == 200) {
                    this.note = res.data.domain.note2
                    this.users = res.data.users
                    this.booths = res.data.booths
                    this.text.title = res.data.domain.title2
                    this.text.content = res.data.domain.content2
                    if (                        
                        !localStorage.getItem('isPosterGuide') && 
                        this.text.title && this.text.content &&
                        this.text.title != '' && this.text.content != ''
                    ) {
                        this.$refs['guideModal'].show()
                    }
                    this.categories = res.data.categories
                    if (res.data.domain.contact2 == 1) {
                        this.isContact = true
                    }
                }
            })
        },
        hideModal(ref) {
            this.$refs[ref].hide()
            this.memberId = 0
        },
        guideConfirm() {
            localStorage.setItem('isPosterGuide', true)
            this.$refs['guideModal'].hide()
        },
        submitNote() {
            Axios.put(this.apiBase + 'note', {note: this.note, id: this.siteId, type: 2}).then(res => {
                if (res.statusText == 'OK') {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        async addMember() {
            if (this.booth == '') {
                this.$notify('primary filled', '', 'Please Insert Poster Name!', { duration: 3000, permanent: false })
            } else {
                var error = false
                for (const [key, value] of Object.entries(this.member)) {
                    if (value == '') {
                        error = true
                    }
                }
                // var title = false
                // if (this.createType == 0) {
                //     this.booths.forEach(element => {
                //         if (element.title == this.booth) {
                //             title = true
                //         }
                //     })
                // }
                if (error) {
                    this.$notify('primary filled', '', 'Please fill the forms', { duration: 3000, permanent: false })
                } else {
                    var res = await Axios.post(this.apiBase + 'member', {memberId: this.memberId, member: this.member, id: this.siteId, booth: this.booth, type: 2})
                    if (res.status == 200) {
                        if (res.data.errors) {
                            this.$notify('primary filled', '', 'Email is repeated!', { duration: 3000, permanent: false })
                        } else {
                            var text = 'Successfully Added!'
                            if (this.memberId != 0) {
                                text = 'Successfully Changed!'
                            }
                            this.$notify('primary filled', '', text, { duration: 3000, permanent: false })
                            this.members = res.data.members
                            this.booths = res.data.booths
                        }
                    } else {
                        this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                    }
                    this.member = {name: '', email: ''}
                    this.memberId = 0
                }
            }
        },
        editMember(item) {
            this.buttonText = 'Change'
            this.memberId = item.id
            this.member = {name: item.name, email: item.email}
        },
        cancelMember() {
            this.buttonText = 'Add to Poster'
            this.member = {name: '', email: ''}
            this.memberId = 0
        },
        delMember(data) {
            this.memberId = data.item.id
            this.currentIndex = data.index
            this.$refs['confirm_modal'].show()
        },
        confirm() {
            Axios.post(this.apiBase + 'del', {id: this.memberId, type: 2, siteId: this.siteId}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                    this.users = res.data
                    this.members.splice(this.currentIndex, 1)
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
                this.$refs['confirm_modal'].hide()
            })
        },
        getMember(item) {
            this.booth = item.title
            this.members = []
            this.users.forEach(element => {
                if (element.booth_id == item.id) {
                    this.members.push(element)
                }
            })
        },
        emptyMember() {
            this.members = []
            this.booth = ''
        },
        confirmMember() {
            this.userData.forEach(element => {
                this.members.push(element)
            });
            this.$refs['userModal'].hide()
        },
        async upload() {
            if (this.booth == '') {
                this.$notify('primary filled', '', 'Please Insert Poster Name!', { duration: 3000, permanent: false })
            } else if (this.category == '') {
                this.$notify('primary filled', '', 'Please Select Poster Category!', { duration: 3000, permanent: false })
            } else {
                const res = await Axios.post(this.apiBase + 'setBooth', {title: this.booth, category: this.category, id: this.siteId, type: 2})
                if (res.status != 200) {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                } else {
                    await this.$store.dispatch('booth/setId', {
                        id: res.data.id,
                    })
                    if (this.user.role < 3) {
                        this.$router.push('/app/wizard/poster/edit/'+res.data.id)
                    } else {
                        this.$router.push('/wizard/poster/edit/'+res.data.id)
                    }
                }
            }
        },
        async getModel() {
            if (this.booth == '') {
                this.$notify('primary filled', '', 'Please Insert Poster Name!', { duration: 3000, permanent: false })
            } else if (this.category == '') {
                this.$notify('primary filled', '', 'Please Select Poster Category!', { duration: 3000, permanent: false })
            } else {
                const res = await Axios.post(this.apiBase + 'setBooth', {title: this.booth, category: this.category, id: this.siteId, type: 2, members: this.members})
                if (res.status != 200) {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                } else {
                    await this.$store.dispatch('booth/setId', {
                        id: res.data.id,
                    })

                    if (this.user.role < 3) {
                        this.$router.push('/app/wizard/poster/edit/'+res.data.id)
                    } else {
                        this.$router.push('/wizard/poster/edit/'+res.data.id)
                    }
                }
            }
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
        submit(file) {
            var no = 2
            this.userData = []
            while(file['B' + no] && file['C' + no])  {
                this.userData.push({
                    name: file['B' + no].v,
                    email: file['C' + no].v
                })
                no++
            }
            this.$refs['userModal'].show()
        },
        showContact() {
            Axios.put(this.apiBase + 'setContact', {id: this.siteId, status: !this.isContact, type: 2}).then(res => {
                if (res.status == 200) {
                    this.isContact = !this.isContact
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        }
    },
    computed: {
        ...mapGetters({
            user: 'auth/user',
            siteId: 'site/getSiteId',
            item: 'booth/getItem'
        }),
        boothTitle() {
            if (this.item.title) {
                return this.item.title
            } else {
                return ''
            }
        }
    },
    watch: {
        createType(val) {
            if (val == 1) {
                this.getMember(this.booths[0])
            } else {
                this.emptyMember()
            }
        },
        booth(val) {
            if (this.createType == 1 && val != '') {
                this.cancelMember()
                this.booths.forEach(element => {
                    if (element.title == val) {
                        this.getMember(element)
                    }
                });
            }
        }
    },
    mounted() {
        if (this.user.role > 2 && this.boothTitle == '') {
            this.$router.push({name: 'wizard.poster.models'})
        }
        this.booth = this.boothTitle
        this.init()
    }
}
</script>

<style lang="css">
    .form-control:disabled {
        background: white;
    }
    .modal .modal-content {
        border-radius: 16px;
        text-align: center;
        font-weight: 500;
    }
    .font-bold {
        font-weight: 900;
    }
    .font-medium {
        font-weight: 600;
    }
    .start-btn {
        background: #922c88;
        border-radius: 16px;
        color: white;
        padding: 8px 48px;
        font-size: 16px;
        font-weight: 700;
        margin-left: auto;
        margin-right: auto;
    }
    .start-btn:hover {
        color: white
    }
</style>