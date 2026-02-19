import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import SearchView from '../views/SearchView.vue'
import ReaderView from '../views/ReaderView.vue'

const routes = [
  { path: '/', component: HomeView },
  { path: '/search', component: SearchView },
  { path: '/read/:mangaId/:chapterId', component: ReaderView }
]

export default createRouter({
  history: createWebHistory(),
  routes
})