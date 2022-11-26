<template>
  <div classs="poll-popup-wrapper">
    <b-modal id="poll-modal" title="Polls" hide-footer>
      <div class="my-4" v-if="!stats">
        <div class="dots" v-if="polls && polls.length > 1">
          <div
            class="dot"
            v-for="(poll, index) in polls"
            :key="index"
            :class="{ active: active == index }"
          ></div>
        </div>
        <div class="polls">
          <div
            class="poll"
            v-for="(poll, index) in polls"
            :key="index"
            :class="{ active: active == index }"
          >
            <div class="title">
              {{ poll.title }}
            </div>
            <div class="answers">
              <div
                class="answer"
                v-for="(answer, indexAnswer) in poll.answers"
                :key="indexAnswer"
              >
                <label>
                  <input
                    type="radio"
                    :value="answer.value"
                    v-model="activeAnswer"
                    :name="`${index}-answer`"
                    @change="onChange(poll)"
                  />
                  {{ answer.value }}
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex" v-if="isAnswered">
          <a
            class="btn btn-next"
            v-if="isAnswered && !isFinished"
            href="#"
            @click.prevent="onClickNext"
            >Next</a
          >
          <a
            class="btn btn-next"
            v-if="isFinished"
            href="#"
            @click.prevent="onClickFinish"
            >Finished</a
          >
        </div>
      </div>
      <div class="my-4" v-if="stats">
        <div class="stats">
          <div class="stat my-2" v-for="(stat, index) in stats" :key="index">
            <div>Question: {{ getPollById(index).title }}</div>
            <div>
              Answers:
              <ul>
                <li
                  v-for="(answer, indexAnswer) in stat.answers"
                  :key="indexAnswer"
                >
                  {{ indexAnswer }} :
                  {{ ((answer / stat.total) * 100).toFixed(2) }}%
                </li>
              </ul>
            </div>
          </div>
        </div>
        <a
          class="btn btn-next"
          href="#"
          @click.prevent="$bvModal.hide('poll-modal')"
          >Close</a
        >
      </div>
    </b-modal>
  </div>
</template>

<script>
import api from "~/pages/site/api";

export default {
  name: "poll-popup",

  data() {
    return {
      polls: [],
      active: 0,
      isAnswered: false,
      activePoll: null,
      activeAnswer: false,
      answeredIds: [],
      stats: null,
    };
  },

  mounted() {
    this.getAvailablePolls().then(() => {
      const notExistIds = this.isAlreadySaw(this.polls.map((item) => item.id));
      if (notExistIds.length) {
        this.polls = this.polls.filter((item) => {
          return notExistIds.includes(item.id);
        });
        this.$bvModal.show("poll-modal");
      }
    });
  },

  computed: {
    isFinished() {
      return this.active == this.polls.length - 1;
    },
  },

  methods: {
    getPollById(id) {
      return this.polls.find((item) => item.id == id);
    },
    isAlreadySaw(ids) {
      let existIds = localStorage.getItem("is-already-saw");
      if (existIds) {
        existIds = JSON.parse(existIds);
        if (existIds && existIds.length) {
          let notExist = [];
          ids.forEach((id) => {
            if (!existIds.includes(id)) {
              notExist = [...notExist, id];
            }
          });
          return notExist;
        }
        return ids;
      }
      return ids;
    },
    onClickFinish() {
      this.answeredIds = [...this.answeredIds, this.activePoll.id];

      this.updateAnswered().then(() => {
        api.getStatByPollIds(this.answeredIds).then(({ data: { data } }) => {
          this.stats = data;
          this.activeAnswer = null;
        });
      });
    },
    getAvailablePolls() {
      return api.getAvailablePolls().then(({ data: { data } }) => {
        this.polls = data;
        return Promise.resolve(true);
      });
    },
    onChange(poll) {
      this.activePoll = poll;
      this.isAnswered = true;
    },
    onClickNext() {
      if (this.active != this.polls.length - 1) {
        this.answeredIds = [...this.answeredIds, this.activePoll.id];
        this.updateAnswered();
        this.active++;
        this.isAnswered = false;
        this.activeAnswer = null;
      }
    },
    updateAnswered() {
      let isAlreadySaw = localStorage.getItem("is-already-saw");
      if (!isAlreadySaw) {
        isAlreadySaw = "[]";
      }
      isAlreadySaw = JSON.parse(isAlreadySaw);
      isAlreadySaw = [...isAlreadySaw, this.activePoll.id];
      isAlreadySaw = isAlreadySaw.filter((value, index, self) => {
        return self.indexOf(value) === index;
      });

      localStorage.setItem("is-already-saw", JSON.stringify(isAlreadySaw));
      return api
        .answersToAnswers(this.activePoll.id, this.activeAnswer)
        .then(() => {
          return Promise.resolve(true);
        });
    },
  },
};
</script>

<style lang="scss">
.dots {
  display: flex;
  justify-content: center;
  margin-top: 15px;
  margin-bottom: 15px;

  .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #777;
    margin-left: 10px;
    transition: all 0.3s;

    &.active {
      background-color: #922c88;
    }
  }
}

.polls {
  position: relative;
  min-height: 150px;
  z-index: 1;

  .poll {
    position: absolute;
    z-index: 2;
    left: 0;
    top: 0;
    background: #fff;
    width: 100%;

    &.active {
      z-index: 3;
    }

    .title {
      margin-bottom: 15px;
    }
  }
}

.btn-next {
  background-color: #922c88;
  color: #fff;
  margin-left: auto;
}
</style>

<style lang="scss" scoped>
.poll-popup-wrapper {
}
</style>