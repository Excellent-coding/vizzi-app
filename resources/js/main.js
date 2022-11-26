import Vue from 'vue'
import PubNubVue from 'pubnub-vue'
import Notifications from 'vue-notification'

import BootstrapVue from 'bootstrap-vue'
import vuePerfectScrollbar from 'vue-perfect-scrollbar'
import contentmenu from 'v-contextmenu'
import VueLineClamp from 'vue-line-clamp'
import VueScrollTo from 'vue-scrollto'
//import Notifications from './components/Common/Notification'
import VCalendar from 'v-calendar'

import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'

import axios from 'axios'

import '~/plugins'
import '~/components'

Vue.config.productionTip = false
Vue.config.devtools = true

Vue.use(BootstrapVue)
Vue.use(Notifications)
Vue.component('vue-perfect-scrollbar', vuePerfectScrollbar)
Vue.use(contentmenu)
Vue.use(VueLineClamp, {
  importCss: true
})
Vue.use(VueScrollTo)

Vue.use(PubNubVue, {
  subscribeKey: 'sub-c-49812e5a-f539-11ea-8db0-569464a6854f',
  publishKey: 'pub-c-99b53823-bd09-4a9a-a2c7-7e75e5a6dea4'
})

Vue.use(VCalendar)

Vue.use(require('vue-moment'))
Vue.use(require('vue-shortkey'))

axios.defaults.baseURL = `${process.env.MIX_APP_URL}/api`
window.axios = axios

new Vue({
  i18n,
  store,
  router,
  ...App
})
