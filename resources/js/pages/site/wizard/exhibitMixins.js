import { mapGetters } from 'vuex'
import Axios from 'axios'
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import Header from "../../../containers/Web/DomainHeader"
import QuillEditor from '../../../components/Pages/QuillEditor'
import Draggable from "vuedraggable"

export default {
    components: {
        "v-select": vSelect,
        'b-header': Header,
        "b-quill-editor": QuillEditor,
        Multiselect,
        Draggable
    },
    data() {
        return {
            activeItems: [],
            assetItem: {
                id: 0,
                title: '',
                file: null
            },
            assetTitle: 'Add new Asset',
            apiBase: '/wizard/',
            booth: '',
            booths: [],
            boothData: [],
            boothModels: [],
            boothFields: [
                '#',
                { key: 'title', sortable: true },
                { key: 'view', sortable: true },
                { key: 'action', sortable: false }
            ],
            contact: { name: '', email: '', phone: '' },
            createType: 0,
            currentIndex: 0,
            data: [
                {
                    x: 0,
                    ex: 0,
                    y: 0,
                    ey: 0,
                    url: null
                }
            ],
            design: null,
            extension: '',
            editorId: null,
            editData: {},
            editId: 0,
            fields: [
                '#',
                { key: 'name', sortable: true },
                { key: 'email', sortable: true },
                { key: 'status', sortable: true },
                { key: 'action', sortable: false }
            ],
            formData: new FormData(),
            file: null,
            hallIndex: 0,
            hallTitle: 'Create New Hall',
            hallItem: {
                name: '',
                items: [],
                list: [],
                id: 0,
                siteId: null,
                type: null
            },
            hallBoothItems: [],
            hallData: [],
            hallFields: [
                '#',
                { key: 'name', sortable: true },
                { key: 'status', sortable: true },
                { key: 'view', sortable: true },
                { key: 'action', sortable: false }
            ],
            headerStyle: {
                bg: '#922c88',
                height: 32,
                btn: {
                    bg: '#d683ce',
                    color: '#ffffff',
                    radius: 4,
                    space: 8,
                    boxShadow: true,
                    hasBg: true,
                    fontSize: 13,
                    paddingX: 2,
                    paddingY: 8
                }
            },
            header: [],
            isConfirmed: false,
            isConfirm: [],
            isContact: false,
            id: null,
            isOnline: [],
            isSelect: [],
            isClick: false,
            isArea: false,
            isUpload: false,
            isUploaded: false,
            items: [],
            isDelStatus: 0,
            menuTitle: '',
            member: { name: '', email: '' },
            menus: [],
            modalCurrentIndex: null,
            modalId: 0,
            memberId: 0,
            members: [],
            modalFields: [
                '#',
                { key: 'name', sortable: true },
                { key: 'email', sortable: true }
            ],
            managers: [],
            memberFields: [
                '#',
                { key: 'name', sortable: true },
                { key: 'email', sortable: true },
                { key: 'status', sortable: true },
                { key: 'action', sortable: false }
            ],
            mainUsers: [],
            notifyText: 'Successfully Created!',
            note: '',
            onView: false,
            pendingItems: [],
            status: ['Active', 'Inactive', 'Delete'],
            setting: 'menus',
            selected: false,
            selectedItems: [],
            settingText: {
                title: '',
                content: '',
                id: null,
                type: null
            },
            selectedMenus: [],
            settingNote: '',
            submitText: 'Create',
            tabs: [],
            tabIndex: 0,
            totalItems: [],
            tabModel: 0,
            tabMenu: 0,
            temp: '',
            templates: [],
            template: '',
            text: { title: '', content: '' },
            titleText: 'Create Menu',
            type: 0,
            userBooths: [],
            userFields: [
                '#',
                { key: 'title', sortable: true },
                { key: 'status', sortable: true },
                { key: 'mainManager', sortable: true },
                { key: 'action', sortable: false}
            ],
            users: [],
            url: null,
            urls: [],
            userData: [],
            userManagementData: [],
            userMembers: [],
            uploadStatus: false,
            uploadExtension: '',
            uploadUrl: null,
            uploadFormData: new FormData(),
            viewData: {},
            viewItem: {},
            workingItems: [],
        }
    },
    methods: {
        mainInit() {
            Axios.post(this.apiBase + 'get', {id: this.siteId, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.note = res.data.domain['note' + this.type]
                    this.users = res.data.users
                    this.booths = res.data.booths
                    this.text.title = res.data.domain['title' + this.type]
                    this.text.content = res.data.domain['content' + this.type]
                    if (
                        !localStorage.getItem(this.storageName) && 
                        this.text.title && this.text.content &&
                        this.text.title != '' && this.text.content != ''
                    ) {
                        this.$refs['guide_modal'].show()
                    }
                    this.templates = res.data.templates
                    if (res.data.domain.contact0 == 1) {
                        this.isContact = true
                    }
                }
            })
        },
        boothInit() {
            Axios.post(this.apiBase + 'getAll', { id: this.siteId, type: this.type }).then(res => {
                if (res.status == 200 && this.user) {
                    if (this.user.role < 3) {
                        this.workingItems = res.data.workingItems
                        this.activeItems = res.data.activeItems
                        this.pendingItems = res.data.pendingItems
                        this.hallData = res.data.hallData
                    } else {
                        var item = res.data
                        if (item.status == 0) {
                            this.workingItems.push(item)
                        } else if (item.status == 1) {
                            this.activeItems.push(item)
                        } else {
                            this.pendingItems.push(item)
                        }
                    }
                }
            })
        },
        modelInit() {
            Axios.post(this.apiBase + 'models', {siteId: this.siteId, type: this.type}).then(res => {
                this.boothModels = res.data
            })
        },
        async settingInit() {
            const res = await Axios.post(this.apiBase + 'get-setting', {id: this.siteId, type: this.type})
            if (res.status == 200) {
                this.settingText.title = res.data.title
                this.settingText.content = res.data.content
                this.settingNote = res.data.note
            } else {
                this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
            }
        },
        async userInit() {
            const res = await Axios.post(this.apiBase + 'getBoothData', {id: this.siteId, type: this.type})
            if (res.status == 200) {
                this.userBooths = res.data
            } else {
                this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
            }
        },

        itemStyle(data) {
            return {
                'top': 'calc(' + Math.min(data.y, data.ey) + '%)',
                'left': 'calc(' + Math.min(data.x, data.ex) + '%)',
                'width': 'calc(' + Math.abs(data.ex - data.x) + '%)',
                'height': 'calc(' + Math.abs(data.ey - data.y) + '%)'
            }
        },
        approve(id) {
            Axios.post(this.apiBase + 'approve', {id: id, siteId: this.siteId, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Approved!', { duration: 3000, permanent: false })
                    this.workingItems = res.data.workingItems
                    this.activeItems = res.data.activeItems
                    this.pendingItems = res.data.pendingItems
                }
            })
        },
        reject(id) {
            Axios.post(this.apiBase + 'reject', {id: id, siteId: this.siteId, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Data is rejected!', { duration: 3000, permanent: false })
                    this.workingItems = res.data.workingItems
                    this.activeItems = res.data.activeItems
                    this.pendingItems = res.data.pendingItems
                }
            })
        },
        activate(id) {
            Axios.post(this.apiBase + 'activate', {id: id, siteId: this.siteId, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Activated!', { duration: 3000, permanent: false })
                    this.workingItems = res.data.workingItems
                    this.activeItems = res.data.activeItems
                    this.pendingItems = res.data.pendingItems
                }
            })
        },
        deactivate(id) {
            Axios.post(this.apiBase + 'deactivate', {id: id, siteId: this.siteId, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Data is deactivated!', { duration: 3000, permanent: false })
                    this.workingItems = res.data.workingItems
                    this.activeItems = res.data.activeItems
                    this.pendingItems = res.data.pendingItems
                }
            })
        },
        remove(id) {
            this.currentIndex = id
            if (!this.isConfirmed) {
                this.$refs.confirm_modal.show()
            } else {
                Axios.post(this.apiBase + 'remove', { id: id, siteId: this.siteId, type: this.type }).then(res => {
                    if (res.status == 200) {
                        this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                        this.workingItems = res.data.workingItems
                        this.activeItems = res.data.activeItems
                        this.pendingItems = res.data.pendingItems
                    }
                })
                this.isConfirmed = false
                this.currentIndex = null
            }
        },
        async edit(item) {
            await this.$store.dispatch('booth/setId', {
                id: item.id,
            })
            if (item.template) {
                if (this.user.role < 3) {
                    this.$router.push('/app/wizard/' + this.text +'/edit/'+item.id)
                } else {
                    this.$router.push('/wizard/' + this.text +'/edit/'+item.id)
                }
            } else {
                if(item.template_id === null || item.template_id === undefined) {
                    if (this.user.role < 3) {
                        this.$router.push({ name: this.text + '.models' })
                    } else if (this.user.role == 3) {
                        this.$router.push({ name: 'wizard.' + this.text + '.models' })
                    }
                } else {
                    if (this.user.role < 3) {
                        this.$router.push('/app/wizard/' + this.text +'/edit/'+item.id)
                    } else {
                        this.$router.push('/wizard/' + this.text +'/edit/'+item.id)
                    }
                }
            }
        },
        editItem(item) {
            var items = []
            this.activeItems.forEach(element => {
                item.booth_items.forEach(booth => {
                    if (element.id == booth.id) {
                        items.push(element.title)
                    }
                })
            })
            this.hallItem = {
                name: item.name,
                items: items,
                list: [],
                id: item.id,
                siteId: null
            }
            this.$refs['hall_modal'].show()
        },
        hideModal(ref) {
            this.$refs[ref].hide()
            this.memberId = 0
            this.assetItem = {
                id: 0,
                title: '',
                file: null
            }
        },
        guideConfirm() {
            localStorage.setItem(this.storageName, true)
            this.$refs['guide_modal'].hide()
        },
        boothConfirm() {
            this.isConfirmed = true
            this.$refs['confirm_modal'].hide()
            this.remove(this.currentIndex)
        },
        editMember(item) {
            this.buttonText = 'Change'
            this.memberId = item.id
            this.member = {name: item.name, email: item.email}
        },
        cancelMember() {
            this.buttonText = 'Add to Booth'
            this.member = {name: '', email: ''}
            this.memberId = 0
        },
        delMember(data) {
            this.memberId = data.item.id
            this.currentIndex = data.index
            this.$refs['confirm_modal'].show()
        },
        async confirm() {
            if (this.tabIndex == 0) {
                Axios.post(this.apiBase + 'del', {id: this.memberId, type: this.type, siteId: this.siteId}).then(res => {
                    if (res.status == 200) {
                        this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                        this.users = res.data
                        this.members.splice(this.currentIndex, 1)
                    } else {
                        this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                    }
                    this.$refs['confirm_modal'].hide()
                })
            } else if (this.tabIndex == 1) {
                const res = await Axios.post(this.apiBase + 'delTemp', {id: this.id})
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                    this.boothModels.splice(this.modalCurrentIndex, 1)
                    this.hideModal('conform_modal')
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            }
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
            this.$refs['user_modal'].hide()
        },

        request() {
        },
        async upload() {
            if (this.booth == '') {
                this.$notify('primary filled', '', 'Please Insert ' + this.modalText + ' Name!', { duration: 3000, permanent: false })
            } else {
                const res = await Axios.post(this.apiBase + 'setBooth', {title: this.booth, id: this.siteId, type: this.type, members: this.members, custom: true})
                if (res.status != 200) {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                } else {
                    await this.$store.dispatch('booth/setId', {
                        id: res.data.id,
                    })
                    if (this.user.role < 3) {
                        this.$router.push('/app/wizard/' + this.urlText + '/edit/'+res.data.id)
                    } else {
                        this.$router.push('/wizard/' + this.urlText + '/edit/'+res.data.id)
                    }
                }
            }
        },
        async getModel() {
            if (this.booth == '') {
                this.$notify('primary filled', '', 'Please Insert ' + this.modalText + ' Name!', { duration: 3000, permanent: false })
            } else {
                const res = await Axios.post(this.apiBase + 'setBooth', {title: this.booth, id: this.siteId, type: this.type, members: this.members})
                if (res.status != 200) {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                } else {
                    await this.$store.dispatch('booth/setId', {
                        id: res.data.id,
                    })
                    if(res.data.template_id === null || res.data.template_id === undefined) {
                        if (this.user.role < 3) {
                            this.$router.push({ name: this.urlText +'.models' })
                        } else if (this.user.role == 3) {
                            this.$router.push({ name: 'wizard.' + this.urlText + '.models' })
                        }
                    } else {
                        if (this.user.role < 3) {
                            this.$router.push('/app/wizard/' + this.urlText + '/edit/'+res.data.id)
                        } else {
                            this.$router.push('/wizard/' + this.urlText + '/edit/'+res.data.id)
                        }
                    }
                }
            }
        },

        async userSubmit(file) {
            var no = 2
            while(file['B' + no] && file['C' + no] && file['D' + no]) {
                this.boothData.push({title: file['B' + no].v})
                this.userManagementData.push({
                    name: file['C' + no].v,
                    email: file['D' + no].v
                })
                no++
            }
            const res = await Axios.post(this.apiBase + 'exhibit-upload', {boothData: this.boothData, userData: this.userManagementData, type: this.type, id: this.siteId})
            if (res.data.errors) {
                res.data.errors.forEach(element => {
                    this.$notify('primary filled', '', element, { duration: 3000, permanent: false })
                });
            }
            if (res.status == 200) {
                this.userBooths = res.data
            }
        },
        showContact() {
            Axios.put(this.apiBase + 'setContact', {id: this.siteId, status: !this.isContact, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.isContact = !this.isContact
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        async addMember() {
            if (this.booth == '') {
                this.$notify('primary filled', '', 'Please Insert ' + this.modalText + ' Name!', { duration: 3000, permanent: false })
            } else {
                var error = false
                for (const [key, value] of Object.entries(this.member)) {
                    if (value == '') {
                        error = true
                    }
                }
                if (error) {
                    this.$notify('primary filled', '', 'Please fill the forms', { duration: 3000, permanent: false })
                } else {
                    var res = await Axios.post(this.apiBase + 'member', {memberId: this.memberId, member: this.member, id: this.siteId, booth: this.booth, type: this.type})
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
        select(id) {
            Axios.post(this.apiBase + 'setTemp', {id: this.boothId, temp_id: id}).then(res => {
                if (res.status == 200) {
                    if (this.user.role < 3) {
                        this.$router.push('/app/wizard/' + this.urlText + '/edit/' + this.boothId)
                    } else {
                        this.$router.push('/wizard/' + this.urlText + '/edit/' + this.boothId)
                    }
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        del(index, id) {
            this.modalCurrentIndex = index
            this.id = id
        },
        async modelConfirm() {
            const res = await Axios.post(this.apiBase + 'del-temp', {id: this.id})
            if (res.status == 200) {
                this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                this.boothModels.splice(this.modalCurrentIndex, 1)
                this.hideModal('conform_modal')
            } else {
                this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
            }
        },
        create() {
            this.formData.append('siteId', this.siteId)
            this.formData.append('type', this.type)
            this.formData.append('id', this.modalId)
            Axios.post(this.apiBase + 'upload', this.formData).then(res => {
                if (res.statusText == 'OK') {
                    this.boothModels = res.data
                    this.$notify('primary filled', 'Success!', this.modalText + ' model is successfully uploaded', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Ooop...!', 'Something went wrong!', { duration: 3000, permanent: false })
                }
                this.formData = new FormData()
                this.isUploaded = false
                this.modalId = 0
            })
        },
        cancel() {
            this.formData = new FormData()
            this.isUploaded = false
            this.modalId = 0
        },
        editModal(id) {
            this.modalId = id
        },
        onModalFileChange(e) {
            this.file = e.target.files[0]
            this.extension = this.file.name.split('.').pop();

            if (this.extension != 'mp4') {
                if (this.file.size / 10000 > 5) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
                }
            } else {
                if (this.file.size / 100000 > 1) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
                }
            }
            this.url = URL.createObjectURL(this.file)
            this.formData.set('file', this.file)
            this.isUploaded = true
        },
        uploadConfirm() {
            this.uploadFormData.append('siteId', this.siteId)
            this.uploadFormData.append('type', this.type)
            axios.post(this.apiBase + 'upload', this.uploadFormData).then(res => {
                if (res.statusText == 'OK') {
                    this.$notify('primary filled', 'Success!', this.modalText + ' model is successfully uploaded', { duration: 3000, permanent: false })
                    this.$router.push({name: this.urlText + '.models'})
                } else {
                    this.$notify('primary filled', 'Ooop...!', 'Something went wrong!', { duration: 3000, permanent: false })
                }
            })
        },
        onFileChange(e) {
            const file = e.target.files[0]
            this.uploadExtension = file.name.split('.').pop();

            if (this.uploadExtension != 'mp4') {
                if (file.size / 10000 > 5) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
                }
            } else {
                if (file.size / 100000 > 1) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
                }
            }
            this.uploadUrl = URL.createObjectURL(file)
            this.uploadFormData.set('file', file)
            this.uploadStatus = true
        },

        settingSubmit() {
            this.settingText.type = this.type
            this.settingText.id = this.siteId
            if (this.settingText.title == '' || this.settingText.content == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert all fields', { duration: 3000, permanent: false })
            } else {
                this.settingText.id = this.siteId
                Axios.put(this.apiBase + 'setText', this.settingText).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Ooop!', 'Something went wrong', { duration: 3000, permanent: false })
                    }
                })
            }
        },
        submitNote() {
            Axios.put(this.apiBase + 'note', {note: this.settingNote, id: this.siteId, type: 0}).then(res => {
                if (res.statusText == 'OK') {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        boothStatus(id, status) {
            Axios.post(this.apiBase + 'boothStatus', {id: id, siteId: this.siteId, status: status, type: 0}).then(res => {
                if (res.status == 200) {
                    this.userBooths = res.data
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        userStatus(id, status) {
            Axios.post(this.apiBase + 'userStatus', {id: id, siteId: this.siteId, status: status, type: 0}).then(res => {
                if (res.status == 200) {
                    this.userBooths = res.data
                } else {
                    this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        addHall() {
            if (this.hallItem.name == '' || this.hallItem.items.length == 0) {
                this.$notify('primary filled', '', 'Please Insert Fields', { duration: 3000, permanent: false })
            } else {
                this.hallItem.siteId = this.siteId
                this.hallItem.type = this.type
                this.hallItem.items.forEach(item => {
                    this.activeItems.forEach(element => {
                        if (element.title == item) {
                            this.hallItem.list.push(element.id)
                        }
                    })
                })
                Axios.post(this.apiBase + 'hall-set', this.hallItem).then(res => {
                    this.$notify('primary filled', '', 'Successfully Saved', { duration: 3000, permanent: false })
                    this.hallData = res.data
                })
                this.hallItem = {
                    name: '',
                    items: [],
                    list: [],
                    id: 0,
                    siteId: null
                }
                this.$refs['hall_modal'].hide()
            }
        },
        viewBoothItem(item) {
            this.viewData = item
            this.onView = true
        },
        delItem(id) {
            Axios.post(this.apiBase + 'hall-item-del', {id: id, siteId: this.siteId, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.hallData = res.data
                    this.$notify('primary filled', '', 'Successfully Deleted', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        hallStatus(id, status) {
            Axios.post(this.apiBase + 'hall-status', {id: id, status: status, siteId: this.siteId, type: this.type}).then(res => {
                if (res.status == 200) {
                    this.hallData = res.data
                    this.$notify('primary filled', '', 'Successfully Changed', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        setBooth() {
            this.formData.append('id', this.id)
            this.formData.append('data', JSON.stringify(this.data))
            Axios.post(this.apiBase + 'set', this.formData).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Saved', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        publish() {
            this.formData.append('id', this.id)
            this.formData.append('siteId', this.siteId)
            Axios.post(this.apiBase + 'publish', this.formData).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', 'Thank you!', 'You have successfully completed your Booth, our team will review.', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        },
        view(item) {
            this.viewItem = item
            this.extension = this.viewItem.item.split('.').pop()
        },
        onAssetFileUpload(e) {
            this.assetItem.file = e.target.files[0]
        },
        customUpload(e) {
            const file = e.target.files[0]
            e.target.value = ''
            if (file.size / 10000 > 1) {
                this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
            }
            this.design = URL.createObjectURL(file)
            this.formData.append('template', file)
        },
        choose() {
            if (this.user.role < 3) {
                this.$router.push('/app/wizard/booth/models')
            } else {
                this.$router.push('/wizard/booth/models')
            }
        },
        getArea(e) {
            if (this.isSelect[this.currentIndex]) {
                this.isClick = !this.isClick
                var clientHeight = document.getElementById('content').clientHeight
                var clientWidth = document.getElementById('content').clientWidth
                const x = e.layerX / clientWidth * 100
                const y = e.layerY / clientHeight * 100

                if (this.isClick) {
                    this.data[this.currentIndex].x = x
                    this.data[this.currentIndex].y = y
                    this.data[this.currentIndex].ex = x
                    this.data[this.currentIndex].ey = y
                } else {
                    this.data[this.currentIndex].ex = x
                    this.data[this.currentIndex].ey = y
                }
            }
        },
        areaStyle(data) {
            return {
                'top': 'calc(' + Math.min(data.y, data.ey) + '%)',
                'left': 'calc(' + Math.min(data.x, data.ex) + '%)',
                'width': 'calc(' + Math.abs(data.ex - data.x) + '%)',
                'height': 'calc(' + Math.abs(data.ey - data.y) + '%)',
                'cursor': 'pointer'
            }
        },
        onEditorChange({ editor, html, text }) {
            this.header.forEach(menu => {
                if (this.editorId == menu.id) {
                    menu.contentData = html
                }
            })
        },
        selectAll(isToggle) {
            if (this.type == 'modal') {
                if (this.selectedMenus.length >= this.menus.length) {
                    if (isToggle) this.selectedMenus = [];
                } else {
                    this.selectedMenus = this.menus.map(x => x.id);
                }
            } else {
                if (this.selectedItems.length >= this.items.length) {
                    if (isToggle) this.selectedItems = [];
                } else {
                    this.selectedItems = this.items.map(x => x.id);
                }
            }
        },
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                    break;
                case "undo":
                    if (this.type == 'modal') {
                        this.selectedMenus = [];
                    } else {
                        this.selectedItems = [];
                    }
                    break;
            }
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedMenus.length > 0) {
                if (this.type == 'modal') {
                    let itemsForToggle = this.menus;
                    var start = this.getIndex(itemId, itemsForToggle, "id");
                    var end = this.getIndex(
                        this.selectedMenus[this.selectedMenus.length - 1],
                        itemsForToggle,
                        "id"
                    );
                    itemsForToggle = itemsForToggle.slice(
                        Math.min(start, end),
                        Math.max(start, end) + 1
                    );
                    this.selectedMenus.push(
                        ...itemsForToggle.map(item => {
                            return item.id;
                        })
                    );
                } else {
                    let itemsForToggle = this.items;
                    var start = this.getIndex(itemId, itemsForToggle, "id");
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
                }
            } else {
                if (this.type == 'modal') {
                    if (this.selectedMenus.includes(itemId)) {
                        this.selectedMenus = this.selectedMenus.filter(x => x !== itemId);
                    } else this.selectedMenus.push(itemId);
                } else {
                    if (this.selectedItems.includes(itemId)) {
                        this.selectedItems = this.selectedItems.filter(x => x !== itemId);
                    } else this.selectedItems.push(itemId);
                }
            }
        },
        initData() {
            if (this.type == 'content') {
                this.assetItem = {}
            } else {
                this.editData = {}
            }
        },
        getType(file) {
            const extension = file.split('.').pop()
            if (extension == 'mp4') {
                return '/assets/img/type/video.png'
            } else if (extension == 'pdf') {
                return '/assets/img/type/pdf.png'
            } else {
                return '/assets/img/type/image.png'
            }
        },
        getTypeText(file) {
            const extension = file.split('.').pop()
            if (extension == 'mp4') {
                return 'video'
            } else if (extension == 'pdf') {
                return 'pdf'
            } else {
                return 'image'
            }
        },
        getStyle(file) {
            const extension = file.split('.').pop()
            if (extension == 'pdf') {
                return {
                    'letter-spacing': '4px',
                    'background': this.headerStyle.bg
                }
            } else {
                return {
                    'letter-spacing': '1px',
                    'background': this.headerStyle.bg
                }
            }
        },
        getHeader() {
            let headerArray = []
            let sourceArray = []
            this.header.forEach(menu => {
                headerArray.push(menu.id)
            })
            this.menus.forEach(menu => {
                sourceArray.push(menu.id)
            })
            Axios.post(this.apiBase + 'order', { header: headerArray, source: sourceArray, id: this.id }).then(res => {
                if (res.status == 200) {
                    this.header = res.data.header
                }
            })
        },
        closeTab() {
            if (this.isDelStatus == 0 && !this.isConfirmed) {
                this.isDelStatus = 2
                this.$refs['confirm_modal'].show()
            } else {
                var id = this.tabs[this.tabMenu][this.tabModel].id
                Axios.post(this.apiBase + 'delTab', { delId: id, id: this.id }).then(res => {
                    this.header = res.data.header
                })
                this.isDelStatus = 0
                this.isConfirmed = false
                this.$refs['confirm_modal'].hide()
            }
        },
        setStatus(status) {
            if (status == 2 && !this.isConfirmed) {
                this.isDelStatus = 1
                this.$refs['confirm_modal'].show()
            } else {
                if (this.type == 'modal') {
                    Axios.post(this.apiBase + 'status', { status: status, items: this.selectedMenus, id: this.id }).then(res => {
                        this.menus = res.data.resource
                        this.header = res.data.data.header
                        this.selectedMenus = []
                    })
                } else {
                    Axios.post(this.apiBase + 'mediaStatus', { status: status, items: this.selectedItems, id: this.id }).then(res => {
                        if (res.status == 200) {
                            this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                            this.header = res.data.header
                            this.selectedItems = []
                        } else {
                            this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                        }
                    })
                }
            }
            this.isConfirmed = false
            this.$refs['confirm_modal'].hide()
        },
        addTab(index) {
            this.tabs[index].push(
                { title: 'Tab' }
            )
            var tabs = this.tabs
            this.tabs = []
            this.tabs = tabs
            this.setContent()
        },
        updateTitle(title) {
            if (title == '') {
                this.$notify('primary filled', '', 'Please insert the title', { duration: 3000, permanent: false })
            } else {
                var tabs = this.tabs
                this.tabs = []
                this.tabs = tabs
                this.setContent()
            }
        },
        addNewItem(title, editId) {
            Axios.post(this.apiBase + 'setHeader', { title: title, id: this.id, editId: editId }).then(res => {
                if (res.data.resource && res.data.data) {
                    this.menus = res.data.resource
                    this.header = res.data.data.header
                    this.selectedMenus = []
                    this.hideModal('add_modal')
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                }
            })
        },
        addNewAssetItem(item) {
            let onAdd = true
            if (item.title == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert file title', { duration: 3000, permanent: false })
                onAdd = false
            }
            if (!item.file) {
                this.$notify('primary filled', 'Warn!', 'Please insert file', { duration: 3000, permanent: false })
                onAdd = false
            }
            if (onAdd) {
                var id = this.tabs[this.tabMenu][this.tabModel].id
                var formData = new FormData()
                formData.append('title', item.title)
                formData.append('file', item.file)
                formData.append('id', id)
                formData.append('boothId', this.id)
                formData.append('editId', this.editId)
                Axios.post(this.apiBase + "addAsset", formData).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                        this.header = res.data.header
                        this.hideModal('asset_modal')
                        this.assetItem = {
                            id: 0,
                            title: '',
                            file: null
                        }
                    }
                })
            }
        },
        setHead() {
            Axios.put(this.apiBase + 'head', { style: this.headerStyle, id: this.id }).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                }
            })
        },

        setContent() {
            Axios.post(this.apiBase + 'content', { data: this.header, tabs: this.tabs, id: this.id }).then(res => {
                this.header = res.data.header
            })
        },

        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        },
        getHeaderStyle() {
            return {
                'background-color': this.headerStyle.bg,
                'height': this.headerStyle.height + 'px'
            }
        },
        getButtonStyle() {
            let boxShadow = '0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12)'
            let bg = this.headerStyle.btn.bg
            if (!this.headerStyle.btn.boxShadow) {
                boxShadow = 'none'
            }
            if (!this.headerStyle.btn.hasBg) {
                bg = 'transparent'
            }
            const buttonHeight = 2 * Number(this.headerStyle.btn.paddingX) + Number(this.headerStyle.btn.fontSize)
            return {
                'background-color': bg,
                'color': this.headerStyle.btn.color,
                'margin-left': this.headerStyle.btn.space + 'px',
                'margin-right': this.headerStyle.btn.space + 'px',
                'border-radius': this.headerStyle.btn.radius + 'px',
                'margin-top': ((this.headerStyle.height - buttonHeight) / 2 - 5) + 'px',
                'font-size': this.headerStyle.btn.fontSize + 'px',
                'box-shadow': boxShadow,
                'padding-top': this.headerStyle.btn.paddingX + 'px',
                'padding-bottom': this.headerStyle.btn.paddingX + 'px',
                'padding-left': this.headerStyle.btn.paddingY + 'px',
                'padding-right': this.headerStyle.btn.paddingY + 'px'
            }
        },
        setTitle(id) {
            Axios.put(this.apiBase + 'setTitle', { id: id, title: this.booth.title, type: 0 }).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                }
            })
        },
        addOption() {
            this.data.push({
                x: 0,
                ex: 0,
                y: 0,
                ey: 0,
                url: null
            })
            this.url = null
        },
        delData(index) {
            this.data.splice(index, 1)
            this.formData.delete('media' + index)
        },
        getTab(id, index) {
            this.editorId = id;
            this.tabMenu = index
        },
        returnTab() {
            this.tabIndex--
        },
        nextTab() {
            this.tabIndex++
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            user: 'auth/user',
            boothId: 'booth/getId',
            domain: 'site/getDomain'
        }),
        isSelectedAll() {
            if (this.type == 'modal') {
                return this.selectedMenus.length >= this.menus.length
            } else if (this.type == 'content') {
                if (this.items) {
                    return this.selectedItems.length >= this.items.length
                } else {
                    return true
                }
            }
        },
        isAnyItemSelected() {
            if (this.type == 'modal') {
                return (
                    this.selectedMenus.length > 0 &&
                    this.selectedMenus.length < this.menus.length
                )
            } else if (this.type == 'content') {
                return (
                    this.selectedItems.length > 0 &&
                    this.selectedItems.length < this.items.length
                )
            }
        },
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
        },
        activeItems(val) {
            val.forEach(item => {
                this.hallBoothItems.push(item.title)
            })
        },
        header(val) {
            if (val.length) {
                this.editorId = val[0].id
            }
            this.tabs = []
            val.forEach((element, index) => {
                this.tabs[index] = element.tab
                if (this.tabs[index].length == 0) {
                    this.tabs[index].push({
                        title: 'Resource'
                    })
                    this.tabs[index].push({
                        title: 'Video'
                    })
                }
            });
            if (val.length && val[this.tabMenu] && val[this.tabMenu].tab && val[this.tabMenu].tab[this.tabModel]) {
                this.items = this.header[this.tabMenu].tab[this.tabModel].media
            } else {
                this.items = []
            }
        },
        editData: function (val) {
            if (val.id) {
                this.titleText = "Edit The Title"
                this.menuTitle = val.title
                this.submitText = 'Change'
                this.editId = val.id
            } else {
                this.titleText = "Create Menu"
                this.menuTitle = ''
                this.submitText = 'Create'
                this.editId = 0
            }
        },
        assetItem(val) {
            if (val.id) {
                this.titleText = "Edit The Asset"
                this.menuTitle = val.title
                this.submitText = 'Change'
                this.editId = val.id
                this.notifyText = 'Successfully Changed!'
            } else {
                this.titleText = "Create Menu"
                this.menuTitle = ''
                this.submitText = 'Create'
                this.editId = 0
                this.notifyText = 'Successfully Created!'
            }
        },
        tabMenu(val) {
            if (this.header[val] && this.header[val].tab && this.header[val].tab[this.tabModel]) {
                this.items = this.header[val].tab[this.tabModel].media
            } else {
                this.items = []
            }
        },
        tabModel(val) {
            if (this.header[this.tabMenu] && this.header[this.tabMenu].tab && this.header[this.tabMenu].tab[val]) {
                this.items = this.header[this.tabMenu].tab[val].media
            } else {
                this.items = []
            }
        },
        tabIndex(val) {
            if (val == 0) {
                this.type = 'upload'
            } else if (val == 1) {
                this.type = 'modal'
            } else if (val == 2) {
                this.type = 'content'
            } else if (val == 3) {
                this.type = 'settings'
            } else if (val == 4) {
                this.type = 'publish'
            }
        }
    }
}