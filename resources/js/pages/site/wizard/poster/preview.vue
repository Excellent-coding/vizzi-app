<template>
    <div v-if="isShow">
        <div class="py-5 bg position-relative text-center" :style="getBackdrop(viewData)">
            <router-link tag="div" :to="'/app/wizard/poster/edit/' + id" class="backHall position-absolute bg-white p-2">
                Return
            </router-link>
            <template v-if="viewData.type < 3">
                <div class="view-header mx-auto mb-4 text-capitalize">{{viewData.title.substring(0, 120)}}</div>
                <div v-if="viewData.type == 1">
                    <div v-if="viewData.media" class="text-center single-view mx-auto position-relative"  v-b-modal.poster_view_modal @click="view = viewData.media">
                        <img :src="'/data/' + viewData.media" class="w-100 h-100 object-fit" v-if="typeImg(viewData.media)" />
                        <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="typeVideo(viewData.media)">
                            <source class="w-100 h-100 object-fit" :src="/data/ + viewData.media" type="video/mp4">
                        </video>
                        <iframe class="poster-item w-100 h-100 object-fit" :src="'/data/'+viewData.media" v-else />
                    </div>
                </div>
                <div v-else-if="viewData.type == 2" class="text-center">
                    <div v-if="viewData.media && JSON.parse(viewData.media) && JSON.parse(viewData.media).length" class="multiple-view d-flex justify-content-end align-items-center">
                        <div v-for="(item, index) in JSON.parse(viewData.media)" :key="index" class="item mx-auto bg-white position-relative" v-b-modal.poster_view_modal @click="view = item">
                            <img :src="'/data/' + item" class="w-100 h-100 object-fit" v-if="typeImg(item)"  />
                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="typeVideo(item)">
                                <source class="w-100 h-100 object-fit" :src="/data/ + item" type="video/mp4" />
                            </video>
                            <div class="position-relative w-100 h-100" v-else-if="typePdf(item)" >
                                <div class="position-absolute w-100 view-pdf" />
                                <iframe class="w-100 h-100" :src="'/data/' + item" />
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <div v-else style=" height: calc(24vw + 9rem)">
                <div class="position-absolute" :style="getPosterHeaderStyle(posterHeader)" style="text-transform: capitalize">{{viewData.title.substring(0, 120)}}</div>
                <template v-if="viewData.layers">
                    <div class="position-absolute position-relative" v-for="(content, index) in JSON.parse(viewData.layers)" :key="index" :style="contentStyle(content)">
                        <div :style="contentHeaderStyle(content.header)">{{content.title.substring(0, 120)}}</div>
                        <div :style="contentBodyStyle(content.body)" v-if="content.type == 'text'">{{content.text}}</div>
                        <div :style="contentMediaStyle(content.media)" v-else>
                            <img :src="'/data/' + content.media.file" class="w-100 h-100 object-fit" v-if="fileType(content.media.file) !== 'mp4' && fileType(content.media.file) !== 'pdf'" @click="viewPosterItem(content.media.file)" />
                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(content.media.file) == 'mp4'">
                                <source class="w-100 h-100 object-fit" :src="/data/ + content.media.file" type="video/mp4" @click="viewPosterItem(content.media.file)">
                            </video>
                            <div class="position-relative w-100 h-100" v-else >
                                <div class="position-absolute w-100 view-pdf" @click="viewPosterItem(content.media.file)"/>
                                <iframe class="w-100 h-100" :src="'/data/' + content.media.file" />
                            </div>   
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="container" v-if="viewData.header && viewData.header.length">
            <b-row class="mx-0">
                <b-col class="exhibit-header text-center" :style="getHeaderStyle()">
                    <div class="d-inline-flex">
                        <div
                            class="header-btn"
                            v-for="(item, index) in viewData.header"
                            :key="index"
                            :style="getExhibitButtonStyle()"
                            @click="exhibitHtml = item"
                        >
                        {{item.title}}
                        </div>
                    </div>
                </b-col>
            </b-row>
            <div style="min-height: 200px" class="mt-2">
                <b-tabs pills card>
                    <b-tab v-for="tab in exhibitHtml.tab" :key="tab.id" :title="capitalizeFirstLetter(tab.title)">
                        <h6 class="py-2" v-html="tab.content"></h6>
                        <div v-for="media in tab.media" :key="media.id">
                            <div class="d-flex justify-content-end align-items-center mb-2">
                                <div class="position-relative mr-3">
                                    <div class="position-absolute front-type-text" :style="getStyle(media.item)">{{getTypeText(media.item)}}</div>
                                    <img :src="getType(media.item)" style="width: 48px" />
                                </div>
                                <h6 class="mr-auto my-auto text-capitalize">{{media.title}}</h6>
                                <b-button variant="outline-primary" size="sm" v-b-modal.poster_view_modal @click="view = media.item">View</b-button>
                                <b-button variant="primary" size="sm" @click="addCartItem(media)" class="ml-2">
                                    {{getMark(media) ? '-' : '+'}} {{$ct('cart')}}
                                </b-button>
                            </div>
                            <hr>
                        </div>
                    </b-tab>
                </b-tabs>
            </div>
        </div>
        <b-modal
            id="poster_view_modal"
            ref="poster_view_modal"
            :size="'size-' + modalWidth"
            centered
            hide-footer
        >
            <template v-slot:modal-header="{ close }">
                <div class="text-center w-100">
                    <h2 class="view-title">{{viewData.title}}</h2>
                </div>
                <i class="simple-icon-close poster-close-btn" @click="close"></i>
            </template>
            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-if="typeVideo(view)">
                <source class="w-100" :src="/data/ + view" type="video/mp4">
            </video>
            <iframe class="w-100" style="height: 60vh" :src="'/data/'+view" v-else-if="typePdf(view)" />
            <iframe class="w-100" style="height: 60vh" :src="view" v-else-if="view.substring(0, 4) == 'http'" />
            <img :src="'/data/' + view" class="w-100" v-else />
        </b-modal>
    </div>
</template>

<script>
import posterMixins from './posterMixins'
import mixins from '../../../../mixins/mixins'
import Axios from 'axios'
export default {
    data() {
        return {
            isShow: false,
            id: null, 
            type: 2
        }
    },
    mixins: [posterMixins, mixins],
    mounted() {
        this.id = this.$route.params.id
        Axios.post('/get/poster', {id: this.id, siteId: this.siteId, type: this.type}).then(res => {
            this.viewData = res.data
            if (this.viewData.header && this.viewData.header.length) {
                this.exhibitHtml = this.viewData.header[0]
            }
            this.isShow = true
        })
    }
}
</script>

<style >    
    .poster-close-btn {
        border-radius: 50%;
        font-size: 32px;
        background: #212121;
        color: white;
        box-shadow: 0px 0px 1px 1px black;
        position: absolute;
        right: -16px;
        top: -16px;
        cursor: pointer;
    }
    .modal-header {
        background: white;
    }
    .modal-body {
        max-height: 80vh;
        width: 100%;
    }
    .modal-dialog.modal-size-20 {
        max-width: 20%!important;
    }
    .modal-dialog.modal-size-40 {
        max-width: 40%!important;
    }
    .modal-dialog.modal-size-60 {
        max-width: 60%!important;
    }
    .modal-dialog.modal-size-80 {
        max-width: 80%!important;
    }
    .modal-dialog.modal-size-100 {
        max-width: 100%!important;
    }
</style>