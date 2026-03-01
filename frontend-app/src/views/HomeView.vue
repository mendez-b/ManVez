<template>
  <div class="home">
    <!-- Carrusel destacado -->
    <HeroCarousel />

    <!-- Sección: Populares hoy -->
    <section class="section">
      <h2 class="section-title">🔥 Populares hoy</h2>
      <div v-if="loadingPopular" class="manga-grid">
        <SkeletonCard v-for="n in 5" :key="n" />
      </div>
      <div v-else class="manga-grid">
        <MangaCard 
          v-for="manga in popularMangas" 
          :key="manga.id" 
          :manga="manga" 
        />
      </div>
    </section>

    <!-- Sección: Últimas actualizaciones -->
    <section class="section">
      <h2 class="section-title">🕐 Últimas actualizaciones</h2>
      <div v-if="loadingRecent" class="manga-grid">
        <SkeletonCard v-for="n in 30" :key="n" />
      </div>
      <div v-else class="manga-grid">
        <MangaCard 
          v-for="manga in recentMangas" 
          :key="manga.id" 
          :manga="manga" 
        />
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import MangaCard from '../components/MangaCard.vue'
import SkeletonCard from '../components/SkeletonCard.vue'
import HeroCarousel from '../components/HeroCarousel.vue'

const BASE = 'https://manvez-backend.onrender.com/api/mangadex'
const popularMangas = ref([])
const recentMangas = ref([])
const loadingPopular = ref(true)
const loadingRecent = ref(true)

onMounted(async () => {
  try {
    const popRes = await axios.get(
      `${BASE}?path=/manga&query=limit=5%26order[followedCount]=desc%26includes[]=cover_art%26contentRating[]=safe`
    )
    popularMangas.value = popRes.data.data
    loadingPopular.value = false

    const recRes = await axios.get(
      `${BASE}?path=/manga&query=limit=30%26order[updatedAt]=desc%26includes[]=cover_art%26contentRating[]=safe`
    )
    recentMangas.value = recRes.data.data
    loadingRecent.value = false
  } catch (error) {
    console.error('Error al cargar mangas:', error)
    loadingPopular.value = false
    loadingRecent.value = false
  }
})
</script>

<style scoped>
.home {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px 20px;
}

.section {
  margin-bottom: 48px;
}

.section-title {
  font-size: 1.3rem;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid var(--accent);
  display: inline-block;
}

.manga-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
}

@media (max-width: 480px) {
  .manga-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
  .section-title {
    font-size: 1.1rem;
  }
}

.loading {
  color: var(--text-secondary);
  text-align: center;
  padding: 40px;
}
</style>