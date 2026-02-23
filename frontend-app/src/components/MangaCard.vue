<template>
  <RouterLink :to="`/manga/${manga.id}`" class="card">
    <div class="card-img">
      <img :src="coverUrl" :alt="title" loading="lazy" />
      <div class="card-overlay">
        <span>Ver capítulos</span>
      </div>
    </div>
    <div class="card-info">
      <h3>{{ title }}</h3>
      <p class="chapter">{{ lastChapter }}</p>
    </div>
  </RouterLink>
</template>


<script setup>
import { computed } from 'vue'

const props = defineProps({
  manga: Object
})

const title = computed(() => {
  return props.manga.attributes.title.en ||
         Object.values(props.manga.attributes.title)[0] ||
         'Sin título'
})

const coverUrl = computed(() => {
  const coverRel = props.manga.relationships?.find(r => r.type === 'cover_art')
  if (coverRel?.attributes?.fileName) {
    const mangaId = props.manga.id
    const fileName = coverRel.attributes.fileName
    return `https://og.mangadex.org/og-image/manga/${mangaId}`
  }
  return 'https://placehold.co/256x360/0a0f1e/024F32?text=Sin+Cover'
})
const lastChapter = computed(() => {
  const chapter = props.manga.attributes.lastChapter
  return chapter ? `Capítulo ${chapter}` : 'En curso'
})
</script>

<style scoped>
.card {
  display: block;
  border-radius: 8px;
  overflow: hidden;
  background: var(--bg-card);
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(26, 173, 75, 0.3);
}

.card-img {
  position: relative;
  aspect-ratio: 2/3;
  overflow: hidden;
}

.card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: rgba(38, 192, 115, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
  color: white;
  font-weight: bold;
}

.card:hover .card-overlay {
  opacity: 1;
}

.card-info {
  padding: 10px;
}

.card-info h3 {
  font-size: 0.85rem;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.chapter {
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin-top: 4px;
}
</style>