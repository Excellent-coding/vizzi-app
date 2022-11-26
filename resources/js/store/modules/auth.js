import axios from 'axios'
import Cookies from 'js-cookie'
import * as types from '../mutation-types'

export const state = {
  user: null,
  token: Cookies.get('token'),
  diffZone: Cookies.get('diffZone')
}

// getters
export const getters = {
  user: state => state.user,
  token: state => state.token,
  check: state => state.user !== null,
  diffZone: state => state.diffZone
}

// mutations
export const mutations = {
  [types.SAVE_TOKEN] (state, { token, remember }) {
    state.token = token
    Cookies.set('token', token, { expires: remember ? 365 : null })
  },

  [types.FETCH_USER_SUCCESS] (state, { user }) {
    state.user = user
  },

  [types.FETCH_USER_FAILURE] (state) {
    state.token = null
    Cookies.remove('token')
  },

  [types.LOGOUT] (state) {
    state.user = null
    state.token = null

    Cookies.remove('token')
    Cookies.remove('siteId')
  },

  [types.UPDATE_USER] (state, { user }) {
    state.user = user
  },

  [types.SET_ZONE] (state, { diffZone }) {
    state.diffZone = diffZone
    Cookies.set('diffZone', diffZone)
  }
}

// actions
export const actions = {
  saveToken ({ commit, dispatch }, payload) {
    commit(types.SAVE_TOKEN, payload)
  },

  async fetchUser ({ commit }) {
    try {
      const { data } = await axios.get('/user')
      
      commit(types.FETCH_USER_SUCCESS, {user: data})
    } catch (e) {
      commit(types.FETCH_USER_FAILURE)
    }
  },

  setUser ({ commit }, payload) {
    try {
      commit(types.FETCH_USER_SUCCESS, { user: payload })
    } catch (e) {
      commit(types.FETCH_USER_FAILURE)
    }
  },

  updateUser ({ commit }, payload) {
    commit(types.UPDATE_USER, payload)
  },

  initUser ({ commit }) {
    commit(types.LOGOUT)
  },

  async logout ({ commit }) {
    try {
      await axios.post('/logout')
    } catch (e) { }

    commit(types.LOGOUT)
  },

  async fetchOauthUrl (ctx, { provider }) {
    const { data } = await axios.post(`/oauth/${provider}`)

    return data.url
  },

  setZone({commit}, payload) {
    commit(types.SET_ZONE, payload)
  }
}
