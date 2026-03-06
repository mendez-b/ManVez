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

<<<<<<< HEAD
const BASE = '/api/mangadex'
=======
const BASE = 'http://localhost:8080/api/mangadex'
>>>>>>> a7cbc4b0b2ef03fe389dd30dc001794b05f5f2c1
const popularMangas = ref([])
const recentMangas = ref([])
const loadingPopular = ref(true)
const loadingRecent = ref(true)

onMounted(async () => {
  try {
    
    const popRes = await axios.get(
      `${BASE}/manga?limit=5&order[followedCount]=desc&includes[]=cover_art&contentRating[]=safe`
    )
    popularMangas.value = popRes.data.data
    loadingPopular.value = false

    const recRes = await axios.get(
      `${BASE}/manga?limit=30&order[updatedAt]=desc&includes[]=cover_art&contentRating[]=safe`
    )
    recentMangas.value = recRes.data.data
    loadingRecent.value = false

  } catch (error) {
    console.error('Error al cargar mangas:', error)
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