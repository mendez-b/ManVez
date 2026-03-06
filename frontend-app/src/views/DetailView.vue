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

          <!-- Botones de acción -->
          <div class="action-buttons">
            <RouterLink :to="`/read/${mangaId}/first`" class="read-btn">
              <BookOpen :size="18" />
              Empezar a leer
            </RouterLink>

            <!-- Botón lista desplegable -->
            <div class="list-menu" ref="listMenuRef">
              <button class="list-btn" :class="{ 'list-btn--active': currentList }" @click="toggleListMenu">
                <component :is="currentListIcon" :size="18" />
                {{ currentListLabel }}
                <ChevronDown :size="14" />
              </button>

              <div v-if="listMenuOpen" class="list-dropdown">
                <button
                  v-for="opt in listOptions"
                  :key="opt.key"
                  class="list-option"
                  :class="{ 'list-option--active': currentList === opt.key }"
                  @click="setList(opt.key)"
                >
                  <component :is="opt.icon" :size="16" />
                  {{ opt.label }}
                </button>
                <div v-if="currentList" class="list-divider"></div>
                <button v-if="currentList" class="list-option list-option--remove" @click="setList(null)">
                  <X :size="16" />
                  Quitar de la lista
                </button>
              </div>
            </div>
          </div>

          <!-- Indicador de lista actual -->
          <p v-if="currentList" class="list-indicator">
            <component :is="currentListIcon" :size="14" />
            Guardado en: <strong>{{ currentListLabel }}</strong>
          </p>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { BookOpen, Heart, Clock, CheckCircle, XCircle, ChevronDown, X, BookMarked } from 'lucide-vue-next'

const BASE = '/api/mangadex'

const route      = useRoute()
const manga      = ref(null)
const loading    = ref(true)
const listMenuOpen = ref(false)
const listMenuRef  = ref(null)
const currentList  = ref(null)

const mangaId = computed(() => route.params.mangaId)

const listOptions = [
  { key: 'favorites',  label: 'Favoritos',    icon: Heart },
  { key: 'reading',    label: 'Leyendo',       icon: BookOpen },
  { key: 'completed',  label: 'Completado',    icon: CheckCircle },
  { key: 'abandoned',  label: 'Abandonado',    icon: XCircle },
  { key: 'pending',    label: 'Pendiente',     icon: Clock },
]

const currentListLabel = computed(() => {
  if (!currentList.value) return 'Agregar a lista'
  return listOptions.find(o => o.key === currentList.value)?.label || 'Agregar a lista'
})

const currentListIcon = computed(() => {
  if (!currentList.value) return BookMarked
  return listOptions.find(o => o.key === currentList.value)?.icon || BookMarked
})

function toggleListMenu() {
  listMenuOpen.value = !listMenuOpen.value
}

function setList(key) {
  currentList.value = key
  listMenuOpen.value = false

  // Guardar en localStorage por ahora
  const saved = JSON.parse(localStorage.getItem('manga_lists') || '{}')
  if (key) {
    saved[mangaId.value] = {
      list: key,
      title: title.value,
      cover: coverUrl.value,
      mangaId: mangaId.value
    }
  } else {
    delete saved[mangaId.value]
  }
  localStorage.setItem('manga_lists', JSON.stringify(saved))
}

function handleClickOutside(e) {
  if (listMenuRef.value && !listMenuRef.value.contains(e.target)) {
    listMenuOpen.value = false
  }
}

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
    return `https://uploads.mangadex.org/covers/${mangaId.value}/${coverRel.attributes.fileName}.512.jpg`
  }
  return 'https://placehold.co/256x360/0a0f1e/024F32?text=Sin+Cover'
})

onMounted(async () => {
  try {
    const res = await axios.get(`${BASE}/manga/${mangaId.value}?includes[]=cover_art`)
    manga.value = res.data.data

    // Cargar lista guardada
    const saved = JSON.parse(localStorage.getItem('manga_lists') || '{}')
    if (saved[mangaId.value]) {
      currentList.value = saved[mangaId.value].list
    }
  } catch (err) {
    console.error('Error al obtener detalles:', err)
  } finally {
    loading.value = false
  }

  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
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

/* ── Botones de acción ──────────────────────────────────────── */
.action-buttons {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  align-items: center;
  margin-bottom: 12px;
}

.read-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: var(--accent);
  color: white;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: bold;
  text-decoration: none;
  transition: opacity 0.2s;
}

.read-btn:hover {
  opacity: 0.88;
}

/* ── Lista desplegable ──────────────────────────────────────── */
.list-menu {
  position: relative;
}

.list-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 18px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-primary);
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: border-color 0.2s;
}

.list-btn:hover {
  border-color: var(--accent);
}

.list-btn--active {
  border-color: var(--accent);
  color: var(--accent);
}

.list-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 10px;
  min-width: 190px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.3);
  overflow: hidden;
  animation: fadeDown 0.15s ease;
  z-index: 50;
}

@keyframes fadeDown {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}

.list-option {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 16px;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  color: var(--text-primary);
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.15s;
}

.list-option:hover {
  background: color-mix(in srgb, var(--accent) 10%, transparent);
}

.list-option--active {
  color: var(--accent);
  background: color-mix(in srgb, var(--accent) 12%, transparent);
}

.list-option--remove {
  color: #e74c3c;
}

.list-option--remove:hover {
  background: rgba(231, 76, 60, 0.1);
}

.list-divider {
  height: 1px;
  background: var(--border);
  margin: 4px 0;
}

.list-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  color: var(--accent);
  margin: 0;
}

/* ── Skeleton ───────────────────────────────────────────────── */
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

/* ── Responsive ─────────────────────────────────────────────── */
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
  .action-buttons {
    justify-content: center;
  }
}
</style>