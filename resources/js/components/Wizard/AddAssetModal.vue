<template>
    <b-modal
        id="add_modal"
        ref="add_modal"
        :title="titleText"
        modal-class="modal-center"
    >
        <b-form>
            <b-form-group label="Title">
                <b-form-input v-model="menuTitle" />
            </b-form-group>
        </b-form>
        <template slot="modal-footer">
            <b-button
                variant="outline-secondary"
                @click="hideModal('add_modal')"
            >{{ $t('cancel') }}</b-button>
            <b-button
                variant="primary"
                @click="addNewItem(menuTitle, editId)"
                class="mr-1">
                {{submitText}}
            </b-button>
        </template>
    </b-modal>
</template>

<script>
export default {
    props: [
        'addNewItem',
        'editData'
    ],
    data() {
        return {
            menuTitle: '',
            editId: 0,
            submitText: 'Create',
            titleText: 'Create Menu'
        }
    },
    methods: {
        hideModal(refname) {
            this.$refs[refname].hide();
        }
    },
    watch: {
        editData: function(val) {
            if (val.id) {
                this.titleText = "Edit The Title"
                this.menuTitle = val.title
                this.submitText = 'Change'
                this.editId = val.id
            } else {
                this.titleText = "Create Menu"
                this.menuTitle = ''
                this.submitText = 'Create'
                this.editId = 0
            }
        }
    }
}
</script>