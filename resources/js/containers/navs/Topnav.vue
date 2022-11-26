<template>
  <nav class="navbar fixed-top">
    <div class="d-flex align-items-center navbar-left">
      <a
        href="#"
        class="menu-button d-none d-md-block"
        @click.prevent.stop="changeSideMenuStatus({step :menuClickCount+1, classNames:menuType, selectedMenuHasSubItems})"
      >
        <menu-icon />
      </a>
      <a
        href="#"
        class="menu-button-mobile d-xs-block d-sm-block d-md-none"
        @click.prevent.stop="changeSideMenuForMobile(menuType)"
      >
        <mobile-menu-icon />
      </a>
      <div class="ml-2 d-flex ">
        <img src="/assets/img/vizzi-logo.png" style="width: 35px" />
        <h6 class="my-auto ml-2 d-sm-block d-none">Vizzi</h6>
      </div>
    </div>
    <router-link class="navbar-logo" tag="a" to="/app">
      <div class="d-flex">
        <img :src="'/data/'+logo" class="site-logo mx-sm-0 mx-auto" v-if="!isDark && logo" />
        <img :src="'/data/'+darkLogo" class="site-logo mx-sm-0 mx-auto" v-if="isDark && darkLogo" />
        <div class="ml-2 site-title mt-1 d-sm-block d-none">{{title}}</div>
      </div>
    </router-link>

    <div class="navbar-right">
      <div class="d-none d-md-inline-block align-middle mr-3">
        <switches
          id="tool-mode-switch"
          v-model="isDarkActive"
          theme="custom"
          class="vue-switcher-small"
          color="primary"
        />
        <b-tooltip target="tool-mode-switch" placement="left" title="Dark Mode" class="mt-5"></b-tooltip>
      </div>
      <!-- <div class="header-icons d-inline-block align-middle">
        <div class="position-relative d-none d-sm-inline-block">
          <b-dropdown
            variant="empty"
            size="sm"
            right
            toggle-class="header-icon"
            menu-class="position-absolute mt-3 iconMenuDropdown"
            no-caret
          >
            <template slot="button-content">
              <i class="simple-icon-grid" />
            </template>
            <div>
              <router-link tag="a" to="#" class="icon-menu-item">
                <i class="iconsminds-shop-4 d-block" />
                Dashboard
              </router-link>
              <router-link tag="a" to="#" class="icon-menu-item">
                <i class="iconsminds-pantone d-block" />
                Ui
              </router-link>
              <router-link tag="a" to="#" class="icon-menu-item">
                <i class="iconsminds-bar-chart-4 d-block" />
                Charts
              </router-link>
              <router-link tag="a" to="#" class="icon-menu-item">
                <i class="iconsminds-speach-bubble d-block" />
                Chat
              </router-link>
              <router-link tag="a" to="#" class="icon-menu-item">
                <i class="iconsminds-formula d-block" />
                Suervey
              </router-link>
              <router-link tag="a" to="#" class="icon-menu-item">
                <i class="iconsminds-check d-block" />
                Todo
              </router-link>
            </div>
          </b-dropdown>
        </div>

        <div class="position-relative d-inline-block">
          <b-dropdown
            variant="empty"
            size="sm"
            right
            toggle-class="header-icon notificationButton"
            menu-class="position-absolute mt-3 notificationDropdown"
            no-caret
          >
            <template slot="button-content">
              <i class="simple-icon-bell" />
              <span class="count">3</span>
            </template>
            <vue-perfect-scrollbar :settings="{ suppressScrollX: true, wheelPropagation: false }">
              <div
                class="d-flex flex-row mb-3 pb-3 border-bottom"
                v-for="(n,index) in notifications"
                :key="index"
              >
                <router-link tag="a" to="/app/pages/product/details">
                  <img
                    :src="n.img"
                    :alt="n.title"
                    class="img-thumbnail list-thumbnail xsmall border-0 rounded-circle"
                  />
                </router-link>
                <div class="pl-3 pr-2">
                  <router-link tag="a" to="/app/pages/product/details">
                    <p class="font-weight-medium mb-1">{{n.title}}</p>
                    <p class="text-muted mb-0 text-small">{{n.date}}</p>
                  </router-link>
                </div>
              </div>
            </vue-perfect-scrollbar>
          </b-dropdown>
        </div>
      </div> -->
      <div class="user mr-2 pr-0 d-inline-block">
        <b-dropdown
          class="dropdown-menu-right"
          right
          variant="empty"
          toggle-class="p-0"
          menu-class="mt-3"
          no-caret
        >
          <template slot="button-content" v-if="currentUser">
            <span class="name mr-1 text-capitalize">{{currentUser.name}}</span>
            <span>
              <img :alt="currentUser.name" :src="'/assets/img/avatar/' + currentUser.avatar" v-if="currentUser.avatar" />
              <img :alt="currentUser.name" src="/assets/img/avatar/default.jpg" v-else />
            </span>
          </template>
          <b-dropdown-item to="/" active-class>Site</b-dropdown-item>
          <b-dropdown-item to="/settings" active-class>Account</b-dropdown-item>
          <b-dropdown-item>History</b-dropdown-item>
          <b-dropdown-item>Support</b-dropdown-item>
          <b-dropdown-divider />
          <b-dropdown-item @click.prevent="logout">Sign out</b-dropdown-item>
        </b-dropdown>
      </div>
      <div class="position-relative d-none d-sm-inline-block mr-4">
        <div class="btn-group">
          <b-button variant="empty" class="header-icon btn-sm" @click="toggleFullScreen">
            <i
              :class="{'d-inline-block':true,'simple-icon-size-actual':fullScreen,'simple-icon-size-fullscreen':!fullScreen }"
            />
          </b-button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import Switches from "vue-switches";

