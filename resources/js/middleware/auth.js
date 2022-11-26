import store from '~/store'

export default async (to, from, next) => {
  if ((!store.getters['auth/check'] 
  	&& !store.getters['auth/login']) && 
  	(store.getters['auth/user'] && store.getters['auth/user'].role > 4)) {
    next({ name: 'login' })
  } else {
  	if (store.getters['auth/user'] && store.getters['auth/user'].role > 4) {
	    next({ name: 'home' })
  	} else {
	    next()
    }
  }
}
