<template>
    <div>
        <div class="w-100">
            <div class="d-flex justify-content-end align-items-center bg-white" style="z-index: 98">
                <router-link :to="'/'" class="d-inline-flex mr-auto header-title">
                    <img :src="'/data/' + logo" v-if="logo" class="logo ml-4 mr-2" />
                    <div class="mr-auto title my-auto text-capitalize" v-html="title" v-if="title" />
                </router-link>
                <div class="account mr-4">
                    <template v-if="user">
                        <b-dropdown
                            class="dropdown-menu-right"
                            right
                            variant="empty"
                            toggle-class="p-0"
                            menu-class="mt-3"
                            no-caret
                        >
                            <template slot="button-content">
                                <div class="d-flex" :style="'height:' + headerStyle.height + 'px'">
                                    <div class="my-auto mr-2 text-capitalize">{{user.name}}</div>
                                    <div>
                                        <img 
                                            :src="'/assets/img/avatar/' + user.avatar" 
                                            alt="User Avatar" 
                                            class="user-avatar"
                                        />
                                    </div>
                                </div>
                            </template>
                            <b-dropdown-item v-if="user.role < 3" to="/app">Dashboard</b-dropdown-item>
                            <template v-else-if="user && user.role < 5">
                                <b-dropdown-item v-if="user.type == 0" :to="{name: 'wizard.booth.main'}">Booth</b-dropdown-item>
                                <b-dropdown-item v-if="user.type == 1" :to="{name: 'wizard.sponsor.main'}">Sponsor</b-dropdown-item>
                                <b-dropdown-item v-if="user.type == 2" :to="{name: 'wizard.poster.main'}">Poster</b-dropdown-item>
                            </template>
                            <b-dropdown-item :to="{name: 'settings.profile'}">Account</b-dropdown-item>
                            <b-dropdown-divider />
                            <b-dropdown-item @click="logout">Sign out</b-dropdown-item>
                        </b-dropdown>
                    </template>
                    <template v-else>
                        <router-link tag="div" to="/auth/login" class="py-3">
                            Log In
                        </router-link>
                    </template>
                </div>
            </div>
            <div class="header text-center w-100" :style="getHeaderStyle()" v-if="siteStatus || (user && user.role < 3)">
                <div class="d-inline-flex">
                    <template v-for="(item, index) in headItems">
                        <template v-if="item.type != 'cart' && item.type != 'agenda'">
                            <div class="header-btn" :key="index" :style="getButtonStyle()" @click="getPage(item)">
                                {{item.title}}
                            </div>
                        </template>
                        <template v-else-if="item.type.toLowerCase() == 'cart'">
                            <div 
                                :key="index"
                                class="header-btn"
                                v-b-modal.cart_modal
                                :style="getButtonStyle()"
                            >
                                {{item.title}}
                            </div>
                        </template>
                        <template v-else-if="item.type.toLowerCase() == 'agenda'">
                                <div 
                                    :key="index"
                                    class="header-btn"
                                    v-b-modal.agenda_modal
                                    :style="getButtonStyle()"
                                >
                                    {{item.title}}
                                </div>
                        </template>
                    </template>
                </div>
            </div>
        </div>

        <b-modal ref="view_items_modal" id="view_items_modal" size="lg" :title="modalTitle" centered hide-footer>
            <div v-for="(item, index) in viewItems" :key="index">
                <div class="d-flex flex-row">
                    <img class="list-thumbnail responsive border-0" :src="getType(item.item)" />
                    <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                        <div
                            class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center"
                        >
                            <p class="list-item-heading mb-1 truncate">{{item.title}}</p>
                        </div>
                    </div>
                    <div class="my-auto mr-2">
                        <button
                            type="button"
                            class="btn btn-outline-primary mb-1"
                            v-b-modal.view_asset_modal
                            @click="view(item)"
                        >
                            View
                        </button>
                    </div>
                </div>
                <hr />
            </div>
        </b-modal>

        <b-modal ref="view_asset_modal" id="view_asset_modal" size="lg" :title="viewItem.title" centered hide-footer>
            <video v-if="fileExtension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
                <source :src="'/data/'+viewItem.item" type="video/mp4">
            </video>

            <iframe class="w-100" :src="'/data/'+viewItem.item" v-else-if="fileExtension === 'pdf'" style="height: 75vh" />

            <img class="w-100" :src="'/data/'+viewItem.item" v-else />
        </b-modal>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import mixins from '../../mixins/mixins';

