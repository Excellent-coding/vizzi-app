<template>
    <div class="container">
        <breadcrumb heading="Poster Setting" />
        <div class="separator mb-5"></div>
        <b-tabs>
            <b-tab title="Text Setting">
                <b-card>
                    <b-row>
                        <b-colxx md="6" sm="12">
                            <h5>Welcome Text</h5>
                            <b-form-group label="Title" class="has-float-label mt-4">
                                <b-form-input v-model="text.title" />
                            </b-form-group>
                            <b-form-group label="Content" class="has-float-label">
                                <b-textarea v-model="text.content" style="height: 160px"/>
                            </b-form-group>
                            <button class="btn btn-primary" @click="submit">Submit</button> 
                        </b-colxx>
                        <b-colxx md="6" sm="12">
                            <h5 class="mt-4">Reminder</h5>
                            <b-form-textarea
                                v-model="note"
                                class="w-100 mt-4"
                                style="height: 150px"
                            />
                            <button class="btn btn-primary mt-2" @click="submitNote">Submit</button>
                        </b-colxx>
                    </b-row>
                </b-card>
            </b-tab>
            <b-tab title="Modal Setting">
                <b-card>
                    <b-form-group label="Modal Width(%)" class="w-25 w-md-100">
                        <v-select v-model="modal" :options="widthList" />
                    </b-form-group>
                    <b-button variant="primary" size="sm" @click="modalSetting">Submit</b-button>
                </b-card>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
import Axios from 'axios'
import { mapGetters } from 'vuex'
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"

export default {
    components: {
        vSelect
    },
    data() {
        return {
            text: {
                title: '',
                content: '',
                id: null,
                type: 2
            },
            apiBase: '/wizard/',
            note: '',
            categoryData: [],
            userData: [],
            posterData: [],
            modal: 80,
            widthList: [20, 40, 60, 80, 100]
        }
    },
    mounted() {
        this.init()
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            user: 'auth/user'
        })
    },
    methods: {
        async init() {
            const res = await Axios.post(this.apiBase + 'get-setting', {id: this.siteId, type: 2})
            if (res.status == 200) {
                this.text.title = res.data.title
                this.text.content = res.data.content
                this.note = res.data.note
                this.modal = res.data.modal
            } else {
                this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
            }
        },
        submit() {
            if (this.text.title == '' || this.text.content == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert all fields', { duration: 3000, permanent: false })
            } else {
                this.text.id = this.siteId
                Axios.put(this.apiBase + 'setText', this.text).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', '', 'Successfully Created!', { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Ooop!', 'Something went wrong', { duration: 3000, permanent: false })
                    }
                })
            }
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
        modalSetting() {
            Axios.put(this.apiBase + 'modal-set', {id: this.siteId, type: 2, modal: this.modal}).then(res => {
                if (res.statusText == 'OK') {
                    this.$store.dispatch('site/setModalWidth', {
                        modalWidth: this.modal
                    })
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        }
    }
}
</script>

<style lang="css">
    @media (max-width: 991px) {
        .w-md-100 {
            width: 100%!important;
        }
    }
</style>