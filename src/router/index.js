//Acuerdate de importar las vistas cuando quieras hacer nuevas ;)

import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import AdminView from '../views/AdminView.vue'
import RutasView from '../views/RutasView.vue'
import RutaView from '../views/RutaView.vue'
import CrearRuta from '../views/CrearRuta.vue'
import ReservaView from '../views/ReservaView.vue'
import GuiaView from '../views/GuiaView.vue'
import AcercaView from '@/views/AcercaView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/login',
      name: 'login',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: LoginView
    },{
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/admin',
      name: 'admin',
      component: AdminView,
    }, {
      path: '/rutas',
      name: 'rutas',
      component: RutasView,
    },
    {
      path: '/ruta/:id',
      name: 'ruta',
      component: RutaView,
    }, 
    {
      path: '/ruta/crear',
      name: 'crearRuta',
      component: CrearRuta,
    }, 
    {
      path: '/reservas',
      name: 'reservas',
      component: ReservaView,
    }, 
    {
      path: '/guia',
      name: 'guia',
      component: GuiaView,
    }, 
    {
      path: '/acerca',
      name: 'acerca',
      component: AcercaView,
    }, 
  ],
})

export default router
