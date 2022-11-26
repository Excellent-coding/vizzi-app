import store from '~/store'

export default (to, from, next) => {
  if (store.getters['auth/user']) {
    if (store.getters['auth/user'].role === 1) {
      next()
    } else {
      next({ name: 'home' })
    }
  } else {
    next({ name: 'home' })
  }
}