export default {
    props: [
        'logo', 'headItems', 'headerStyle', 'modalItems', 'title'
    ],
    mixins: [mixins],
    data() {
        return {
            viewItems: [],
            fileExtension: '',
            modalTitle: '',
            viewItem: {},
            headerTop: '64px',
            route: window.location.hash,
            siteStatus: true
        }
    },
    computed: {
        ...mapGetters({
            user: 'auth/user'
        })
    },
    created () {
        window.addEventListener('scroll', this.handleScroll);
    },
    destroyed () {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        getButtonStyle() {
            let boxShadow = '0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12)'
            let bg = this.headerStyle.btn.bg
            if (!this.headerStyle.btn.boxShadow) {
                boxShadow = 'none'
            }
            if (!this.headerStyle.btn.hasBg) {
                bg = 'transparent'
            }
            const buttonHeight = 2 * Number(this.headerStyle.btn.paddingX) +Number( this.headerStyle.btn.fontSize)
            return {
                'background-color': bg,
                'color': this.headerStyle.btn.color,
                'margin-left': this.headerStyle.btn.space + 'px',
                'margin-right': this.headerStyle.btn.space + 'px',
                'border-radius': this.headerStyle.btn.radius + 'px',
                'margin-top': ((this.headerStyle.height - buttonHeight) / 2 - 5) + 'px',
                'font-size': this.headerStyle.btn.fontSize + 'px',
                'box-shadow': boxShadow,
                'padding-top': this.headerStyle.btn.paddingX + 'px',
                'padding-bottom': this.headerStyle.btn.paddingX + 'px',
                'padding-left': this.headerStyle.btn.paddingY + 'px',
                'padding-right': this.headerStyle.btn.paddingY + 'px'
            }
        },
        getPage(item) {
            if ((item.type == 'booth' || item.type == 'poster' || item.type == 'sponsor') 
            && window.location.pathname != '/exhibit/' + item.url) {
                this.$router.push('/exhibit/' + item.url)
            } else if (item.type == 'iframe') {
                this.type = 'iframe'
                this.page = item
                if (window.location.pathname != item.url) {
                    this.$router.push(item.url)
                }
            } else {
                if (item.type == 'modal') {
                    this.viewItems = []
                    this.modalItems.forEach(element => {
                        if (element.page_id == item.id) {
                            this.viewItems.push(element)
                        }
                    })
                    if (this.viewItems.length == 1) {
                        this.viewItem = this.viewItems[0]
                        this.fileExtension = this.viewItem.item.split('.').pop()
                        this.$refs.view_asset_modal.show()
                    } else {
                        this.modalTitle = item.title
                        this.$refs.view_items_modal.show()
                    }
                } else {
                    if (window.location.pathname != item.url) {
                        this.$router.push(item.url)
                        
                    }
                }
            }
        },
        view(item) {
            this.viewItem = item
            this.fileExtension = this.viewItem.item.split('.').pop()
        },
        getType(file) {
            const extension = file.split('.').pop()
            if (extension == 'mp4') {
                return '/assets/img/mp4.png'
            } else if (extension == 'pdf') {
                return '/assets/img/pdf.png'
            } else {
                return '/assets/img/pic.png'
            }
        },
        getHeaderStyle() {
            let position = 'initial'
            if (this.headerTop == 0) {
                position = 'fixed'
            }
            return {
                'background-color': this.headerStyle.bg,
                'height': this.headerStyle.height + 'px',
                'position': position,
                'top': this.headerTop,
                'z-index': 90
            }
        },
        getNameColor() {
            const code = this.headerStyle.bg.charCodeAt(1)
            let nameText = ''
            if (code < 55.5) {
                nameText = 'black'
            } else {
                nameText = 'white'
            }
            return {
                'color': nameText
            }
        },
        async logout () {
            await this.$store.dispatch('auth/logout')
            this.$router.push({ name: 'login' })
        },
        getTitle () {
            return this.title.charAt(0).toUpperCase() + this.title.charAt(1)
        },
        handleScroll(e) {
        }
    }
}
</script>

<style lang="css">
    .logo {
        width: 64px!important;
        height: 64px!important;
    }
    .header-title:hover {
        color: #3a3a3a
    }
    #view_asset_modal___BV_modal_header_ {
        color: #3a3a3a !important
    }
</style>