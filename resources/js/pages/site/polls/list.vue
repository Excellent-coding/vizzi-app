<template>
  <div>
    <b-row>
      <b-colxx xxs="12">
        <breadcrumb heading="Polls" />
        <div class="top-right-button-container d-flex">
          <label
            for="file"
            class="btn btn-primary mb-0"
            @click="$router.push({ name: 'polls-create' })"
            >Create poll</label
          >
        </div>
        <div class="separator mb-5"></div>
      </b-colxx>
    </b-row>
    <b-card class="content">
      <div class="table-responsive">
        <b-table :fields="pollFields" :items="polls" hover>
          <template v-slot:cell(#)="data">{{ data.index + 1 }}</template>
          <template v-slot:cell(title)="data">
            <div style="width: 80px">
              {{ data.item.title }}
            </div>
          </template>
          <template v-slot:cell(expire)="data">
            <div style="width: 80px">
              {{ data.item.expire }}
            </div>
          </template>
          <template v-slot:cell(status)="data">
            <b-badge
              class="mt-2"
              pill
              :variant="'primary'"
              v-if="data.item.active"
              >Active</b-badge
            >
            <b-badge
              class="mt-2"
              pill
              :variant="'danger'"
              v-if="!data.item.active"
              >InActive</b-badge
            >
          </template>
          <template v-slot:cell(action)="data">
            <div class="text-right">
              <b-dropdown variant="primary" right text="Action">
                <b-dropdown-item @click="onEdit(data.item.id)"
                  >Edit</b-dropdown-item
                >
                <b-dropdown-item @click="setStatus(data.item.id, true)"
                  >Active</b-dropdown-item
                >
                <b-dropdown-item @click="setStatus(data.item.id, false)"
                  >InActive</b-dropdown-item
                >
                <b-dropdown-item @click="onDelete(data.item.id)"
                  >Delete</b-dropdown-item
                >
              </b-dropdown>
            </div>
          </template>
        </b-table>
      </div>
    </b-card>
  </div>
</template>

<script>
import api from "../api";

export default {
  name: "polls-list",

  data() {
    return {
      pollFields: [
        { key: "#", sortable: false },
        { key: "title", sortable: true },
        { key: "expire", sortable: true },
        { key: "status", sortable: true },
        { key: "action", sortable: false, class: "text-right action-col" },
      ],
      polls: [],
    };
  },

  mounted() {
    this.getAllPolls();
  },

  methods: {
    getAllPolls() {
      api.getAllPolls().then(({ data: { data } }) => {
        this.polls = data;
      });
    },
    onDelete(poll_id) {
      api.deletePoll(poll_id).then(() => {
        this.getAllPolls();
      });
    },
    setStatus(poll_id, status) {
      api.updatePoll(poll_id, { active: status }).then(() => {
        this.getAllPolls();
      });
    },
    onEdit(poll_id) {
      this.$router.push({ name: "polls-edit", params: { id: poll_id } });
    },
  },
};
</script>

<style lang="scss" scoped>
.content {
  min-height: 200px;

  .action-col {
    width: 80px;
  }
}
</style>
