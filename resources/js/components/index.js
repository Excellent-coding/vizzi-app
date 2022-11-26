import Vue from 'vue'
import Card from './Card'
import Child from './Child'
import Button from './Button'
import Checkbox from './Checkbox'

import Notifications from './Common/Notification'
import Breadcrumb from './Common/Breadcrumb'
import RefreshButton from './Common/RefreshButton'
import Colxx from './Common/Colxx'

import { HasError, AlertError, AlertSuccess } from 'vform'

// Components that are registered globaly.
[
  Card,
  Child,
  Button,
  Checkbox,
  HasError,
  AlertError,
  AlertSuccess,

  Breadcrumb,
  RefreshButton,
  Colxx
].forEach(Component => {
  Vue.component(Component.name, Component)
})
