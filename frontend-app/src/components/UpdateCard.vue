<template>
  <RouterLink :to="`/manga/${manga.id}`" class="update-card">
    <!-- Portada -->
    <img
      :src="coverUrl"
      :alt="title"
      class="update-cover"
      loading="lazy"
    />

    <!-- Info -->
    <div class="update-info">
      <p class="update-title">{{ title }}</p>

      <div class="chapter-list">
        <div
          v-for="ch in chapters"
          :key="ch.id"
          class="chapter-row"
        >
          <span class="chapter-dot">•</span>
          <RouterLink
            :to="`/manga/${manga.id}/read?chapter=${ch.id}`"
            class="chapter-name"
            @click.stop
          >
            Chapter {{ ch.attributes.chapter || '?' }}
            <span v-if="ch.attributes.title" class="chapter-subtitle"> - {{ truncate(ch.attributes.title, 18) }}</span>
          </RouterLink>
          <span class="chapter-date">{{ timeAgo(ch.attributes.publishAt) }}</span>
        </div>

        <!-- Skeletons si todavía carga -->
        <template v-if="loadingChapters">
          <div v-for="n in 3" :key="n" class="chapter-row chapter-row--skeleton">
            <span class="chapter-dot">•</span>
            <span class="skel skel--name"></span>
            <span class="skel skel--date"></span>
          </div>
        </template>
      </div>
    </div>
  </RouterLink>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  manga: { type: Object, required: true },
  chapters: { type: Array, default: () => [] },
  loadingChapters: { type: Boolean, default: false }
})

const COVERS = 'https://manvez-backend.onrender.com/covers'

const title = computed(() => {
  const t = props.manga.attributes?.title
  const raw = t?.es || t?.en || t?.['ja-ro'] || Object.values(t || {})[0] || 'Sin título'
  return truncate(raw, 40)
})

const coverUrl = computed(() => {
  const rel = props.manga.relationships?.find(r => r.type === 'cover_art')
  const file = rel?.attributes?.fileName
  return file ? `${COVERS}/${props.manga.id}/${file}.256.jpg` : ''
})

function truncate(str, max) {
  return str && str.length > max ? str.slice(0, max) + '…' : str
}

function timeAgo(dateStr) {
  if (!dateStr) return ''
  const diff = Date.now() - new Date(dateStr).getTime()
  const mins  = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days  = Math.floor(diff / 86400000)
  if (mins < 60)  return `${mins}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days === 1) return `1 day ago`
  return `${days} days ago`
}
</script>

<style scoped>
.update-card {
  display: flex;
  gap: 12px;
  padding: 10px;
  border-radius: 8px;
  text-decoration: none;
  color: inherit;
  background: var(--bg-card);
  border: 1px solid var(--border);
  transition: border-color 0.2s, background 0.15s;
  overflow: hidden;
}
.update-card:hover {
  border-color: var(--accent);
  background: color-mix(in srgb, var(--bg-card) 85%, var(--accent) 15%);
}

/* Portada */
.update-cover {
  width: 90px;
  min-width: 90px;
  height: 130px;
  object-fit: cover;
  object-position: center top;
  border-radius: 4px;
  flex-shrink: 0;
}

/* Info derecha */
.update-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
  overflow: hidden;
  flex: 1;
}

.update-title {
  font-weight: 700;
  font-size: 1rem;
  color: var(--text-primary);
  line-height: 1.3;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin: 0;
}

/* Lista de capítulos */
.chapter-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.chapter-row {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.87rem;
  min-width: 0;
}

.chapter-dot {
  color: var(--accent);
  flex-shrink: 0;
  font-size: 0.7rem;
}

.chapter-name {
  color: var(--text-primary);
  text-decoration: none;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
  min-width: 0;
  transition: color 0.15s;
}
.chapter-name:hover {
  color: var(--accent);
  text-decoration: underline;
}

.chapter-subtitle {
  color: var(--text-secondary);
  font-size: 0.75rem;
}

.chapter-date {
  color: var(--text-secondary);
  white-space: nowrap;
  flex-shrink: 0;
  font-size: 0.75rem;
}

/* Skeleton rows */
.chapter-row--skeleton {
  pointer-events: none;
}
.skel {
  display: inline-block;
  height: 10px;
  border-radius: 4px;
  background: linear-gradient(90deg, var(--border) 25%, color-mix(in srgb, var(--border) 60%, transparent) 50%, var(--border) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}
.skel--name { flex: 1; }
.skel--date { width: 48px; }

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>