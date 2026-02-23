<template>
  <div class="detail-page">
    <div v-if="loading" class="detail-skeleton">
      <div class="sk-cover skeleton-animation"></div>
      <div class="sk-info">
        <div class="sk-title skeleton-animation"></div>
        <div class="sk-line skeleton-animation"></div>
        <div class="sk-line skeleton-animation"></div>
        <div class="sk-line skeleton-animation"></div>
      </div>
    </div>
    <div v-else-if="manga" class="detail-content">
      <div class="detail-hero">
        <img :src="coverUrl" :alt="title" class="detail-cover" />
        <div class="detail-info">
          <h1>{{ title }}</h1>
          <p class="detail-status">Estado: <strong>{{ status }}</strong></p>
          <div class="detail-tags">
            <span v-for="tag in tags" :key="tag" class="tag">{{ tag }}</span>
          </div>
          <p class="detail-description">{{ description }}</p>
          <RouterLink :to="`/read/${mangaId}/first`" class="read-btn">
            📖 Empezar a leer
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const BASE = 'https://manvez-backend.onrender.com/api/mangadex'
const route = useRoute()
const manga = ref(null)
const loading = ref(true)
const mangaId = computed(() => route.params.mangaId)

const title = computed(() => {
  if (!manga.value) return ''
  return manga.value.attributes.title.en ||
         Object.values(manga.value.attributes.title)[0] || 'Sin título'
})

const description = computed(() => {
  if (!manga.value) return ''
  return manga.value.attributes.description?.en ||
         Object.values(manga.value.attributes.description || {})[0] || 'Sin descripción'
})

const status = computed(() => {
  const s = manga.value?.attributes.status
  const map = { ongoing: 'En curso', completed: 'Completado', hiatus: 'En pausa', cancelled: 'Cancelado' }
  return map[s] || s
})

const tags = computed(() => {
  if (!manga.value) return []
  return manga.value.attributes.tags.slice(0, 8).map(t => t.attributes.name.en)
})

const coverUrl = computed(() => {
  if (!manga.value) return ''
  const coverRel = manga.value.relationships?.find(r => r.type === 'cover_art')
  if (coverRel?.attributes?.fileName) {
    return `https://manvez-backend.onrender.com/covers/${mangaId.value}/${coverRel.attributes.fileName}.512.jpg`
  }
  return 'https://placehold.co/256x360/0a0f1e/024F32?text=Sin+Cover'
})

onMounted(async () => {
  try {
    const res = await axios.get(
      `${BASE}?path=/manga/${mangaId.value}&query=includes[]=cover_art`
    )
    manga.value = res.data.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.detail-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px 20px;
}

.detail-hero {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}

.detail-cover {
  width: 250px;
  min-width: 250px;
  border-radius: 10px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}

.detail-info h1 {
  font-size: 2rem;
  margin-bottom: 12px;
}

.detail-status {
  color: var(--text-secondary);
  margin-bottom: 12px;
}

.detail-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
}

.tag {
  padding: 4px 12px;
  background: var(--accent);
  color: white;
  border-radius: 20px;
  font-size: 0.8rem;
}

.detail-description {
  color: var(--text-secondary);
  line-height: 1.7;
  margin-bottom: 24px;
}

.read-btn {
  display: inline-block;
  padding: 12px 28px;
  background: var(--accent);
  color: white;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: bold;
  transition: background 0.2s;
}

.read-btn:hover {
  background: var(--accent-hover);
}

.detail-skeleton {
  display: flex;
  gap: 32px;
}

.sk-cover {
  width: 250px;
  min-width: 250px;
  height: 370px;
  border-radius: 10px;
}

.sk-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sk-title {
  height: 40px;
  border-radius: 6px;
  width: 70%;
}

.sk-line {
  height: 16px;
  border-radius: 4px;
  width: 100%;
}

.skeleton-animation {
  background: linear-gradient(
    90deg,
    var(--bg-secondary) 25%,
    var(--bg-card) 50%,
    var(--bg-secondary) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

@media (max-width: 768px) {
  .detail-hero {
    flex-direction: column;
    align-items: center;
  }
  .detail-cover {
    width: 180px;
    min-width: 180px;
  }
  .detail-skeleton {
    flex-direction: column;
  }
  .sk-cover {
    width: 180px;
    height: 270px;
  }
  .detail-info h1 {
    font-size: 1.4rem;
    text-align: center;
  }
  .detail-tags {
    justify-content: center;
  }
}
</style>
