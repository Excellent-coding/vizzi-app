import store from '~/store'

export default (to, from, next) => {
  if (store.getters['auth/user']) {
    if (store.getters['auth/user'].role === 1 || (store.getters['auth/user'].role === 2 && store.getters['site/getSiteId'] == store.getters['auth/user'].domain_id)) {
      next()
    } else {
      next({ name: 'home' })
    }
  } else {
    next({ name: 'home' })
  }
}