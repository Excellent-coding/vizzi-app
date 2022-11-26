export const state = {
    id: null,
    item: {}
}

export const getters = {
    getId(state) {
        return state.id
    },
    getItem(state) {
        return state.item
    }
}

export const mutations = {
    setId(state, payload) {
        state.id = payload.id
    },
    setItem(state, payload) {
        state.item = payload
    }
}

export const actions = {
    setId({commit}, {id}) {
        commit('setId', {id})
    },
    setItem({ commit }, payload) {
        commit('setItem', payload)
    } 
}