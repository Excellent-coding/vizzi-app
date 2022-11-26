<template>
    <nav v-bind:class="getClass(status)">
        <div class="container d-flex align-items-center justify-content-between">
            <a class="navbar-logo pull-left scrollTo" href="#home">
                <span class="white"></span>
                <span class="dark"></span>
            </a>
            <ul class="navbar-nav d-none d-lg-flex flex-row">
                <li class="nav-item">
                    <a href="#features" class="scrollTo">FEATURES</a>
                </li>
                <li class="nav-item">
                    <a href="#layouts" class="scrollTo">LAYOUTS</a>
                </li>
                <li class="nav-item">
                    <a href="#components" class="scrollTo">COMPONENTS</a>
                </li>
                <li class="nav-item">
                    <a href="#apps" class="scrollTo">APPS</a>
                </li>
                <li class="nav-item">
                    <a href="#themes" class="scrollTo">THEMES</a>
                </li>
                <template v-if="authStatus">
                    <li class="nav-item" v-if="role && role <= 3">
                        <router-link tag="a" to="/app" class="btn btn-outline-semi-light btn-sm pr-4 pl-4">Home</router-link>
                    </li>
                    <li class="nav-item" v-else>
                        <b-dropdown
                            class="dropdown-menu-right py-4 mt-2"
                            right
                            variant="empty"
                            toggle-class="p-0"
                            no-caret
                        >
                            <template slot="button-content" v-if="user">
                                <span class="name mr-1">{{user.name}}</span>
                                <span>
                                    <img :alt="user.name" :src="user.photo_url" class="avatar" v-if="user.photo_url" />
                                    <img :alt="user.name" src="/assets/img/avatar/default.jpg" class="avatar" v-else />
                                </span>
                            </template>
                            <b-dropdown-item @click.prevent="logout">Sign out</b-dropdown-item>
                        </b-dropdown>
                    </li>
                </template>
                <template v-else>
                    <li class="nav-item pl-4 pr-1">
                        <router-link tag="a" to="login" class="btn btn-outline-semi-light btn-sm pr-4 pl-4">Login</router-link>
                    </li>
                </template>
            </ul>
            <a href="#" class="mobile-menu-button">
                <i class="simple-icon-menu"></i>
            </a>
        </div>
    </nav>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    name: 'TopnavBar',

    props: {
        status: {
            type: Number,
            default: 0
        },
        authStatus: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            role: 5
        }
    },

    computed: {
        ...mapGetters({
            user: "auth/user"
        }),
    },

    methods: {
        getClass(data) {
            return {
                'landing-page-nav headroom headroom--top headroom--not-bottom': data == 0,
                'landing-page-nav headroom headroom--not-bottom headroom--not-top headroom--unpinned': data == 1,
                'landing-page-nav headroom headroom--not-bottom headroom--not-top headroom--pinned': data == 2
            }
        },
        async logout () {
            await this.$store.dispatch('auth/logout')

            this.$router.push({ name: 'login' })
        }
    },

    mounted() {
        if (this.user) {
            this.role = this.user.role
        }
    }
}
</script>

<style lang="css">
    .avatar {
        border-radius: 50%;
        width: 42px;
        height: 42px;
    }
    .dropdown-menu {
        text-align: center!important;
        padding: 0!important
    }
    .navbar-nav li {
        padding-left: 0!important;
    }
    .name {
        color: white
    }
</style>
