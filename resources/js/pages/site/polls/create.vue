<template>
  <b-card>
    <div class="container mb-2 mx-auto">
      <h4>Add Poll</h4>
      <b-row>
        <b-colxx md="6" sm="12">
          <b-form-group label="Poll Title*" class="my-2">
            <b-form-textarea
              v-model="item.title"
              placeholder="Title"
              no-resize
              rows="4"
            />
          </b-form-group>
        </b-colxx>
        <b-colxx md="6" sm="12">
          <b-form-group label="Poll answers*" class="my-2">
            <poll-answers v-model="item.answers" />
          </b-form-group>
        </b-colxx>
        <b-colxx md="6" sm="12">
          <b-form-group label="Poll expire date*" class="my-2">
            <v-date-picker
              v-model="item.expire"
              :input-props="{
                placeholder: 'Select Date',
              }"
            />
          </b-form-group>
        </b-colxx>
      </b-row>
    </div>
    <div class="d-flex">
      <b-button
        variant="outline-primary"
        style="border-radius: 6px"
        class="ml-auto"
        @click="onCancel"
        >Cancel</b-button
      >
      <b-button
        variant="primary"
        class="ml-2"
        style="border-radius: 6px"
        @click="onAddPoll"
        >{{ !isEdit ? "Add" : "Update" }} Poll</b-button
      >
    </div>
  </b-card>
</template>

<script>
import PollAnswers from "~/components/Polls/Answers";
import api from "../api";
import moment from "moment";

export default {
  name: "create",

  components: {
    PollAnswers,
  },

  data() {
    return {
      item: {
        title: "",
        expire: "",
        answers: [],
      },
      isEdit: false,
    };
  },

  mounted() {
    if (this.$route.name == "polls-edit") {
      this.isEdit = true;
      api.getPoll(this.$route.params.id).then(({ data: { data } }) => {
        this.item = {
          title: data.title,
          answers: data.answers,
          expire: moment(data.expire).toDate(),
        };
      });
    }
  },

  methods: {
    onAddPoll() {
      if (
        this.item.title == "" ||
        this.item.expire == "" ||
        !this.item.answers.length
      ) {
        this.$notify(
          "primary filled",
          "",
          "Please insert Required(*) fields!",
          { duration: 3000, permanent: false }
        );
        return;
      }

      let data = {
        title: this.item.title,
        answers: this.item.answers,
        expire: moment(this.item.expire).add(1, "day").toDate(),
      };

      if (!this.isEdit) {
        api.createPoll(data).then(({ data: { data } }) => {
          this.onCancel();
        });
        return;
      }

      api.updatePoll(this.$route.params.id, data).then(({ data: { data } }) => {
        this.onCancel();
      });
    },
    onCancel() {
      this.$router.push({ name: "polls-list" });
    },
  },
};
</script>