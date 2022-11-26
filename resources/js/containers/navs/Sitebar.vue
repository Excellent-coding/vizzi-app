  <template>
    <div class="sidebar" :style="getSidebarHeight()" @click.stop="()=>{}">
        <div class="main-menu" :style="getHeight()">
            <vue-perfect-scrollbar class="scroll" :settings="{ suppressScrollX: true, wheelPropagation: false }">
                <ul class="list-unstyled">
                    <li v-for="item in menuItems" :class="{ 'active' : (selectedParentMenu === item.id && viewingParentMenu === '') || viewingParentMenu === item.id }" :key="`parent_${item.id}`" :data-flag="item.id">
                        <a v-if="item.subs && item.subs.length>0" @click.prevent="openSubMenu($event,item)" :href="`#${item.to}`">
                            <i :class="item.icon" />
                            {{ item.label }}
                        </a>
                        <router-link v-else @click.native="changeSelectedParentHasNoSubmenu(item.id)" :to="item.to">
                            <i :class="item.icon" />
                            {{ item.label }}
                        </router-link>
                    </li>
                </ul>
            </vue-perfect-scrollbar>
        </div>

        <div class="sub-menu" :style="getHeight()">
            <vue-perfect-scrollbar class="scroll" :settings="{ suppressScrollX: true, wheelPropagation: false }">
                <ul v-for="item in menuItems" :class="{'list-unstyled':true, 'd-block' : (selectedParentMenu === item.id && viewingParentMenu === '') || viewingParentMenu === item.id }" :data-parent="item.id" :key="`sub_${item.id}`">
                    <li v-for="(sub, subIndex) in item.subs" :key="subIndex" :class="{'has-sub-item' : sub.subs && sub.subs.length > 0 , 'active' : $route.path.indexOf(sub.to)>-1}">
                        <router-link :to="sub.to">
                            <i :class="sub.icon" />
                            <span>{{ sub.label }}</span>
                        </router-link>
                    </li>
                </ul>
            </vue-perfect-scrollbar>
        </div>
    </div>
</template>

<script>
import {
    mapGetters,
    mapMutations
} from "vuex";
import {
    menuHiddenBreakpoint,
    subHiddenBreakpoint
} from "../../constants/config";
import menuItems from '../../constants/siteMenu'

