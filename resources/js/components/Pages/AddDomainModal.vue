<template>
    <b-modal
        id="add_modal"
        ref="add_modal"
        title="Create Event"
        modal-class="modal-right"
    >
        <b-form>
            <b-form-group label="Event Title">
                <b-form-input v-model="newItem.title" />
            </b-form-group>
            <b-form-group :label="$t('menu.domain-name')">
                <b-input-group>
                    <b-input-group-prepend><b-input-group-text>https://</b-input-group-text></b-input-group-prepend>
                    <b-form-input v-model="newItem.domain" />
                    <b-input-group-append><b-input-group-text>.{{host}}</b-input-group-text></b-input-group-append>
                </b-input-group>
            </b-form-group>
        </b-form>

        <template slot="modal-footer">
            <b-button
                variant="outline-secondary"
                @click="hideModal('add_modal')"
            >{{ $t('cancel') }}</b-button>
            <b-button variant="primary" @click="addNewItem(newItem)" class="mr-1">{{ $t('submit') }}</b-button>
        </template>
    </b-modal>
</template>

<script>
export default {
    props: [
        'addNewItem'
    ],
    data() {
        return {
            newItem: {
                title: "",
                domain: ""
            }
        };
    },
    computed: {
        host() {
            return window.location.host;
        }
    },
    methods: {
        hideModal(refname) {
            this.$refs[refname].hide();
        }
    }
};
</script>



