<template>
    <div :style="getStyle()" class="user-list" v-if="userData">
        <div class="head">
            <img :src="'/assets/img/avatar/' + userData.avatar" class="user-avatar" />
            <div class="text text-black mt-3">{{userData.name}}</div>
        </div>
        <hr />
        <div class="d-flex p-1"><h6>Email : </h6><div> </div></div>
        <div class="d-flex p-1"><h6>Phone : </h6><div> </div></div>
        <div class="d-flex p-1"><h6>Address : </h6><div> </div></div>
        <div class="d-flex p-1"><h6>Zipcode : </h6><div> {{userData.zipcode}}</div></div>
    </div>
</template>

<script>
import {
    mapGetters
} from 'vuex'

import ChatSidebarHeader from '../../components/ChatApp/ChatSidebarHeader'
import Axios from 'axios'

export default {
    data() {
        return {
            userData: null
        }
    },
    components: {
        'list-header': ChatSidebarHeader
    },
    props: ['height', 'logo', 'title'],
    computed: {
        ...mapGetters({
            selectedUser: 'chat/selectedUser'
        }),
    },

    watch: {
        selectedUser(val) {
            Axios.post('/site/user', {id: val}).then(res => {
                this.userData = res.data
            })
        }
    },
    methods: {
        getStyle() {
            return {
                'height': 'calc(100vh - ' + this.height + 'px)',
                'width': '320px',
                'position': 'fixed'
            }
        }
    }
}
</script>

<style lang="css">
    .user-list {
        position: absolute;
        right: 0;
        background: white;
        padding: 24px;
    }
    .user-item:hover {
        background: #f4f6f8;
    }
</style>