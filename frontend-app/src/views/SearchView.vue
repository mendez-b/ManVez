<template>
  <div class="search-page">
    <div class="filters">
      <h2>Buscar Manga</h2>
      
      <!-- Input de búsqueda -->
      <div class="search-input">
        <input 
          v-model="query"
          @keyup.enter="search"
          placeholder="Nombre del manga..."
          type="text"
        />
        <button @click="search">Buscar</button>
      </div>

      <!-- Filtro por géneros -->
      <div class="genre-filter">
        <h3>Géneros</h3>
        <div class="genre-tags">
          <button 
            v-for="genre in genres" 
            :key="genre.id"
            :class="['tag', { active: selectedGenres.includes(genre.id) }]"
            @click="toggleGenre(genre.id)"
          >
            {{ genre.name }}
          </button>
        </div>
      </div>
    </div>

    <!-- Resultados -->
    <div v-if="loading" class="loading">Buscando...</div>
    <div v-else-if="results.length === 0 && searched" class="empty">
      No se encontraron resultados.
    </div>
    <div v-else class="manga-grid">
      <MangaCard 
        v-for="manga in results" 
        :key="manga.id" 
        :manga="manga" 
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import MangaCard from '../components/MangaCard.vue'

const route = useRoute()
const query = ref('')
const results = ref([])
const loading = ref(false)
const searched = ref(false)
const selectedGenres = ref([])

// Géneros más comunes de MangaDex (IDs reales)
const genres = [
  { id: '391b0423-d847-456f-aff0-8b0cfc03066b', name: 'Acción' },
  { id: 'e5301a23-ebd9-49dd-a0cb-2add944c7fe9', name: 'Aventura' },
  { id: '4d32cc48-9f00-4cca-9b5a-a839f0764984', name: 'Comedia' },
  { id: 'b9af3a63-f058-46de-a9a0-e0c13906197a', name: 'Drama' },
  { id: 'cdc58593-87dd-415e-bbc0-2ec27bf404cc', name: 'Fantasía' },
  { id: 'f8f62932-27da-4fe4-8ee1-6779a8c5edba', name: 'Romance' },
  { id: '07251805-a27e-4d59-b488-f0bfbec15168', name: 'Sobrenatural' },
  { id: '256c8bd9-4904-4360-bf4f-508a76d67183', name: 'Sci-Fi' },
]

function toggleGenre(id) {
  const idx = selectedGenres.value.indexOf(id)
  if (idx === -1) selectedGenres.value.push(id)
  else selectedGenres.value.splice(idx, 1)
}

async function search() {
  loading.value = true
  searched.value = true
  try {
    let url = `https://manvez-backend.onrender.com/api/mangadex/manga?limit=20&includes[]=cover_art&contentRating[]=safe`
    if (query.value.trim()) url += `&title=${encodeURIComponent(query.value)}`
    selectedGenres.value.forEach(g => { url += `&includedTags[]=${g}` })
    
    const res = await axios.get(url)
    results.value = res.data.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

// Si llegó desde la barra de búsqueda de la navbar
onMounted(() => {
  if (route.query.q) {
    query.value = route.query.q
    search()
  }
})

// Si cambia el query de la URL
watch(() => route.query.q, (val) => {
  if (val) { query.value = val; search() }
})
</script>

<style scoped>
.search-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px 20px;
}

.filters {
  margin-bottom: 32px;
}

.filters h2 {
  font-size: 1.5rem;
  margin-bottom: 16px;
}

.search-input {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
}

.search-input input {
  flex: 1;
  max-width: 400px;
  padding: 10px 14px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 6px;
  color: var(--text-primary);
  font-size: 1rem;
  outline: none;
}

.search-input button {
  padding: 10px 20px;
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
}

.search-input button:hover {
  background: var(--accent-hover);
}

.genre-filter h3 {
  margin-bottom: 12px;
  color: var(--text-secondary);
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.genre-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.tag {
  padding: 6px 14px;
  border-radius: 20px;
  border: 1px solid var(--border);
  background: var(--bg-card);
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.85rem;
}

.tag:hover, .tag.active {
  background: var(--accent);
  color: white;
  border-color: var(--accent);
}

.manga-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 16px;
}

.loading, .empty {
  text-align: center;
  padding: 60px;
  color: var(--text-secondary);
}
</style>