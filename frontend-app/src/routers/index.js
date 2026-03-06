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
  { path: '/login', name: 'Login', component: () => import('../views/LoginView.vue') },
  { path: '/register', name: 'Register', component: () => import('../views/RegisterView.vue') },
  { path: '/forgot-password', name: 'ForgotPassword', component: () => import('../views/ForgotPasswordView.vue') },
  { path: '/reset-password', name: 'ResetPassword', component: () => import('../views/ResetPasswordView.vue') },
  { path: '/favorites', name: 'Favorites', component: () => import('../views/FavoritesView.vue') },
  { path: '/profile', name: 'Profile', component: () => import('../views/ProfileView.vue') },
  { path: '/profile/edit', name: 'EditProfile', component: () => import('../views/EditProfileView.vue') }
]

  const router = createRouter({
  history: createWebHistory(),
  routes
});

//NAVIGATION GUARD: esto verificara si el usuario 
//tiene un token antes de dejarlo entrar a ciertas paginas

router.beforeEach((to, from, next) => {
  // Hacer pública la página de inicio, las páginas de autenticación y las de visualización de mangas
  const publicPages = ['/', '/login', '/register', '/forgot-password', '/reset-password'];
  const isPublicPath = publicPages.includes(to.path) || 
                       to.path.startsWith('/manga/') ||
                       to.path.startsWith('/search') ||
                       to.path.startsWith('/read/');
  
  const authRequired = !isPublicPath;
  const loggedIn = localStorage.getItem('user_token');

  if (authRequired && !loggedIn) {
    return next('/login');
  }
  next();
});

export default router;