<template>
<div class="px-5">
    <div v-for="category in categories" :key="category.id">
        <template v-if="isCategory(category.id)">
            <h4>{{category.category}}</h4>
            <b-row>
                <template v-for="item in items">
                    <b-colxx md="4" sm="12" lg="3" xxs="12" v-if="item.category_id == category.id">
                        <b-card class="mb-4 p-4 text-center" no-body @click="goPage(item.id)">
                            <h6>{{item.title.substring(0, 120)}}</h6>
                            <div class="w-100">
                                <img :src="'/data/' + item.media.split(', ')[0]" alt="Poster" class="w-100 model" v-if="item.type > 1 && item.media && item.media.split(', ').length" />
                                <img :src="'/data/' + item.media" alt="Poster" class="w-100 model" v-if="item.type == 1 && item.media" />
                                <img :src="'/assets/img/poster-backdrop/' + item.category.backdrop.media" alt="Poster" class="w-100 model" v-else />
                            </div>
                        </b-card>
                    </b-colxx>
                </template>
            </b-row>
        </template>
    </div>
</div>

</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'

export default {
    data() {
        return {
            apiBase: 'wizard/poster/models/',
            items: [],
            categories: []
        }
    },
    metaInfo () {
        return { title: `Poster Hall` }
    },
    mounted() {
        Axios.post(this.apiBase + 'get', {id: this.siteId}).then(res => {
            if (res.data.items && res.data.categories) {
                this.items = res.data.items
                this.categories = res.data.categories
            }
        })
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        }),
    },
    methods: {
        typeImg (media) {
            const extension = media.split('.').pop()
            if (extension != 'pdf' && extension != 'mp4') {
                return true
            } else {
                return false
            }
        },
        typeVideo (media) {
            const extension = media.split('.').pop()
            if (extension == 'mp4') {
                return true
            } else {
                return false
            }
        },
        typePdf (media) {
            const extension = media.split('.').pop()
            if (extension == 'pdf') {
                return true
            } else {
                return false
            }
        },
        isCategory(id) {
            for (var i = 0; i < this.items.length; i++) {
                if (this.items[i].category_id == id) {
                    return true
                }
            }
        },
        goPage(id) {
            this.$router.push('/exhibit/poster/'+id)
        }
    }
}
</script>

<style scoped>
    .model {
        background: url(/data/default-poster.png);
        height: 160px;
        background-size: cover;
    }
</style>