export default {
    data() {
        return {
            selectedParentMenu: "",
            viewingParentMenu: "",
            menuItems,
            headerTop: 64
        };
    },
    props: ['height'],
    mounted() {
        this.selectMenu();
        window.addEventListener("resize", this.handleWindowResize);
        document.addEventListener("click", this.handleDocumentClick);
        this.handleWindowResize();
    },
    beforeDestroy() {
        document.removeEventListener("click", this.handleDocumentClick);
        window.removeEventListener("resize", this.handleWindowResize);
    },

    methods: {
        ...mapMutations('menu', [
            "changeSideMenuStatus",
            "addMenuClassname",
            "changeSelectedMenuHasSubItems"
        ]),
        getSidebarHeight() {
            const height = this.height + this.headerTop
            return {
                'padding-top': height + 'px',
                'height': 'calc(100% - '+ height +'px)'
            }
        },
        getHeight() {
            const height = this.height + this.headerTop
            return {
                'height': 'calc(100% - '+ height +'px)'
            }
        },

        selectMenu(data) {
            const currentParentUrl = this.$route.path
                .split("/")
                .filter(x => x !== "")[0];
            if (currentParentUrl !== undefined && currentParentUrl !== null) {
                this.selectedParentMenu = currentParentUrl.toLowerCase();
            } else {
                this.selectedParentMenu = "home";
            }
            this.isCurrentMenuHasSubItem();
        },
        isCurrentMenuHasSubItem() {
            const menuItem = this.menuItems.find(
                x => x.id === this.selectedParentMenu
            );
            
            const isCurrentMenuHasSubItem =
                menuItem && menuItem.subs && menuItem.subs.length > 0 ? true : false;
                
            if (isCurrentMenuHasSubItem != this.selectedMenuHasSubItems) {
                if (!isCurrentMenuHasSubItem) {
                    this.changeSideMenuStatus({
                        step: 0,
                        classNames: this.menuType,
                        selectedMenuHasSubItems: false
                    });
                }else{
                    this.changeSideMenuStatus({
                        step: 0,
                        classNames: this.menuType,
                        selectedMenuHasSubItems: true
                    });
                }
            }

            return isCurrentMenuHasSubItem;
        },

        changeSelectedParentHasNoSubmenu(parentMenu) {
            this.selectedParentMenu = parentMenu;
            this.viewingParentMenu = parentMenu;
            this.changeSelectedMenuHasSubItems(false);
            this.changeSideMenuStatus({
                step: 0,
                classNames: this.menuType,
            });
        },

        openSubMenu(e, menuItem) {
            const selectedParent = menuItem.id;
            const hasSubMenu = menuItem.subs && menuItem.subs.length > 0;
            this.changeSelectedMenuHasSubItems(hasSubMenu);
            if (!hasSubMenu) {
                this.viewingParentMenu = selectedParent;
                this.selectedParentMenu = selectedParent;
                this.toggle();
            } else {
                const currentClasses = this.menuType ?
                    this.menuType.split(" ").filter(x => x !== "") :
                    "";

                if (!currentClasses.includes("menu-mobile")) {
                    if (
                        currentClasses.includes("menu-sub-hidden") &&
                        (this.menuClickCount === 2 || this.menuClickCount === 0)
                    ) {
                        this.changeSideMenuStatus({
                            step: 3,
                            classNames: this.menuType,
                            selectedMenuHasSubItems: hasSubMenu
                        });
                    } else if (
                        currentClasses.includes("menu-hidden") &&
                        (this.menuClickCount === 1 || this.menuClickCount === 3)
                    ) {
                        this.changeSideMenuStatus({
                            step: 2,
                            classNames: this.menuType,
                            selectedMenuHasSubItems: hasSubMenu
                        });
                    } else if (
                        currentClasses.includes("menu-default") &&
                        !currentClasses.includes("menu-sub-hidden") &&
                        (this.menuClickCount === 1 || this.menuClickCount === 3)
                    ) {
                        this.changeSideMenuStatus({
                            step: 0,
                            classNames: this.menuType,
                            selectedMenuHasSubItems: hasSubMenu
                        });
                    }
                } else {
                    this.addMenuClassname({
                        classname: "sub-show-temporary",
                        currentClasses: this.menuType
                    });
                }
                this.viewingParentMenu = selectedParent;
            }
        },
        handleDocumentClick(e) {
            this.viewingParentMenu = "";
            this.selectMenu();
            this.toggle();
        },
        toggle() {
            const currentClasses = this.menuType.split(" ").filter(x => x !== "");
            if (
                currentClasses.includes("menu-sub-hidden") &&
                this.menuClickCount === 3
            ) {
                this.changeSideMenuStatus({
                    step: 2,
                    classNames: this.menuType,
                    selectedMenuHasSubItems: this.selectedMenuHasSubItems
                });
            } else if (
                currentClasses.includes("menu-hidden") ||
                currentClasses.includes("menu-mobile")
            ) {
                if (!(this.menuClickCount === 1 && !this.selectedMenuHasSubItems)) {
                    this.changeSideMenuStatus({
                        step: 0,
                        classNames: this.menuType,
                        selectedMenuHasSubItems: this.selectedMenuHasSubItems
                    });
                }
            }
        },
        // Resize
        handleWindowResize(event) {
            if (event && !event.isTrusted) {
                return;
            }
            let nextClasses = this.getMenuClassesForResize(this.menuType);
            this.changeSideMenuStatus({
                step: 0,
                classNames: nextClasses.join(" "),
                selectedMenuHasSubItems: this.selectedMenuHasSubItems
            });
        },
        getMenuClassesForResize(classes) {
            let nextClasses = classes.split(" ").filter(x => x !== "");
            const windowWidth = window.innerWidth;

            if (windowWidth < menuHiddenBreakpoint) {
                nextClasses.push("menu-mobile");
            } else if (windowWidth < subHiddenBreakpoint) {
                nextClasses = nextClasses.filter(x => x !== "menu-mobile");
                if (
                    nextClasses.includes("menu-default") &&
                    !nextClasses.includes("menu-sub-hidden")
                ) {
                    nextClasses.push("menu-sub-hidden");
                }
            } else {
                nextClasses = nextClasses.filter(x => x !== "menu-mobile");
                if (
                    nextClasses.includes("menu-default") &&
                    nextClasses.includes("menu-sub-hidden")
                ) {
                    nextClasses = nextClasses.filter(x => x !== "menu-sub-hidden");
                }
            }
            return nextClasses;
        },
        middleware(item) {
            if (this.user) {
                const unique = item.unique
                if (this.user.role == 1 || this.user.role == 2) {
                    return true
                } else {
                    if (unique != undefined) {
                        if (unique == this.user.role) {
                            const type = item.id
                            if (
                                (type == 0 && this.user.type == 0) ||
                                (type == 1 && this.user.type == 1) ||
                                (type == 2 && this.user.type == 2)
                            ) {
                                return true
                            } else {
                                return false
                            }
                        }
                    } else {
                        const middleware = item.middleware
                        if (middleware) {
                            if (middleware >= this.user.role) {
                                return true
                            } else {
                                return false
                            }
                        } else {
                            return true
                        }
                    }
                }
            } else {
                return false
            }
        },
        handleScroll(e) {
            if (window.pageYOffset > 64) {
                this.headerTop = 0
            } else {
                this.headerTop = 64
            }
        }
    },
    created () {
        window.addEventListener('scroll', this.handleScroll);
    },
    destroyed () {
        window.removeEventListener('scroll', this.handleScroll);
    },
    computed: {
        ...mapGetters({
            menuType: "menu/getMenuType",
            menuClickCount: "menu/getMenuClickCount",
            selectedMenuHasSubItems: "menu/getSelectedMenuHasSubItems",
            user: "auth/user"
        })
    },
    watch: {
        $route(to, from) {
            if (to.path !== from.path) {
                const toParentUrl = to.path.split("/").filter(x => x !== "")[1];
                if (toParentUrl !== undefined && toParentUrl !== null) {
                    this.selectedParentMenu = toParentUrl.toLowerCase();
                } else {
                    this.selectedParentMenu = "dashboards";
                }
                this.selectMenu();
                this.toggle();                  
                this.changeSideMenuStatus({
                    step: 0,
                    classNames: this.menuType,
                    selectedMenuHasSubItems: this.selectedMenuHasSubItems
                });

                window.scrollTo(0, top);
            }
        }
    }
};
</script>
