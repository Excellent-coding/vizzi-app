<template>
        <b-row>
            <b-colxx xxs="12" xs="6" lg="4" v-for="item in exhibitData" :key="item.id">
                <b-card class="mb-4" no-body>
                    <b-card-header class="text-center h4 mt-2">{{item.title}}</b-card-header>
                    <div class="position-relative" v-if="item.template">
                        <img :src="'/data/'+item.template.media" class="card-img-top" />
                        <template v-if="item.data">
                            <div
                                :style="itemStyle(element)"
                                class="position-absolute" 
                                v-for="(element, index) in JSON.parse(item.data)"
                                :key="index"
                            >
                                <img :src="'/data/'+element.url" class="w-100 h-100" style="object-fit: cover" v-if="element.url.substring(0, 4) != 'http'" />
                                <img :src="element.url" class="w-100 h-100" style="object-fit: cover" v-else />
                            </div>
                        </template>
                    </div>
                    <b-card-body class="py-1">
                        <div class="d-flex">
                            <img :src="'/assets/img/avatar/'+item.user.avatar" class="user-avatar"/>
                            <div class="my-auto ml-2">
                                <h6 class="mb-0 card-subtitle">{{item.user.name}}</h6>
                                <p class="mb-0">{{item.user.email}}</p>
                            </div>
                        </div>
                        <p class="card-text text-muted text-small mb-0 font-weight-light">Created at {{item.created_at | moment('MM.DD.YYYY')}}</p>
                    </b-card-body>
                </b-card>
            </b-colxx>
        </b-row>
</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'

export default {
    metaInfo () {
        return { title: `Vizzi Exhibit` }
    },
    data() {
        return {
            id: null,
            type: null,
            apiBase: '/exhibit-hall/',
            exhibitData: []
        }
    },
    mounted() {
        this.id = this.$route.params.id
        var type = this.$route.params.type
        if (type == 'booth') {
            this.type = 0
        } else if (type == 'sponsor') {
            this.type = 1
        } else {
            this.type = 2
        }
        this.init()
    },
    methods: {
        init() {
            Axios.post(this.apiBase + 'get', {type: this.type, id: this.id}).then(res => {
                if (this.type < 2) {
                    this.exhibitData = res.data.booth_items
                } else {
                    this.exhibitData = res.data.poster_items
                }
            })
        },
        itemStyle(data) {
            return {
                'top': 'calc(' + Math.min(data.y, data.ey) + '%)',
                'left': 'calc(' + Math.min(data.x, data.ex) + '%)',
                'width': 'calc(' + Math.abs(data.ex - data.x) + '%)',
                'height': 'calc(' + Math.abs(data.ey - data.y) + '%)'
            }
        },
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        }),
    }
}
</script>