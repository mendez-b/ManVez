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

const BASE = 'https://manvez-backend.onrender.com/api/mangadex'
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
  { id: 'db9ce55f-8370-4478-9b3b-f3b453a4b4d8', name: 'Slice of Life' },
  { id: '36fd93ea-e8b8-4741-a9a3-2a3c1b8ef2a5', name: 'Misterio' },
  { id: 'ee968100-4191-4968-93d3-f82d72be7e46', name: 'Horror' },
  { id: 'cdad7e68-1419-41d4-b401-16cb21ba7905', name: 'Psicológico' },
  { id: '5ca48985-9a9d-4bd8-be29-80dc0303db72', name: 'Deportes' },
  { id: 'a1f53773-c69a-4ce5-8cab-fffcd90b1565', name: 'Magia' },
  { id: '87cc87cd-a395-47af-b27a-93258283bbc6', name: 'Mecha' },
  { id: 'aafb99c1-7f60-43fa-b75f-fc9502ce29c7', name: 'Harem' },
  { id: '2d1f5d56-a1e5-4d0d-a961-2193588b08ec', name: 'Histórico' },
  { id: 'a3c67850-4684-404e-9b7f-c69850ee5da6', name: 'Girls Love' },
  { id: '423e2eae-a7a2-4a8b-ac03-a8351462d71d', name: 'Boys Love' },
  { id: '9ab53f92-3eed-4e9b-903a-917c86035ee3', name: 'Isekai' },
  { id: 'ace04997-f6bd-436e-b261-779182193d3d', name: 'Tragedia' },
  { id: 'b29d6a3d-1569-4e7a-8cef-cf652e3b5b61', name: 'Wuxia' },
  { id: '4485491f-2b5e-4c11-8fdf-1a35a04b77c9', name: 'Musical' },
  { id: '5fff9cde-849c-4d78-aab0-0d52b2ee1d25', name: 'Crimen' },
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
    let queryParams = `limit=20%26includes[]=cover_art%26contentRating[]=safe`
    if (query.value.trim()) queryParams += `%26title=${encodeURIComponent(query.value)}`
    selectedGenres.value.forEach(g => { queryParams += `%26includedTags[]=${g}` })
    
    const res = await axios.get(`${BASE}?path=/manga&query=${queryParams}`)
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

@media (max-width: 768px) {
  .search-input {
    flex-direction: column;
  }
  .search-input input {
    max-width: 100%;
  }
  .manga-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
}
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