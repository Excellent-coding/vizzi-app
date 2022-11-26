<template>
  <div class="answers-wrapper">
    <div class="answers">
      <div class="answer" v-for="(answer, index) in answers" :key="index">
        <div
          class="info"
          :style="{ width: answers.length > 1 ? '90%' : '100%' }"
        >
          <b-form-group>
            <b-form-input
              v-model="answer.value"
              :placeholder="`Answer #${index + 1}`"
              @input="onInput"
            />
          </b-form-group>
        </div>
        <div class="action ml-2" v-if="answers.length > 1">
          <b-button
            style="border-radius: 6px"
            variant="danger"
            @click.prevent="onRemove(index)"
          >
            <i class="simple-icon-trash my-auto"></i>
          </b-button>
        </div>
      </div>
    </div>
    <div>
      <b-button @click="onAddAnswer" style="border-radius: 6px"
        ><i class="simple-icon-plus"></i> Add answer</b-button
      >
    </div>
  </div>
</template>

<script>
export default {
  name: "poll-answers",

  props: {
    value: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      answers: [
        {
          value: "",
        },
      ],
    };
  },

  watch: {
    value() {
      if (this.value) {
        this.answers = this.value;
      }
    },
  },

  methods: {
    onRemove(indexRemove) {
      this.answers = this.answers.filter((item, index) => index != indexRemove);
      this.$emit("input", this.answers);
    },
    onAddAnswer() {
      this.answers = [
        ...this.answers,
        {
          value: "",
        },
      ];
      this.$emit("input", this.answers);
    },
    onInput() {
      this.$emit("input", this.answers);
    },
  },
};
</script>

<style lang="scss" scoped>
.answers-wrapper {
  .answers {
    display: flex;
    flex-direction: column;

    .answer {
      display: flex;
      flex-direction: row;
      width: 100%;

      .info {
        width: 90%;
      }

      .action {
        width: 10%;
      }
    }
  }
}
</style>