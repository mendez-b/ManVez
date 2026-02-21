<template>
  <div class="home">
    <!-- Sección: Populares hoy -->
    <section class="section">
      <h2 class="section-title">🔥 Populares hoy</h2>
      <div v-if="loadingPopular" class="loading">Cargando...</div>
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
      <div v-if="loadingRecent" class="loading">Cargando...</div>
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

const popularMangas = ref([])
const recentMangas = ref([])
const loadingPopular = ref(true)
const loadingRecent = ref(true)

const BASE = 'https://manvez-backend.onrender.com/api/mangadex'
const INCLUDES = 'includes[]=cover_art'

onMounted(async () => {
  try {
    // Mangas populares (ordenados por seguidos)
    const popRes = await axios.get(
      `${BASE}/manga?limit=12&order[followedCount]=desc&${INCLUDES}&contentRating[]=safe`
    )
    popularMangas.value = popRes.data.data
    loadingPopular.value = false

    // Mangas actualizados recientemente
    const recRes = await axios.get(
      `${BASE}/manga?limit=12&order[updatedAt]=desc&${INCLUDES}&contentRating[]=safe`
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
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 16px;
}

.loading {
  color: var(--text-secondary);
  text-align: center;
  padding: 40px;
}
</style>