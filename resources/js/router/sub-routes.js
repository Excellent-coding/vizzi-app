function page (path) {
    return () => import(`~/pages/${path}`).then(m => m.default || m)
  }
  let loginComponent = page('auth/login')
//  let loginComponent = page('site/auth/login')
  
  if (window.config.custom_login && window.config.custom_login.length > 0) {
    loginComponent = page(window.config.custom_login + '/auth/login')
  }

  export default [
    { path: '/', name: 'home', component: page('site/main.vue') },
    {
      path: '/auth',
      component: page('site/auth'),
      children: [
        { path: '', redirect: {name: 'login' } },
        { path: 'login', name: 'login', component: loginComponent },
        { path: 'register/:code', name: 'signUp', component: page('site/auth/register2') },
        { path: 'wizard/:code', name: 'register', component: page('site/auth/register') }
      ]
    },
    { path: '/forgot-password', component: page('auth/password/email.vue'), meta: { auth:false } },
    { path: '/reset/:token', component: page('auth/password/reset.vue'), meta: { auth:false } },
    { path: '/app',
      component: page('app/index.vue'),
      redirect: {name: 'app.home'},
      children: [
        { path: 'home', name: 'app.home', component: page('site/dashboard') },
        {
          path: 'website',
          component: page('app/website'),
          children: [
            { path: '', redirect: {name: 'web.domain'} },
            { path: 'domain', name: 'web.domain', component: page('app/website/domain') },
            { path: ':domain/menus', name: 'web.menus', component: page('app/website/menus') },
            { path: ':domain/:id', name: 'web.page', component: page('app/website/page') },
            { path: ':domain/exhibit/:type/:id', name: 'web.exhibit', component: page('app/website/exhibit') },
            { path: 'regedit', name: 'web.regedit', component: page('app/website/regedit') },
            { path: 'cart', name: 'web.cart', component: page('app/website/cart') },
          ]
        },
        {
          path: 'wizard',
          component: page('site/wizard'),
          redirect: {name: 'booth.main'},
          children: [
            {
              path: 'booth',
              component: page('site/wizard/booth'),
              children: [
                { path: '', redirect: {name: 'booth.main'} },
                { path: 'main', name: 'booth.main', component: page('site/wizard/booth/main') },
                { path: 'upload', name: 'booth.upload', component: page('site/wizard/booth/upload') },
                { path: 'models', name: 'booth.models', component: page('site/wizard/booth/models') },
                { path: 'edit/:id', name: 'booth.edit', component: page('site/wizard/booth/edit') },
                { path: 'view', name: 'booth.view', component: page('site/wizard/booth/booths') },
                { path: 'users', name: 'booth.users', component: page('site/wizard/booth/users') },
                { path: 'setting', name: 'booth.setting', component: page('site/wizard/booth/setting') }
              ]
            },
            {
              path: 'sponsor',
              component: page('site/wizard/sponsor'),
              children: [
                { path: '', redirect: {name: 'sponsor.main'} },
                { path: 'main', name: 'sponsor.main', component: page('site/wizard/sponsor/main') },
                { path: 'upload', name: 'sponsor.upload', component: page('site/wizard/sponsor/upload') },
                { path: 'models', name: 'sponsor.models', component: page('site/wizard/sponsor/models') },
                { path: 'edit/:id', name: 'sponsor.edit', component: page('site/wizard/sponsor/edit') },
                { path: 'view', name: 'sponsor.view', component: page('site/wizard/sponsor/booths') },
                { path: 'users', name: 'sponsor.users', component: page('site/wizard/sponsor/users') },
                { path: 'setting', name: 'sponsor.setting', component: page('site/wizard/sponsor/setting') }
              ]
            },
            {
              path: 'poster',
              component: page('site/wizard/poster'),
              children: [
                { path: '', redirect: {name: 'poster.main'} },
                { path: 'main', name: 'poster.main', component: page('site/wizard/poster/main') },
                { path: 'category', name: 'poster.category', component: page('site/wizard/poster/category') },
                { path: 'models', name: 'poster.models', component: page('site/wizard/poster/models') },
                { path: 'edit/:id', name: 'poster.edit', component: page('site/wizard/poster/edit') },
                { path: 'preview/:id', name: 'poster.preview', component: page('site/wizard/poster/preview') },
                { path: 'users', name: 'poster.users', component: page('site/wizard/poster/users') },
                { path: 'setting', name: 'poster.setting', component: page('site/wizard/poster/setting') }
              ]
            }
          ]
        },
        {
          path: 'users',
          component: page('users'),
          children: [
            { path: '', redirect: {name: 'user.admin'} },
            { path: 'admin', name: 'user.admin', component: page('users/admin') },
            { path: 'user', name: 'user.front', component: page('users/user') },
            { path: 'booth', name: 'user.booth', component: page('users/booth') },
            { path: 'sponsor', name: 'user.sponsor', component: page('users/sponsor') },
            { path: 'poster', name: 'user.poster', component: page('users/poster') },
            { path: 'event', name: 'user.event', component: page('users/event') },
            { path: 'all', name: 'user.all', component: page('users/all') }
          ]
        },
        { path: 'warroom', name: 'app.warroom', component: page('site/wizard/chat') }
      ]
    },
    { path: '/wizard',
      component: page('site/wizard'),
      redirect: {name: 'booth.main'},
      children: [
        {
          path: 'booth',
          component: page('site/wizard/booth'),
          children: [
            { path: '', name: 'wizard.booth', redirect: {name: 'booth.main'} },
            { path: 'main', name: 'wizard.booth.main', component: page('site/wizard/booth/main') },
            { path: 'view', name: 'wizard.booth.view', component: page('site/wizard/booth/booths') },
            { path: 'models', name: 'wizard.booth.models', component: page('site/wizard/booth/models') },
            { path: 'edit/:id', name: 'wizard.booth.edit', component: page('site/wizard/booth/edit') }
          ]
        },
        {
          path: 'sponsor',
          component: page('site/wizard/sponsor'),
          children: [
            { path: '', name: 'wizard.sponsor', redirect: {name: 'wizard.sponsor.main'} },
            { path: 'main', name: 'wizard.sponsor.main', component: page('site/wizard/sponsor/main') },
            { path: 'view', name: 'wizard.sponsor.view', component: page('site/wizard/sponsor/booths') },
            { path: 'models', name: 'wizard.sponsor.models', component: page('site/wizard/sponsor/models') },
            { path: 'edit/:id', name: 'wizard.sponsor.edit', component: page('site/wizard/sponsor/edit') }
          ]
        },
        {
          path: 'poster',
          component: page('site/wizard/poster'),
          children: [
            { path: '', name: 'wizard.poster', redirect: {name: 'wizard.poster.main'} },
            { path: 'main', name: 'wizard.poster.main', component: page('site/wizard/poster/main') },
            { path: 'models', name: 'wizard.poster.models', component: page('site/wizard/poster/models') },
            { path: 'edit/:id', name: 'wizard.poster.edit', component: page('site/wizard/poster/edit') },
            { path: 'preview/:id', name: 'wizard.poster.preview', component: page('site/wizard/poster/preview') }
          ]
        },
        { path: 'network', name: 'networking', component: page('site/wizard/networking') },
        { path: 'chat', name: 'chat', component: page('site/wizard/chat') },

        { path: 'team', name: 'team', component: page('site/wizard/team') },
      ]
    },
    { 
      path: '/tracks', 
      component: page('site/tracks'),
      children: [
        { path: '', redirect: {name: 'tracks.manager'} },
        { path: 'manager', name: 'tracks.manager', component: page('site/tracks/manager') },
        { path: 'category', name: 'tracks.category', component: page('site/tracks/category') },
        { path: 'session', name: 'tracks.session', component: page('site/tracks/session') },
        { path: 'page', name: 'tracks.page', component: page('site/tracks/page') },
        { path: 'video', name: 'tracks.video', component: page('site/tracks/video') },
      ]
    },
    { path: '/session/:id', component: page('site/session') },
    { path: '/network', component: page('site/chat') },
    // {
    //   path: '/exhibit',
    //   component: page('site/exhibit'),
    //   children: [
    //     { path: '', redirect: {name: 'site.booth'} },
    //     { path: 'booth', name: 'site.booth', component: page('site/exhibit/booth') },
    //     { path: 'sponsor', name: 'site.sponsor', component: page('site/exhibit/sponsor') },
    //     { path: 'poster', name: 'site.poster-hall', component: page('site/exhibit/poster-hall') },
    //     { path: 'poster/:id', name: 'site.poster', component: page('site/exhibit/poster') },
    //   ]
    // },
    { path: '/settings',
      component: page('settings'),
      children: [
        { path: '', redirect: { name: 'settings.profile' } },
        { path: 'profile', name: 'settings.profile', component: page('settings/profile') },
        { path: 'password', name: 'settings.password', component: page('settings/password') }
      ]
    },
    { path: '/exhibit/*', component: page('site/exhibitHall.vue') },
    { path: '*', component: page('site/main.vue') }
  ]
  