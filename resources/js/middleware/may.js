import store from '~/store'

export default (to, from, next) => {
  if (store.getters['auth/user']) {
    if (
        store.getters['auth/user'].role === 1 || (
            store.getters['auth/user'].role === 2 && 
            store.getters['site/getSiteUser'] == store.getters['auth/user'].id
        ) || (
            store.getters['auth/user'].role === 3 && 
            store.getters['site/getSiteUser'] == store.getters['auth/user'].parent
        )
    ) {
      next()
    } else {
      next({ name: 'home' })
    }
  } else {
    next({ name: 'home' })
  }
}