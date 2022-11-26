<template>
    <b-modal
        id="sendModal"
        ref="sendModal"
        :title="title"
        modal-class="modal-center"
        size="lg"
    >
        <b-button size="sm" variant="primary" class="float-right" @click="generate()">Code Generate</b-button>
        <b-table-simple striped caption-top responsive class="my-2">
            <b-thead>
                <b-th>Name</b-th><b-th>Email</b-th><b-th>Phone</b-th><b-th>Task</b-th><b-th>Verify Code</b-th>
            </b-thead>
            <b-tbody>
                <b-tr v-for="(data, index) in sendData" :key="index">
                    <b-th>{{users[index].name}}</b-th>
                    <b-th>{{users[index].email}}</b-th>
                    <b-th>{{users[index].phone}}</b-th>
                    <b-th class="w-25">
                        <v-select v-model="data.type" :options="categories" class="w-100" />
                    </b-th>
                    <b-th><b-form-input v-model="data.code" class="w-100" /></b-th>
                </b-tr>
            </b-tbody>
        </b-table-simple>
        <template v-slot:modal-footer="{ cancel }">
            <b-button size="md" @click="cancel()">
                Cancel
            </b-button>
            <b-button size="md" variant="primary" @click="send(sendData)">
                Send
            </b-button>
        </template>
    </b-modal>
</template>

<script>
import axios from 'axios'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

export default {
    components: {
        "v-select": vSelect
    },
    props: [
        'title', 'users', 'id'
    ],
    data() {
        return {
            fields: ['name', 'email', 'phone', 'field'],
            categories: [
                { label: 'Exhibit', value: 0 },
                { label: 'Sponsor', value: 1 },
                { label: 'Poster', value: 2 },
            ],
            sendData: []
        }  
    },
    methods: {
        cancel() {
            this.$refs['sendModal'].hide()
        },
        generate() {
            for(let i = 0; i < this.sendData.length; i++) {
                this.sendData[i].code = Math.round(Math.random() * 1000000)
            }
        },
        send(item) {
            var status = true
            this.users.forEach(element => {
                if (element.status == 0) {
                    status = false
                }
            });
            if (status) {
                var filled = true
                item.forEach(data => {
                    if (data.code == '' || data.type == '') {
                        filled = false
                    }
                })
                if (!filled) {
                    this.$notify('primary filled', '', 'Please fill the all forms', { duration: 3000, permanent: false });
                } else {
                    axios.post('/domain/link', {data: item, id: this.id}).then(res => {
                        if (res.statusText == 'OK') {
                            this.$notify('primary filled', 'Success!', 'Successfully Done!', { duration: 3000, permanent: false });
                            this.$refs['sendModal'].hide();
                        } else {
                            this.$notify('primary filled', 'Sorry!', 'Something went wrong!', { duration: 3000, permanent: false });
                        }
                    })
                }
            } else {
                this.$notify('primary filled', '', 'Company Data is not approved, please wait for a few days', { duration: 3000, permanent: false });
            }
        },
    },
    watch: {
        users() {
            for(let i = 0; i < this.users.length; i++) {
                this.sendData.push({type: '', code: ''})
            }
        }
    }

}
</script>

<style lang="css">
    .table-responsive {
        padding-bottom: 6rem;
    }
</style>