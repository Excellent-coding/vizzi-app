<template>
<div>
    <b-row>
        <b-colxx xxs="12">
            <breadcrumb :heading="$t('wizard.sponsor')" />
            <div class="separator mb-5"></div>
        </b-colxx>
    </b-row>
    <b-row>
        <b-colxx xxs="12">
            <input id="upload" type="file" @change="onFileChange" style="display: none" />
            <label for="upload" class="btn btn-outline-primary">Create Sponsor</label>
            <b-card no-body class="flex-row mb-4">
                <div class="w-100">
                    <img v-if="uploadUrl && uploadExtension !== 'mp4'" :src="uploadUrl" class="edit-size mb-2 w-100"/>
                    
                    <video v-if="uploadUrl && uploadExtension === 'mp4'" class="edit-size" height="auto" autoplay="autoplay" loop="loop" muted="">
                        <source :src="uploadUrl" type="video/mp4">
                    </video>
                </div>
            </b-card>
            <button class="btn btn-primary float-right" @click="uploadConfirm()" v-if="uploadStatus">Confirm</button>
        </b-colxx>
    </b-row>
</div>
</template>

<script>
import exhibitMixins from '../exhibitMixins.js'

export default {
    middleware: 'should',
    metaInfo () {
        return { title: `Booth Upload` }
    },
    data() {
        return {
            type: 1,
            urlText: 'sponsor',
            modalText: 'Sponsor'
        }
    },
    mixins: [exhibitMixins],
}
</script>