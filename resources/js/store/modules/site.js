import axios from 'axios'
import * as types from '../mutation-types'
import Cookies from 'js-cookie'

let siteId
if (Cookies.get('siteId') == undefined) {
    siteId = 0
} else {
    siteId = Cookies.get('siteId')
}

export const state = {
    siteId: siteId,
    domain: Cookies.get('domain'),
    siteUser: Cookies.get('siteUser'),
    style: Cookies.get('style'),
    member: Cookies.get('member'),
    logo: Cookies.get('logo'),
    darkLogo: Cookies.get('darkLogo'),
    title: Cookies.get('title'),
    customName: Cookies.get('customName'),
    cartItem: Cookies.get('cartItem'),
    start: Cookies.get('start'),
    end: Cookies.get('end'),
    modalWidth: Cookies.get('modalWidth')
}

export const getters = {
    getSiteId(state) {
        return state.siteId
    },
    getDomain(state) {
        return state.domain
    },
    getSiteUser(state) {
        return state.siteUser
    },
    getStyle(state) {
        return state.style
    },
    isMember(state) {
        return state.member
    },
    getLogo(state) {
        return state.logo
    },
    getDarkLogo(state) {
        return state.darkLogo
    },
    getTitle(state) {
        return state.title
    },
    getCustomName(state) {
        return state.customName
    },
    getCartItem(state) {
        return state.cartItem
    },
    getStartTime(state) {
        return state.start
    },
    getEndTime(state) {
        return state.end
    },
    modalWidth(state) {
        return state.modalWidth
    }
}

export const mutations = {
    [types.SET_SITE] (state, {id, domain, user_id, logo, title, darkLogo, custom_name, start, end, modal} ) {
        state.siteId = id
        state.domain = domain
        state.logo = logo
        state.darkLogo = darkLogo
        state.title = title
        state.siteUser = user_id
        state.customName = custom_name
        state.start = start
        state.end = end
        state.modalWidth = modal
        Cookies.set('siteId', id)
        Cookies.set('domain', domain)
        Cookies.set('logo', logo)
        Cookies.set('darkLogo', darkLogo)
        Cookies.set('title', title)
        Cookies.set('siteUser', user_id)
        Cookies.set('customName', custom_name)
        Cookies.set('start', start)
        Cookies.set('end', end)
        Cookies.set('modalWidth', modal)
    },
    [types.SET_SITE_STYLE] (state, style) {
        state.style = JSON.stringify(style)
        Cookies.set('style', JSON.stringify(style))
    },
    [types.SET_SITE_FAILURE] (state) {
        state.siteId = null
        state.domain = null
        state.siteUser = null
        state.logo = null
        state.darkLogo = null
        state.title = null
        state.customName = null
        Cookies.remove('siteId')
        Cookies.remove('domain')
        Cookies.remove('siteUser')
        Cookies.remove('logo')
        Cookies.remove('darkLogo')
        Cookies.remove('title')
        Cookies.remove('customName')
    },
    [types.SET_SITE_MEMBER](state, data) {
        state.member = data
        Cookies.set('member', data)
    },
    [types.SET_CART_ITEM] (state, data) {
        state.cartItem = JSON.stringify(data)
        Cookies.set('cartItem', JSON.stringify(data))
    },
    [types.SET_CUSTOM_NAME] (state, data) {
        state.customName = JSON.stringify(data)
        Cookies.set('customName', JSON.stringify(data))
    },
    [types.SET_SITE_MODAL](state, {modalWidth}) {
        state.modalWidth = modalWidth
        Cookies.set('modalWidth', modalWidth)
    }
}

export const actions = {
    saveSite({ commit }, payload) {
        commit(types.SET_SITE, payload )
    },
    saveStyle ( {commit}, payload ) {
        commit(types.SET_SITE_STYLE, payload )
    },
    async setSite({ commit }, payload) {
        try {
            const { data } = await axios.post('/site/data', { host: window.location.host.split('.')[0] })
            var flag = 0
            if (data.site.user_id == payload.userId || payload.domainId == data.site.id) {
                flag = 1
            }
            await commit(types.SET_SITE, data.site )
            await commit(types.SET_SITE_MEMBER, flag )
        } catch (e) {
            commit(types.SET_SITE_FAILURE)
        }
    },
    setCartItem ( {commit}, payload ) {
        commit(types.SET_CART_ITEM, payload)
    },
    setCustomName ( {commit}, payload ) {
        commit(types.SET_CUSTOM_NAME, payload)
    },
    setModalWidth({ commit }, payload) {
        commit(types.SET_SITE_MODAL, payload)
    }
}