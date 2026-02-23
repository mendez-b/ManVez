import { createRouter, createWebHistory } from 'vue-router'
import DetailView from '../views/DetailView.vue'
import HomeView from '../views/HomeView.vue'
import SearchView from '../views/SearchView.vue'
import ReaderView from '../views/ReaderView.vue'

const routes = [
  { path: '/manga/:mangaId', component: DetailView },
  { path: '/', component: HomeView },
  { path: '/search', component: SearchView },
  { path: '/read/:mangaId/:chapterId', component: ReaderView }
]

export default createRouter({
  history: createWebHistory(),
  routes
})