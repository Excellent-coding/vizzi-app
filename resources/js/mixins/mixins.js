import { mapGetters } from 'vuex'

export default {
    data() {
        return {
            customNameList: []
        }
    },
    created() {
        if (this.customName && this.customName !== 'undefined') {
            this.customNameList = JSON.parse(this.customName)
        }
    },
    computed: {
        ...mapGetters({
            cartItems: 'site/getCartItem',
            customName: 'site/getCustomName',
            siteId: 'site/getSiteId',
            menuType: 'menu/getMenuType'
        }),
        host() {
            return window.location.host.split('.')[0]
        }
    },
    methods: {
        getCartItems() {
            var data = []
            if (this.cartItems) {
                data = JSON.parse(this.cartItems)
            }
            return data
        },
        getMark(item) {
            if (this.cartItems) {
                var data = JSON.parse(this.cartItems)
                var flag = false
                data.forEach(element => {
                    if (element.id == item.id) {
                        flag = true
                    }
                });
                return flag
            } else {
                return false
            }
        },
        async addCartItem(item) {
            if (this.cartItems) {
                var data = JSON.parse(this.cartItems)
                var isNew = true
                data.forEach((element, index) => {
                    if (element.id == item.id) {
                        isNew = false
                        data.splice(index, 1)
                    }
                })
                if (isNew) {
                    data.push(item)
                }
                await this.$store.dispatch('site/setCartItem', data)
            } else {
                var data = []
                data.push(item)
                await this.$store.dispatch('site/setCartItem', data)
            }
        },
        $ct(text) {
            var customText = text
            this.customNameList.forEach(item => {
                if (item.title.toLowerCase() == text.toLowerCase()) {
                    customText = item.customTitle
                }
            });
            return customText
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        },
        getType(file) {
            const extension = file.split('.').pop()
            if (extension == 'mp4') {
                return '/assets/img/type/video.png'
            } else if (extension == 'pdf') {
                return '/assets/img/type/pdf.png'
            } else {
                return '/assets/img/type/image.png'
            }
        },
        getTypeText(file) {
            const extension = file.split('.').pop()
            if (extension == 'mp4') {
                return 'video'
            } else if (extension == 'pdf') {
                return 'pdf'
            } else {
                return 'image'
            }
        },
        getStyle(file) {
            const extension = file.split('.').pop()
            if (extension == 'pdf') {
                return {
                    'letter-spacing': '4px',
                    'background': this.headerStyle.bg
                }
            } else {
                return {
                    'letter-spacing': '1px',
                    'background': this.headerStyle.bg
                }
            }
        }
    }
}