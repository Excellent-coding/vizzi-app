import store from '~/store'

export default async (to, from, next) => {
    if (
        store.getters['auth/check'] && 
        (
            store.getters['auth/user'].role == 1 ||
            store.getters['auth/user'].id == store.getters['chat/adminId'] || 
            store.getters['auth/user'].domain_id == store.getters['site/getSiteId']
        )
    ) {
        next()
    } else {
        if (store.getters['auth/user']) {
            await store.dispatch('auth/initUser')
        }
        if (window.location.pathname != '/auth/login') {
            next({ name: 'login' })
        }
    }
}