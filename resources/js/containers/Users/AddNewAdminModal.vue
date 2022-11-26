<template>
  <b-modal
    id="userModal"
    ref="userModal"
    :title="$t('users.add-new-user')"
    modal-class="modal-center"
  >
    <b-form>
      <b-form-group :label="$t('name')">
        <b-form-input v-model="newItem.name" />
      </b-form-group>
      <b-form-group :label="$t('email')">
        <b-form-input v-model="newItem.email" />
      </b-form-group>
      <b-form-group :label="$t('verify-code')">
        <b-input-group>
          <b-input-group-prepend @click="generate()">
            <b-input-group-text>Generate</b-input-group-text>
          </b-input-group-prepend>
          <b-form-input v-model="newItem.code" />
        </b-input-group>
      </b-form-group>
    </b-form>
    <template slot="modal-footer">
      <b-button variant="outline-secondary" @click="hideModal('userModal')">{{ $t('cancel') }}</b-button>
      <b-button variant="primary" @click="addUser(newItem)" class="mr-1">{{ $t('submit') }}</b-button>
    </template>
  </b-modal>
</template>
<script>
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"

const TodoAddNewModal = {
  components: {
    "v-select": vSelect
  },
  props: ["statuses", "addUser", "newItem"],
  data() {
    return {
      roles: [
        { value: 3, label: "Manager" },
        { value: 4, label: "Client" }
      ]
    }
  },
  methods: {
    hideModal(refname) {
      this.$refs[refname].hide();
    },
    generate() {
      this.newItem.code = Math.round(Math.random() * 1000000)
    }
  }
};
export default TodoAddNewModal;
</script>

<style lang="css">
  .input-group-text {
    cursor: pointer;
    background: #f4f6f8;
  }
  .input-group-text:hover {
    background: greenyellow;
  }
</style>
