import Layout from '@/layout'

const broadcastRouter = {
  name: 'Broadcast',
  path: '/broadcast',
  component: Layout,
  redirect: '/broadcast/index',
  meta: {
    title: 'broadcast-manage',
    roles: ['admin'],
    icon: 'message'
  },
  children: [
    {
      name: 'broadcast-list',
      path: 'index',
      component: () => import('@/views/broadcast/list'),
      meta: {
        title: 'broadcast-list',
        roles: ['admin']
      }
    },
    {
      name: 'broadcast-create',
      path: 'create',
      component: () => import('@/views/broadcast/create'),
      meta: {
        title: 'broadcast-create',
        roles: ['admin']
      }
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/broadcast/edit'),
      name: 'broadcast-edit',
      hidden: true,
      meta: {
        title: 'broadcast-edit',
        noCache: true,
        roles: ['admin']
      }
    },
    {
      name: 'broadcast-show',
      path: 'show/:id',
      component: () => import('@/views/broadcast/create'),
      hidden: true,
      meta: {
        title: 'broadcast-show',
        roles: ['admin']
      }
    }
  ]
}

export default broadcastRouter
