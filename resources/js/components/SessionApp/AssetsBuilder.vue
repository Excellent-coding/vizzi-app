<template>
    <div>
        <b-button 
            variant="primary" 
            style="border-radius: 6px"
            class="my-2"
            @click="status = 0"
            v-if="status == 1"
        ><i class="simple-icon-plus btn-group-icon"/> Add Asset
        </b-button>
        <template v-else-if="status == 0">
            <b-form-group class="mb-0">
                <b-form-radio-group v-model="option">
                    <b-form-radio :value="0">Upload</b-form-radio>
                    <b-form-radio :value="1">Link</b-form-radio>
                </b-form-radio-group>
                <b-input-group :prepend="$t('input-groups.upload')" class="my-2" v-if="option == 0">
                    <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onFileUpload" />
                </b-input-group>
                <b-form-input placeholder="Insert Url" v-model="link" class="my-2" v-else />
            </b-form-group>
            <b-button
                variant="primary"
                style="border-radius: 6px"
                class="float-right"
                @click="upload"
            >Upload</b-button>
        </template>
    </div>
</template>

<script>
import Axios from 'axios'
export default {
    props: ['id'],
    data() {
        return {
            apiBase: '/agenda/session/',
            option: 0,
            status: 1,
            formData: null,
            link: '',
            isUploaded: false
        }
    },
    methods: {
        upload() {
            if (this.option) {

            } else {
                if (this.isUploaded) {

                } else {
                    this.$notify('primary filled', '', 'File is not uploaded!', { duration: 3000, permanent: false })
                }
            }
        },
        order() {

        },
        onFileUpload(e) {
            const file = e.target.files[0]
            if (file.size / 10000 > 5) {
                this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
            }
            this.formData.append('file', file)
            this.isUploaded = true
        },
        validURL(str) {
            var pattern = new RegExp('^(https?:\\/\\/)?'+ 
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ 
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ 
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ 
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ 
                '(\\#[-a-z\\d_]*)?$','i'); 
            return !!pattern.test(str);
        },
    }
}
</script>