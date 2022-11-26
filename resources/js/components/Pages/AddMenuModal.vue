<template>
    <b-modal
        id="add_modal"
        ref="add_modal"
        :title="title"
        modal-class="modal-center"
    >
        <b-form>
            <b-form-group :label="'Title'">
                <b-form-input v-model="newItem.title" @keyup="autoUrl()" />
            </b-form-group>
            <b-form-group :label="'Type'">
                <v-select v-model="type" :on-change="subListSet(type)" :options="types" />
            </b-form-group>
            <label v-if="newItem.type != 'modal'">Url</label>
            <b-input-group v-if="newItem.type != 'modal'">
                <b-input-group-append>
                    <b-input-group-text>
                        {{host}}
                    </b-input-group-text>
                    <b-form-input v-model="newItem.url"/>
                </b-input-group-append>
            </b-input-group>
        </b-form>

        <template slot="modal-footer">
            <b-button
                variant="outline-secondary"
                @click="hideModal('add_modal')"
            >{{ $t('cancel') }}</b-button>
            <b-button variant="primary" @click="addNewItem(newItem.title, newItem.type, newItem.url, editId)" class="mr-1">{{submit}}</b-button>
        </template>
    </b-modal>
</template>

<script>
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"

export default {
    components: {
        "v-select": vSelect
    },
    props: [
        'addNewItem',
        'editData',
        'host',
        'submit'
    ],
    data() {
        return {
            type: '',
            newItem: {
                title: '',
                type: '',
                url: ''
            },
            types: [
                {label: "Page", value: "content"},
                {label: "Modal", value: "modal"},
                {label: "Exhibit Hall", value: "exhibit"}
            ],
            title: 'Add New Page',
            editId: 0
        };
    },
    methods: {
        hideModal(refname) {
            this.$refs[refname].hide();
        },
        subListSet(value) {
            this.newItem.type = value.value
        },
        autoUrl() {
            let str = this.newItem.title.replace(/^\s+|\s+$/g, '')
            str = str.toLowerCase()
            const from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            const to   = "aaaaeeeeiiiioooouuuunc------";
            for (let i=0, l=from.length ; i<l ; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }
            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            this.newItem.url = str
        }
    },
    watch: {
        editData:function(val) {
            if (val.id) {
                this.title = "Edit page"
                this.newItem = {
                    title: val.title,
                    type: val.type,
                    url: val.url.substring(1)
                }
                if (val.type == 'content') {
                    this.type = {label: "Page", value: "content"}
                } else if (val.type == 'modal') {
                    this.type = {label: "Modal", value: "modal"}
                } else {
                    this.type = {label: "Exhibit Hall", value: "exhibit"}
                }
                this.editId = val.id
            } else {
                this.title = "Add New Page"
                this.editId = 0
            }
        }
    }
};
</script>

<style lang="css">
    .input-group-text {
        border-right: 0;
        background: #f4f6f8;
        padding: 4px;
        font-size: 14px;
    }
</style>

