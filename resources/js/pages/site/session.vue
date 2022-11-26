<template>
  <div :style="getStyle()" class="d-flex" v-if="isMounted">
    <v-screen
        :color="color"
        :session="session"
        :presenterData="presenterData"
        class="vw-75"
    />
    <div class="bg-white vw-25">
        <v-chat :id="id" />
        <hr style="margin-top: 0px; margin-bottom: -2px"/>
    <!-- <v-note /> -->
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

import SessionScreen from "../../containers/session/SessionScreen";
import SessionChat from "../../containers/session/SessionChat";
import SessionNote from "../../containers/session/SessionNote";
import Axios from "axios";

export default {
  components: {
    "v-screen": SessionScreen,
    "v-chat": SessionChat,
    "v-note": SessionNote
  },
  metaInfo() {
    return { title: `Session` };
  },
  data() {
    return {
      id: null,
      height: 32,
      session: null,
      video_sources: [],
      isMounted: false,
      color: "#922c88",
      presenterData: []
    };
  },
  computed: {
    ...mapGetters({
      siteId: "site/getSiteId",
      currentUser: "auth/user",
      style: "site/getStyle"
    })
  },
  mounted() {
    if (this.style) {
        this.color = JSON.parse(this.style).style.bg
        this.height = JSON.parse(this.style).style.height
    }
    window.setInterval(this.init, 3000)
  },
  methods: {
    init() {
        var id = this.id
        this.id = this.$route.params.id
        if (id != this.id) {
            Axios.post("/site/session", { id: this.id }).then(res => {
                console.log(res.data)
                if (res.status == 200) {
                    this.session = res.data.session
                    this.presenterData = res.data.presenterData
                    this.isMounted = true;
                }
            })
        }
    },
    getStyle() {
      return {
        width: "100%"
      };
    }
  }
};
</script>

<style lang="css">
.h-60 {
  height: 60%;
}
.h-40 {
  height: 40%;
}
.vw-25 {
  width: 25vw;
}
.vw-75 {
  width: 75vw;
}
</style>