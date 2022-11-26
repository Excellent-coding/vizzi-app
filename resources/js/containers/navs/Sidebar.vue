  <template>
    <div class="sidebar" @click.stop="()=>{}">
        <div class="main-menu">
            <vue-perfect-scrollbar class="scroll" :settings="{ suppressScrollX: true, wheelPropagation: false }">
                <ul class="list-unstyled">
                    <li v-for="item in menus" :class="{ 'active' : (selectedParentMenu === item.id && viewingParentMenu === '') || viewingParentMenu === item.id }" :key="`parent_${item.id}`" :data-flag="item.id">
                        <template v-if="middleware(item)">
                            <a v-if="item.newWindow" :href="item.to" rel="noopener noreferrer" target="_blank">
                                <i :class="item.icon" />
                                {{ item.label }}
                            </a>
                            <a v-else-if="item.subs && item.subs.length>0" @click.prevent="openSubMenu($event,item)" :href="`#${item.to}`">
                                <i :class="item.icon" />
                                {{ item.label }}
                            </a>
                            <router-link v-else @click.native="changeSelectedParentHasNoSubmenu(item.id)" :to="item.to">
                                <i :class="item.icon" />
                                {{ item.label }}
                            </router-link>
                        </template>
                    </li>
                </ul>
            </vue-perfect-scrollbar>
        </div>

        <div class="sub-menu">
            <vue-perfect-scrollbar class="scroll" :settings="{ suppressScrollX: true, wheelPropagation: false }">
                <ul v-for="(item,itemIndex) in menus" :class="{'list-unstyled':true, 'd-block' : (selectedParentMenu === item.id && viewingParentMenu === '') || viewingParentMenu === item.id }" :data-parent="item.id" :key="`sub_${item.id}`">
                    <li v-for="(sub, subIndex) in item.subs" :key="subIndex" :class="{'has-sub-item' : sub.subs && sub.subs.length > 0 , 'active' : $route.path.indexOf(sub.to)>-1}">
                        <template v-if="middleware(sub)">
                            <a v-if="sub.newWindow" :href="sub.to" rel="noopener noreferrer" target="_blank">
                                <i :class="sub.icon" />
                                <span>{{ sub.label }}</span>
                            </a>
                            <template v-else-if="sub.subs &&  sub.subs.length > 0">
                                <b-link v-b-toggle="`menu_${itemIndex}_${subIndex}`" variant="link" class="rotate-arrow-icon opacity-50">
                                    <i class="simple-icon-arrow-down"></i>
                                    <span class="d-inline-block">{{sub.label}}</span>
                                </b-link>
                                <b-collapse visible :id="`menu_${itemIndex}_${subIndex}`">
                                    <ul class="list-unstyled third-level-menu">
                                        <li v-for="(thirdLevelSub, thirdIndex) in sub.subs" :key="`third_${itemIndex}_${subIndex}_${thirdIndex}`" :class="{'third-level-menu':true , 'active' : $route.path ===thirdLevelSub.to}">
                                            <template v-if="middleware(thirdLevelSub)">
                                                <a v-if="thirdLevelSub.newWindow" :href="thirdLevelSub.to" rel="noopener noreferrer" target="_blank">
                                                    <i :class="thirdLevelSub.icon" />
                                                    <span>{{ thirdLevelSub.label }}</span>
                                                </a>
                                                <router-link v-else :to="thirdLevelSub.to">
                                                    <i :class="thirdLevelSub.icon" />
                                                    <span>{{ thirdLevelSub.label }}</span>
                                                </router-link>
                                            </template>
                                        </li>
                                    </ul>
                                </b-collapse>
                            </template>
                            <router-link v-else :to="sub.to">
                                <i :class="sub.icon" />
                                <span>{{ sub.label }}</span>
                            </router-link>
                        </template>
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
import menuItems from "../../constants/menu";
import subMenuItems from "../../constants/subMenu";

export default {
    data() {
        return {
            selectedParentMenu: "",
            menuItems,
            subMenuItems,
            viewingParentMenu: "",
            menus: []
        };  
    },
    mounted() {
        const parts = window.location.host.split('.');
        if (parts[0] === 'vcs') {
            this.menus = menuItems
        } else {
            this.menus = subMenuItems
        }
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
        selectMenu(data) {
            const currentParentUrl = this.$route.path
                .split("/")
                .filter(x => x !== "")[1];
                
            if (currentParentUrl !== undefined && currentParentUrl !== null) {
                this.selectedParentMenu = currentParentUrl.toLowerCase();
            } else {
                this.selectedParentMenu = "dashboards";
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
                selectedMenuHasSubItems: false
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
                const only = item.only
                if (only != undefined) {
                    if (this.user.role == only) {
                        return true
                    } else {
                        false
                    }
                } else {
                    const unique = item.unique

                    if (unique != undefined) {
                        if (this.user.role == 1 ||this.user.role == 2) {
                            return true
                        } else {
                            if (unique == this.user.role) {
                                const type = item.id
                                if (
                                    (type == 'booth' && this.user.type == 0) ||
                                    (type == 'sponsor' && this.user.type == 1) ||
                                    (type == 'poster' && this.user.type == 2)
                                ) {
                                    return true
                                } else {
                                    return false
                                }
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
