function page (path) {
  return () => import(`~/pages/${path}`).then(m => m.default || m)
}

export default [
  { path: '/auth/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register/:code', name: 'register', component: page('auth/register') },

  { path: '/session/:id', component: page('site/session') },
  { path: '/app',
    component: page('app/index.vue'),
    children: [
      { path: '', redirect: { name: 'app.home' } },
      { path: 'home', name: 'app.home', component: page('app/home.vue') },
      { 
        path: 'website',
        component: page('app/website'),
        children: [
          { path: '', redirect: {name: 'web.domain'} },
          { path: 'domain', name: 'web.domain', component: page('app/website/domain') },
          { path: ':domain/menus', name: 'web.menus', component: page('app/website/menus') },
          { path: ':domain/:id', name: 'web.page', component: page('app/website/page') },
        ]
      },
      {
        path: 'users',
        component: page('users'),
        children: [
          { path: '', redirect: {name: 'user.admin'} },
          { path: 'admin', name: 'user.admin', component: page('users/admin') },
          { path: 'user', name: 'user.all', component: page('users/user') }
        ]
      },
      { path: 'settings',
        component: page('settings'),
        children: [
          { path: '', redirect: { name: 'settings.profile' } },
          { path: 'profile', name: 'settings.profile', component: page('settings/profile') },
          { path: 'password', name: 'settings.password', component: page('settings/password') }
        ]
      },
      { path: 'warroom',
        component: page('warroom'),
        children: [
          { path: '', name: 'warroom.dash', component: page('warroom/dash') },
          { path: 'event', name: 'warroom.event', component: page('warroom/event') }
        ]
      }
    ]
  },
  { path: '*', component: page('errors/404.vue') }
]