import { mapGetters, mapMutations, mapActions } from "vuex";
import { MenuIcon, MobileMenuIcon } from "../../components/Svg";
import {
  searchPath,
  menuHiddenBreakpoint,
  localeOptions,
  buyUrl,
  defaultColor,
  themeSelectedColorStorageKey
} from "../../constants/config";
export default {
  components: {
    "menu-icon": MenuIcon,
    "mobile-menu-icon": MobileMenuIcon,
    switches: Switches
  },
  data() {
    return {
      searchKeyword: "",
      isMobileSearch: false,
      isSearchOver: false,
      fullScreen: false,
      menuHiddenBreakpoint,
      searchPath,
      localeOptions,
      buyUrl,
      notifications: [],
      isDarkActive: false,
      isDark: false
    };
  },
  methods: {
    ...mapMutations('menu', ["changeSideMenuStatus", "changeSideMenuForMobile"]),
    ...mapActions('menu', ["signOut"]),
    search() {
      this.$router.push(`${this.searchPath}?search=${this.searchKeyword}`);
      this.searchKeyword = "";
    },
    searchClick() {
      if (window.innerWidth < this.menuHiddenBreakpoint) {
        if (!this.isMobileSearch) {
          this.isMobileSearch = true;
        } else {
          this.search();
          this.isMobileSearch = false;
        }
      } else {
        this.search();
      }
    },
    handleDocumentforMobileSearch() {
      if (!this.isSearchOver) {
        this.isMobileSearch = false;
        this.searchKeyword = "";
      }
    },

    async logout () {
      await this.$store.dispatch('auth/logout')

      this.$router.push({ name: 'login' })
    },

    toggleFullScreen() {
      const isInFullScreen = this.isInFullScreen();

      var docElm = document.documentElement;
      if (!isInFullScreen) {
        if (docElm.requestFullscreen) {
          docElm.requestFullscreen();
        } else if (docElm.mozRequestFullScreen) {
          docElm.mozRequestFullScreen();
        } else if (docElm.webkitRequestFullScreen) {
          docElm.webkitRequestFullScreen();
        } else if (docElm.msRequestFullscreen) {
          docElm.msRequestFullscreen();
        }
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      }
      this.fullScreen = !isInFullScreen;
    },
    getThemeColor() {
      return localStorage.getItem(themeSelectedColorStorageKey)
        ? localStorage.getItem(themeSelectedColorStorageKey)
        : defaultColor;
    },
    isInFullScreen() {
      return (
        (document.fullscreenElement && document.fullscreenElement !== null) ||
        (document.webkitFullscreenElement &&
          document.webkitFullscreenElement !== null) ||
        (document.mozFullScreenElement &&
          document.mozFullScreenElement !== null) ||
        (document.msFullscreenElement && document.msFullscreenElement !== null)
      );
    }
  },
  computed: {
    ...mapGetters({
      currentUser: "auth/user",
      menuType: "menu/getMenuType",
      menuClickCount: "menu/getMenuClickCount",
      selectedMenuHasSubItems: "menu/getSelectedMenuHasSubItems",
      logo: 'site/getLogo',
      darkLogo: 'site/getDarkLogo',
      title: 'site/getTitle'
    })
  },
  beforeDestroy() {
    document.removeEventListener("click", this.handleDocumentforMobileSearch);
  },
  created() {
    const color = this.getThemeColor();
    this.isDarkActive = color.indexOf("dark") > -1;
  },
  watch: {
    "$i18n.locale"(to, from) {
      if (from !== to) {
        this.$router.go(this.$route.path);
      }
    },
    isDarkActive(val) {
      let color = this.getThemeColor();
      let isChange = false;
      if (val && color.indexOf("light") > -1) {
        isChange = true;
        color = color.replace("light", "dark");
      } else if (!val && color.indexOf("dark") > -1) {
        isChange = true;
        color = color.replace("dark", "light");
      }
      // if (isChange) {
      //   localStorage.setItem(themeSelectedColorStorageKey, color);
      //   setTimeout(() => {
      //     window.location.reload();
      //   }, 500);
      // }
    },
    isMobileSearch(val) {
      if (val) {
        document.addEventListener("click", this.handleDocumentforMobileSearch);
      } else {
        document.removeEventListener(
          "click",
          this.handleDocumentforMobileSearch
        );
      }
    }
  },
  mounted() {
    var theme = localStorage.getItem('theme_selected_color')
    if (theme && theme.substring(0, 4) == 'dark') {
      this.isDark = true
    }
  },
};
</script>

<style lang="css">
  .site-logo {
    height: 35px;
    width: auto;
  }
  .site-title {
    font-size: 16px;
    text-transform: capitalize;
  }
  .navbar .navbar-logo {
    width: fit-content;
  }
  .navbar .user img {
    height: 40px;
  }
  @media (max-width: 767px) {
    .navbar .user img {
      height: 30px;
    }
  }
</style>
