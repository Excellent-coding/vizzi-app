import { mapGetters } from 'vuex'

export default {
    data() {
        return {
            media: [],
            exhibitHtml: '',
            header: [],
            viewData: {},
            posterHeader: {
                position: {
                    x: 1, y: 1, ex: 99, ey: 10
                },
                bg: '#ffffff',
                text: {
                    color: '#000000',
                    size: 36
                }
            },
            headerStyle: {
                bg: '#922c88',
                height: 32,
                btn: {
                    bg: '#d683ce',
                    color: '#ffffff',
                    radius: 4,
                    space: 8,
                    boxShadow: true,
                    hasBg: true,
                    fontSize: 13,
                    paddingX: 2,
                    paddingY: 8
                }
            },
            view: '',
            onView: false
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            boothId: 'booth/getId',
            user: 'auth/user',
            modalWidth: 'site/modalWidth'
        })
    },
    methods: {
        getBackdrop(data) {
            if (data.category && data.category.backdrop) {
                return {
                    'background': 'url(/assets/img/poster-backdrop/' + data.category.backdrop.media + ')',
                    'background-position': 'bottom'
                }
            }
        },
        typeImg(media) {
            const extension = media.split('.').pop()
            if (extension != 'pdf' && extension != 'mp4') {
                return true
            } else {
                return false
            }
        },
        typeVideo(media) {
            const extension = media.split('.').pop()
            if (extension == 'mp4') {
                return true
            } else {
                return false
            }
        },
        typePdf(media) {
            const extension = media.split('.').pop()
            if (extension == 'pdf') {
                return true
            } else {
                return false
            }
        },
        contentHeaderStyle(data) {
            return {
                'color': data.color,
                'font-size': data.size + 'px'
            }
        },
        contentBodyStyle(data) {
            return {
                'color': data.color,
                'font-size': data.size + 'px',
                'margin-left': Math.min(data.position.x, data.position.ex) + '%',
                'margin-top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'word-break': 'break-all',
                'background': data.bg,
                'border-radius': data.borderRadius + 'px',
                'padding': data.padding + 'px',
            }
        },
        contentMediaStyle(data) {
            return {
                'margin-left': Math.min(data.position.x, data.position.ex) + '%',
                'margin-top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'border-radius': data.borderRadius + 'px',
                'padding': data.padding + 'px',
                'background': data.bg
            }
        },
        viewPosterItem(item) {
            this.view = item
            this.$refs['poster_view_modal'].show()
        },
        getExhibitButtonStyle(data) {
            let boxShadow = '0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12)'
            let bg = this.headerStyle.btn.bg
            if (!this.headerStyle.btn.boxShadow) {
                boxShadow = 'none'
            }
            if (!this.headerStyle.btn.hasBg) {
                bg = 'transparent'
            }
            const buttonHeight = 2 * Number(this.headerStyle.btn.paddingX) + Number(this.headerStyle.btn.fontSize)
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
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        },
        getPosterHeaderStyle(data) {
            return {
                'background': data.bg,
                'color': data.text.color,
                'font-size': data.text.size + 'px',
                'text-align': 'center',
                'left': Math.min(data.position.x, data.position.ex) + '%',
                'top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%'
            }
        },
        contentStyle(data) {
            return {
                'padding': data.padding + 'px',
                'background': data.bg,
                'z-index': data.zIndex,
                'left': Math.min(data.position.x, data.position.ex) + '%',
                'top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'border-radius': data.borderRadius + 'px'
            }
        },
        fileType(media) {
            if (media !== undefined) {
                return media.split('.').pop()
            }
        },
        getHeaderStyle(data) {
            if (data) {
                this.headerStyle = data
            }
            return {
                'background-color': this.headerStyle.bg,
                'height': this.headerStyle.height + 'px'
            }
        },
        hideModal(ref) {
            this.$refs[ref].hide()
            this.memberId = 0
        },
    }
}