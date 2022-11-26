import store from '~/store'

export default (to, from, next) => {
    if (store.getters['auth/user']) {
        if (
            store.getters['auth/user'].role === 1 || (
                store.getters['auth/user'].role < 4 && 
                store.getters['site/isMember'] == 1
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