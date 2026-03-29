<template>
  <div class="favorites-page">
    <h2>Mis Favoritos</h2>

    <div v-if="favorites.length === 0" class="empty-state">
      <p>Aún no has guardado ningún manga en favoritos.</p>
    </div>

    <div v-else class="manga-grid">
      <RouterLink
        v-for="manga in favorites"
        :key="manga.mangaId"
        :to="`/manga/${manga.mangaId}`"
        class="manga-card"
      >
        <img :src="manga.cover" :alt="manga.title" />
        <p>{{ manga.title }}</p>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const favorites = ref([])

onMounted(() => {
  const saved = JSON.parse(localStorage.getItem('manga_lists') || '{}')
  favorites.value = Object.values(saved).filter(m => m.list === 'favorites')
})
</script>

<style scoped>
.favorites-page {
  max-width: 900px;
  margin: 0 auto;
  padding: 24px;
  color: var(--text-primary);
}

.favorites-page h2 {
  font-size: 1.4rem;
  font-weight: 800;
  margin-bottom: 20px;
}

.empty-state {
  color: var(--text-secondary);
  text-align: center;
  padding: 60px 0;
}

.manga-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 16px;
}

.manga-card { text-decoration: none; }

.manga-card img {
  width: 100%;
  border-radius: 8px;
  aspect-ratio: 2/3;
  object-fit: cover;
  transition: transform 0.2s;
}

.manga-card:hover img { transform: scale(1.03); }

.manga-card p {
  font-size: 0.8rem;
  color: var(--text-primary);
  margin: 6px 0 0;
  text-align: center;
}
</style>