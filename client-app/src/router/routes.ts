import { RouteConfig } from 'vue-router'

const routes: RouteConfig[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', redirect: 'mes-gardes' },
      { path: 'mes-gardes', component: () => import('pages/MesGardes.vue') },
      { path: 'appels-a-garde', component: () => import('pages/AppelsAGarde.vue') },
      { path: 'mes-dispos', component: () => import('pages/MesDispos.vue') },
      { path: 'planning', component: () => import('pages/Planning.vue') },
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
