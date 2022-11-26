<template>
    <div class="container">
        <breadcrumb heading="Booth Setting" />
        <div class="separator mb-5"></div>
        <b-card>
            <b-row>
                <b-colxx md="6" sm="12">
                    <h5>Welcome Text</h5>
                    <b-form-group label="Title" class="has-float-label mt-4">
                        <b-form-input v-model="settingText.title" />
                    </b-form-group>
                    <b-form-group label="Content" class="has-float-label">
                        <b-textarea v-model="settingText.content" style="height: 160px"/>
                    </b-form-group>
                    <button class="btn btn-primary" @click="settingSubmit">Submit</button> 
                </b-colxx>
                <b-colxx md="6" sm="12">
                    <h5 class="mt-2">Reminder</h5>
                    <b-form-textarea
                        v-model="settingNote"
                        class="w-100 mt-4"
                        style="height: 150px"
                    />
                    <button class="btn btn-primary mt-2" @click="submitNote">Submit</button>
                </b-colxx>
            </b-row>
        </b-card>
    </div>
</template>

<script>
import exhibitMixins from '../exhibitMixins.js'

export default {
    mixins: [exhibitMixins],
    data() {
        return {
            type: 0
        }
    },
    mounted() {
        this.settingInit()
    },
}
</script>