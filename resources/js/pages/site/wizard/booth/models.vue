<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <div class="top-right-button-container">
                    <label for="upload" class="btn btn-outline-primary">Create Booth</label>
                </div>
                <breadcrumb :heading="$t('wizard.booth-model')" />
                <div class="separator mb-5"></div>
            </b-colxx>
        </b-row>
        <b-row v-if="!isUploaded">
            <b-colxx md="6" sm="12" lg="4" v-for="(template, index) in boothModels" :key="template.id">
                <b-card class="mb-4 text-center">
                    <div class="w-100 p-4">
                        <img :src="'/data/' + template.media" alt="Booth" class="w-100 model" />
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <label
                            for="upload"
                            class="btn btn-primary ml-auto mt-2"
                            @click="modalId = template.id"
                        >Change</label>
                        <button 
                            class="btn btn-outline-primary mx-2" 
                            @click="del(index, template.id)"
                            v-b-modal.conform_modal
                            v-if="user && user.role < 3"
                        >Delete</button>
                        <button class="btn btn-primary ml-2 mr-auto" @click="select(template.id)" v-if="boothId">Select this booth</button>
                    </div>
                </b-card>
            </b-colxx> 
        </b-row>
        <b-row v-else>
            <b-colxx xxs="12">
                <b-card no-body class="flex-row mb-4">
                    <div class="w-100">
                        <img v-if="url && extension !== 'mp4'" :src="url" class="edit-size mb-2 w-100"/>
                        
                        <video v-if="url && extension === 'mp4'" class="edit-size" height="auto" autoplay="autoplay" loop="loop" muted="">
                            <source :src="url" type="video/mp4">
                        </video>
                    </div>
                </b-card>
            </b-colxx>
            <button class="btn btn-outline-primary ml-auto mr-2" @click="cancel">Cancel</button>
            <button class="btn btn-primary float-right mr-4" @click="create()">Confirm</button>
        </b-row>

        <input id="upload" type="file" @change="onModalFileChange" onclick="this.value = null" style="display: none" />
        <b-modal id="conform_modal" ref="conform_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="modelConfirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal('conform_modal')">Cancel</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import exhibitMixins from '../exhibitMixins.js'

export default {
    metaInfo () {
        return { title: `Booth Models` }
    },
    mixins: [exhibitMixins],
    data() {
        return {
            type: 0,
            urlText: 'booth',
            modalText: 'Booth'
        }
    },
    mounted() {
        this.modelInit()
    }
}
</script>