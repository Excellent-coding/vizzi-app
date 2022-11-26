<template>
<div>
    <div class="py-5" style="background: url('/data/default-poster.png'); height: calc(24vw + 9rem)">
        <div class="view-header mx-auto mb-4 text-center">{{title}}</div>
        <div v-if="type == 1">
            <div v-if="items.length" class="text-center">
                <div class="single-view mx-auto position-relative">
                    <template v-if="!changeUrl">
                        <img :src="'/data/' + items[0]" class="w-100 h-100 object-fit" v-if="fileType(items[0]) !== 'mp4' && fileType(items[0]) !== 'pdf'" />
                        <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(items[0]) == 'mp4'">
                            <source class="w-100 h-100 object-fit" :src="/data/ + items[0]" type="video/mp4">
                        </video>
                        <iframe class="poster-item w-100 h-100 object-fit" :src="'/data/'+items[0]" v-else-if="fileType(items[0]) == 'pdf'" />
                        <div class="position-center position-absolute">
                            <button class="btn btn-primary mx-1" @click="upload">Confirm</button>
                            <label for="media" class="btn btn-outline-primary mx-1 mt-2">Change</label>
                        </div>
                    </template>
                    <template v-else>
                        <img :src="changeUrl" class="w-100 h-100 object-fit" v-if="extension !== 'mp4' && extension !== 'pdf'" />
                        <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="extension === 'mp4'">
                            <source class="w-100 h-100 object-fit" :src="changeUrl" type="video/mp4">
                        </video>
                        <iframe class="w-100 h-100 object-fit" :src="changeUrl" v-else-if="extension === 'pdf'" />
                        <div class="position-center position-absolute">
                            <button class="btn btn-primary mx-1" @click="upload">Confirm</button>
                            <label for="media" class="btn btn-outline-primary mx-1 mt-2">Change</label>
                        </div>
                    </template>
                </div>
            </div>
            <div v-else class="single-view mx-auto position-relative">
                <label for="media" class="btn btn-outline-primary position-center position-absolute" v-if="!url">Upload</label>
                <template v-else>
                    <img :src="url" class="w-100 h-100 object-fit" v-if="extension !== 'mp4' && extension !== 'pdf'" />
                    <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="extension === 'mp4'">
                        <source class="w-100 h-100 object-fit" :src="url" type="video/mp4">
                    </video>
                    <div class="w-100 h-100" v-else-if="extension === 'pdf'" >
                        <iframe class="h-100 w-100 object-fit" :src="url" />
                    </div>
                    <div class="position-center position-absolute">
                        <label for="media" class="btn btn-primary mx-1 mt-2">Change</label>
                    </div>
                </template>
            </div>
        </div>

        <input id="media" type="file" @change="onFileChange" class="d-none" />
    </div>
    <button class="btn btn-primary mt-2">Submit</button>
</div>
</template>

<script>
export default {
    props: ['type', 'title', 'items'],
    data() {
        return {
            url: null,
            extension: '',
            formData: new FormData()
        }
    },
    methods: {
        onFileChange(e) {
            const file = e.target.files[0]
            this.extension = file.name.split('.').pop()
            if (this.extension != 'mp4') {
                if (file.size / 10000 > 5) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
                }
            } else {
                if (file.size / 100000 > 1) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
                }
            }
            if (this.type == 1) {
                if (this.items.length) {
                    this.changeUrl = URL.createObjectURL(file)
                } else {
                    this.url = URL.createObjectURL(file)
                }
                this.formData.set('file', file)
            } else {
                if (this.currentIndex == 1) {
                    this.extension_1 = this.extension
                    if (this.items.length) {
                        this.changeUrl_1 = URL.createObjectURL(file)
                    } else {
                        this.url_1 = URL.createObjectURL(file)
                    }
                    this.formData.set('file1', file)
                } else if (this.currentIndex == 2) {
                    this.extension_2 = this.extension
                    if (this.items.length) {
                        this.changeUrl_2 = URL.createObjectURL(file)
                    } else {
                        this.url_2 = URL.createObjectURL(file)
                    }
                    this.formData.set('file2', file)
                } else {
                    this.extension_3 = this.extension
                    if (this.items.length) {
                        this.changeUrl_3 = URL.createObjectURL(file)
                    } else {
                        this.url_3 = URL.createObjectURL(file)
                    }
                    this.formData.set('file3', file)
                }
            }
        },

        fileType(media) {
            return extension = media.split('.').pop()
        },
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
        }
    }
}
</script>

<style scoped>

</style>