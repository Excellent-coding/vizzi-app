<template>
    <div :style="getStyle()" class="user-list">
        <div class="head">
            <img :src="'/data/' + logo" class="logo" v-if="logo" />
            <div v-html="getTitle()" class="logo chat-header" v-else />
            <h4 class="mb-0 ml-2">Visitor Information</h4>
        </div>
        <hr />
        <div class="p-1" v-if="user && user.avatar">
            <div class="my-auto d-flex">
                <img :src="'/assets/img/avatar/' + user.avatar" class="avatar mr-3" />
                <h6 class="my-auto">{{user.name}}</h6>
            </div>
            <div class="mt-5">
                <div class="d-flex mb-3">
                    <i class="simple-icon-location-pin p-2 icon icon-red mr-4"></i>
                    <h6 class="my-auto text-muted">{{user.email}}</h6>
                </div>
                <div class="d-flex mb-3" v-if="user.address">
                    <i class="iconsminds-envelope p-2 icon icon-purple mr-2"></i>
                    <h5 class="my-auto text-muted">{{user.address}}</h5>
                </div>
            </div>
            <div class="mt-5 d-flex">
                <div class="icon-red p-4 border-rounded mr-2">
                    <h3>24</h3>
                    <h6>Past Visits</h6>
                </div>
                <div class="icon-purple p-4 border-rounded">
                    <h3>5</h3>
                    <h6>Past Chats</h6>
                </div>
            </div>
        </div>
        <!-- <div class="d-flex user-item p-1" v-for="user in managers" :key="user.id">
            <img :src="'assets/img/avatar/' + user.avatar" class="avatar mr-3" />
            <div class="my-auto">
                <h6>{{user.name}}</h6>
                <p>{{userType(user)}}</p>
            </div>
        </div> -->
    </div>
</template>

<script>
import {
    mapGetters
} from 'vuex'

import ChatSidebarHeader from '../../components/ChatApp/ChatSidebarHeader'

export default {
    components: {
        'list-header': ChatSidebarHeader
    },
    props: ['height', 'logo', 'title', 'user'],
    computed: {
        ...mapGetters({
            // managers: 'chat/managers',
            adminUser: 'chat/admin',
            currentUser: 'auth/user'
        }),
    },
    methods: {
        getStyle() {
            return {
                'height': 'calc(100vh - ' + this.height + 'px)',
                'width': '450px'
            }
        },
        getTitle() {
            return this.title.charAt(0).toUpperCase() + this.title.charAt(1)
        },
        userType(user) {
            if (user.role < 3) {
                return 'Admin'
            } else {
                if (user.type == 0) {
                    return 'Booth Designer'
                } else if (user.type == 1) {
                    return 'Sponsor Designer'
                } else {
                    return 'Poster Designer'
                }
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
    .icon {
        border-radius: 50%;
    }
    .icon-purple {
        background: rgba(0, 0, 255, .08);
        color: purple;
    }
    .icon-red {
        background: rgba(255, 0, 0, .08);
        color: red;
    }
    .border-rounded {
        border-radius: 1rem;
    }
</style>