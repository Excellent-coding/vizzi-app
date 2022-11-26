<template>
  <div class="landing-page">
    <div class="main-container" @scroll="handleScroll">
      <topnav :status="status" :authStatus="authenticated" />
      <child />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

import TopNav from '../containers/Landing/TopNav'
import MobileMenu from '../containers/Landing/MobileMenu'

export default {
  name: 'BasicLayout',
  components: {
    'topnav': TopNav,
    'mobile-menu': MobileMenu
  },
  data() {
    return {
      scrollPosition: 0,
      status: 0
    }
  },
  created () {
    window.addEventListener('scroll', this.handleScroll);
  },
  destroyed () {
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    handleScroll: function(e) {
      const currentPosition = window.top.scrollY;
      if (currentPosition !=0 ) {
        if (this.scrollPosition < currentPosition) {
          this.status = 1;
        } else {
          this.status = 2;
        }
      } else {
        this.status = 0;
      }
      this.scrollPosition = currentPosition;
    }
  },
  computed: mapGetters({
    authenticated: 'auth/check'
  })
}
</script>

