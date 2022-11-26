import axios from 'axios'
import Cookies from 'js-cookie'

export const state = {
    isLoadContacts: false,
    isLoadConversations: false,
    error: "",
    managers: [],
    users: [],
    admin: {},
    adminId: Cookies.get("adminId"),
    selectedUser: null,
    contactsSearchResult: null,
    conversations: null,
    channel: null,
    channelType: null
};

export const getters = {
    isLoadContacts: state => state.isLoadContacts,
    isLoadConversations: state => state.isLoadConversations,
    error: state => state.error,
    managers: state => state.managers,
    users: state => state.users,
    admin: state => state.admin,
    adminId: state => state.adminId,
    conversations: state => state.conversations,
    contactsSearchResult: state => state.contactsSearchResult,
    selectedUser: state => state.selectedUser,
    channel: state => state.channel,
    channelType: state => state.channelType
}

export const mutations = {
    getContactsSuccess(state, payload) {
        state.managers = payload.managers
        state.users = payload.users
        state.admin = payload.admin
        Cookies.set('adminId', payload.admin.id)
    },
    setSelectedSuccess(state, payload) {
        state.selectedUser = payload.userId
    },
    setChannel(state, payload) {
        state.channel = payload.channel
        state.channelType = payload.channelType
    }
}

export const actions = {
    getContacts({ commit }, {userId, siteId}) {
        axios
        .post(`site/contacts`, {siteId: siteId})
        .then(res => {
            if (res.status) {
                commit('getContactsSuccess', { managers: res.data.managers, users: res.data.users, userId: userId, admin: res.data.admin })
            } else {
                commit('getContactsError', 'error:getContacts')
            }
        })
    },
    setSelectedUser({ commit }, {userId}) {
        commit('setSelectedSuccess', {userId})
    },
    setChannel({ commit }, payload) {
        commit('setChannel', payload)
    }
}