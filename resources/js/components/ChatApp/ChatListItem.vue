<template>
    <div class="mt-auto">
        <div class="chat-list-item" v-for="msg in contentData" :key="msg.timetoken" :class="getClass(msg)">
            <div class="d-flex mb-3">
                <img :src="'/assets/img/avatar/' + msg.meta.avatar" alt="" class="chat-avatar mr-3">
                <div>
                    <div class="d-flex mb-2">
                        <div class="chat-user-name">{{msg.meta.userName}}</div>
                        <div class="chat-time mt-auto">{{timeDiff(msg.timetoken)}}</div>
                    </div>
                    <div class="chat-content" v-html="msg.message" />
                </div>
            </div>
        </div>
        <div class="chat-list-item" v-for="msg in currentData" :key="msg.timetoken" :class="getResClass(msg)">
            <div class="d-flex mb-3">
                <img :src="'/assets/img/avatar/' + msg.userMetadata.avatar" alt="" class="chat-avatar mr-3">
                <div>
                    <div class="d-flex mb-2">
                        <div class="chat-user-name">{{msg.userMetadata.userName}}</div>
                        <div class="chat-time mt-auto">{{timeDiff(msg.timetoken)}}</div>
                    </div>
                    <div class="chat-content" v-html="msg.message" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapGetters
} from 'vuex'

export default {
    props: ['contentData', 'currentData'],
    computed: {
        ...mapGetters({
            currentUser: "auth/user"
        })
    },
    methods: {
        timeDiff(past) {
            const msPerMinute = 60 * 1000;
            const msPerHour = msPerMinute * 60;
            const msPerDay = msPerHour * 24;
            const msPerMonth = msPerDay * 30;
            const msPerYear = msPerDay * 365;
            const elapsed = new Date().getTime() - Math.floor(past / 10000);

            if (elapsed < msPerMinute) {
                return 'Just before';   
            }

            else if (elapsed < msPerHour) {
                return Math.round(elapsed/msPerMinute) + ' minutes ago';   
            }

            else if (elapsed < msPerDay ) {
                return Math.round(elapsed/msPerHour ) + ' hours ago';   
            }

            else if (elapsed < msPerMonth) {
                return 'approximately ' + Math.round(elapsed/msPerDay) + ' days ago';   
            }

            else if (elapsed < msPerYear) {
                return 'approximately ' + Math.round(elapsed/msPerMonth) + ' months ago';   
            }

            else {
                return 'approximately ' + Math.round(elapsed/msPerYear ) + ' years ago';   
            }
        },
        getClass(msg) {
            if (this.currentUser && msg.meta.id != this.currentUser.id) {
                return 'mr-auto ml-2'
            } else {
                return 'sended ml-auto mr-2'
            }
        },
        getResClass(msg) {
            if (this.currentUser && msg.userMetadata.id != this.currentUser.id) {
                return 'ml-auto mr-4'
            } else {
                return 'sended'
            }
        }
    }
}
</script>
