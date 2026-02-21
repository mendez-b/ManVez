<template>
  <div class="reader">
    <div class="reader-header">
      <RouterLink :to="`/`" class="back-btn">← Volver</RouterLink>
      <select v-model="currentChapter" @change="loadChapter" class="chapter-select">
        <option v-for="ch in chapters" :key="ch.id" :value="ch.id">
          Capítulo {{ ch.attributes.chapter || '?' }} — {{ ch.attributes.title || '' }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="loading">Cargando capítulo...</div>

    <div v-else-if="chapters.length === 0" class="empty">
    Este manga no tiene capítulos disponibles en español o inglés.
</div>
    
    <div v-else class="pages">
      <img 
        v-for="(page, i) in pages" 
        :key="i"
        :src="page"
        :alt="`Página ${i + 1}`"
        loading="lazy"
        class="page-img"
      />
    </div>

    <div class="reader-nav">
      <button @click="prevChapter" :disabled="!hasPrev">← Anterior</button>
      <button @click="nextChapter" :disabled="!hasNext">Siguiente →</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const BASE = 'https://manvez-backend.onrender.com/api/mangadex'
const route = useRoute()
const chapters = ref([])
const currentChapter = ref(null)
const pages = ref([])
const loading = ref(true)

const currentIndex = computed(() => 
  chapters.value.findIndex(c => c.id === currentChapter.value)
)
const hasPrev = computed(() => currentIndex.value > 0)
const hasNext = computed(() => currentIndex.value < chapters.value.length - 1)

async function loadChapters() {
  const res = await axios.get(
    `${BASE}?path=/manga/${route.params.mangaId}/feed&query=limit=100%26translatedLanguage[]=en%26translatedLanguage[]=es%26order[chapter]=desc`
  )
  chapters.value = res.data.data
  if (chapters.value.length > 0) {
    currentChapter.value = chapters.value[0].id
    await loadChapter()
  } else {
    loading.value = false
  }
}
async function loadChapter() {
  loading.value = true
  try {
    const res = await axios.get(
      `${BASE}?path=/at-home/server/${currentChapter.value}&query=`
    )
    const { baseUrl, chapter } = res.data
    
    if (chapter && chapter.data && chapter.data.length > 0) {
      pages.value = chapter.data.map(
        filename => `${baseUrl}/data/${chapter.hash}/${filename}`
      )
    } else if (chapter && chapter.dataSaver && chapter.dataSaver.length > 0) {
      pages.value = chapter.dataSaver.map(
        filename => `${baseUrl}/data-saver/${chapter.hash}/${filename}`
      )
    } else {
      pages.value = []
    }
  } catch (err) {
    console.error(err)
    pages.value = []
  } finally {
    loading.value = false
  }
}

function prevChapter() {
  if (hasPrev.value) {
    currentChapter.value = chapters.value[currentIndex.value - 1].id
    loadChapter()
  }
}

function nextChapter() {
  if (hasNext.value) {
    currentChapter.value = chapters.value[currentIndex.value + 1].id
    loadChapter()
  }
}

onMounted(loadChapters)
</script>

<style scoped>
.reader {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.empty {
  text-align: center;
  padding: 60px;
  color: var(--text-secondary);
  font-size: 1.1rem;
}

.reader-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.back-btn {
  color: var(--accent);
  font-weight: bold;
}

.chapter-select {
  padding: 8px 12px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  color: var(--text-primary);
  border-radius: 6px;
  cursor: pointer;
}

.pages {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.page-img {
  width: 100%;
  border-radius: 4px;
}

.reader-nav {
  display: flex;
  justify-content: space-between;
  margin-top: 24px;
}

.reader-nav button {
  padding: 10px 24px;
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
}

.reader-nav button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.loading {
  text-align: center;
  padding: 60px;
  color: var(--text-secondary);
}
</style>