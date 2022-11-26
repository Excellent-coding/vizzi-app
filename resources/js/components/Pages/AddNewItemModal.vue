<template>
  <b-modal
    id="add_modal"
    ref="add_modal"
    :title="title"
    modal-class="modal-right"
  >
    <b-form>
      <b-form-group label="Title">
        <b-form-input v-model="newItem.title" />
      </b-form-group>
      <b-form-group label="File">
        <b-input-group :prepend="$t('input-groups.upload')">
            <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onFileUpload" />
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
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

export default {
  components: {
    "v-select": vSelect
  },
  props: [
    'addNewItem',
    'editData'
  ],
  data() {
    return {
      newItem: {
        id: 0,
        title: "",
        file: null
      },
      title: 'Add New Item',
    };
  },
  methods: {
    hideModal(refname) {
      this.$refs[refname].hide();
    },
    onAssetFileUpload(e) {
        this.newItem.file = e.target.files[0] 
    }
  },
  watch: {
    editData: function(val) {
      if (val.id) {
        this.newItem = {
          id: val.id,
          title: val.title,
          file: null
        }
        this.title = "Edit Item"
      } else {
        this.title = "Add New Item"
        this.newItem = {
          id: 0,
          title: '',
          file: null
        }
      }
    }
  }
};
</script>

