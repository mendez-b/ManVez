import { createRouter, createWebHistory } from 'vue-router'
import DetailView from '../views/DetailView.vue'
import HomeView from '../views/HomeView.vue'
import SearchView from '../views/SearchView.vue'
import ReaderView from '../views/ReaderView.vue'

const routes = [
  { path: '/manga/:mangaId', component: DetailView },
  { path: '/', component: HomeView },
  { path: '/search', component: SearchView },
  { path: '/read/:mangaId/:chapterId', component: ReaderView },
  //AQUI SE REGISTRA LA RUTA
  { path: '/login', name: 'Login', component: () => import('../views/LoginView.vue') }
]

const router = createRouter({
  history: createWebHistory(),
  routes
});

//NAVIGATION GUARD: esto verificara si el usuario 
//tiene un token antes de dejarlo entrar a ciertas paginas

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/'];
  const authRequired = !publicPages.includes(to.path);
  const loggedIn = localStorage.getItem('user_token');

  if (authRequired && !loggedIn) {
    return next('/login');
  }
  next();
});

export default router;