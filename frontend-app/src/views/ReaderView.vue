<template>
  <div class="reader">

    <!-- HEADER -->
    <div class="reader-header">
      <RouterLink :to="`/`" class="back-btn">← Volver</RouterLink>

      <select v-model="currentChapter" @change="onChapterChange" class="chapter-select">
        <option v-for="ch in chapters" :key="ch.id" :value="ch.id">
          Capítulo {{ ch.attributes.chapter || '?' }} — {{ ch.attributes.title || '' }}
        </option>
      </select>

      <!-- Toggle de modo de lectura -->
      <button class="mode-toggle" @click="toggleMode" :title="readingMode === 'vertical' ? 'Cambiar a modo horizontal' : 'Cambiar a modo vertical'">
        <span class="mode-icon">{{ readingMode === 'vertical' ? '↔' : '↕' }}</span>
        <span class="mode-label">{{ readingMode === 'vertical' ? 'Horizontal' : 'Vertical' }}</span>
      </button>
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="loading">Cargando capítulo...</div>

    <!-- SIN PÁGINAS -->
    <div v-else-if="pages.length === 0" class="empty">
      😔 Este capítulo no está disponible. Prueba con otro capítulo.
    </div>

    <!-- SIN CAPÍTULOS -->
    <div v-else-if="chapters.length === 0" class="empty">
      Este manga no tiene capítulos disponibles en español o inglés.
    </div>

    <!-- MODO VERTICAL -->
    <div v-else-if="readingMode === 'vertical'" class="pages pages--vertical">
      <img
        v-for="(page, i) in pages"
        :key="i"
        :src="page"
        :alt="`Página ${i + 1}`"
        loading="lazy"
        class="page-img"
      />
    </div>

    <!-- MODO HORIZONTAL -->
    <div
      v-else
      class="pages pages--horizontal"
      @touchstart="onTouchStart"
      @touchend="onTouchEnd"
    >
      <!-- Contador de página -->
      <div class="page-counter">{{ currentPage + 1 }} / {{ pages.length }}</div>

      <!-- Zona de toque izquierda (página anterior) -->
      <div class="touch-zone touch-zone--left" @click="prevPage"></div>

      <!-- Imagen actual -->
      <img
        :src="pages[currentPage]"
        :alt="`Página ${currentPage + 1}`"
        class="page-img page-img--single"
        :key="currentPage"
      />

      <!-- Zona de toque derecha (página siguiente) -->
      <div class="touch-zone touch-zone--right" @click="nextPage"></div>

      <!-- Flechas de navegación de página -->
      <button class="page-arrow page-arrow--left" @click="prevPage" :disabled="currentPage === 0">‹</button>
      <button class="page-arrow page-arrow--right" @click="nextPage" :disabled="currentPage === pages.length - 1">›</button>
    </div>

    <!-- NAV DE CAPÍTULOS -->
    <div class="reader-nav">
      <button @click="prevChapter" :disabled="!hasPrev">← Anterior</button>
      <button @click="nextChapter" :disabled="!hasNext">Siguiente →</button>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const BASE = 'https://manvez-backend.onrender.com/api/mangadex'
const route = useRoute()

const chapters = ref([])
const currentChapter = ref(null)
const pages = ref([])
const loading = ref(true)
const currentPage = ref(0)

// Persistir el modo de lectura en localStorage
const readingMode = ref(localStorage.getItem('manvez-reading-mode') || 'vertical')

function toggleMode() {
  readingMode.value = readingMode.value === 'vertical' ? 'horizontal' : 'vertical'
  localStorage.setItem('manvez-reading-mode', readingMode.value)
  currentPage.value = 0
}

// ─── Capítulos ───────────────────────────────────────────────────────────────

const currentIndex = computed(() =>
  chapters.value.findIndex(c => c.id === currentChapter.value)
)
const hasPrev = computed(() => currentIndex.value < chapters.value.length - 1) // hay capítulo más antiguo
const hasNext = computed(() => currentIndex.value > 0) // hay capítulo más nuevo

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
  currentPage.value = 0
  try {
    const res = await axios.get(
      `${BASE}?path=/at-home/server/${currentChapter.value}&query=`
    )
    const { baseUrl, chapter } = res.data

    if (chapter?.data?.length > 0) {
      pages.value = chapter.data.map(
        filename => `${baseUrl}/data/${chapter.hash}/${filename}`
      )
    } else if (chapter?.dataSaver?.length > 0) {
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

function onChapterChange() {
  loadChapter()
}

function prevChapter() {
  if (hasNext.value) {
    currentChapter.value = chapters.value[currentIndex.value + 1].id
    loadChapter()
  }
}

function nextChapter() {
  if (hasPrev.value) {
    currentChapter.value = chapters.value[currentIndex.value - 1].id
    loadChapter()
  }
}

// ─── Navegación de páginas (modo horizontal) ─────────────────────────────────

function prevPage() {
  if (currentPage.value > 0) currentPage.value--
}

function nextPage() {
  if (currentPage.value < pages.value.length - 1) currentPage.value++
}

// Teclado
function onKeyDown(e) {
  if (readingMode.value !== 'horizontal') return
  if (e.key === 'ArrowRight' || e.key === 'ArrowDown') nextPage()
  if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   prevPage()
}

// Swipe táctil
let touchStartX = 0
function onTouchStart(e) {
  touchStartX = e.changedTouches[0].clientX
}
function onTouchEnd(e) {
  const diff = touchStartX - e.changedTouches[0].clientX
  if (Math.abs(diff) > 50) {
    diff > 0 ? nextPage() : prevPage()
  }
}

onMounted(() => {
  loadChapters()
  window.addEventListener('keydown', onKeyDown)
})
onUnmounted(() => {
  window.removeEventListener('keydown', onKeyDown)
})
</script>

<style scoped>

/* ── Layout general ─────────────────────────────────────────── */
.reader {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

/* ── Header ─────────────────────────────────────────────────── */
.reader-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.back-btn {
  color: var(--accent);
  font-weight: bold;
  white-space: nowrap;
}

.chapter-select {
  flex: 1;
  min-width: 0;
  padding: 8px 12px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  color: var(--text-primary);
  border-radius: 6px;
  cursor: pointer;
}

/* ── Toggle de modo ─────────────────────────────────────────── */
.mode-toggle {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  color: var(--text-primary);
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  white-space: nowrap;
  transition: background 0.2s, border-color 0.2s;
}
.mode-toggle:hover {
  border-color: var(--accent);
  color: var(--accent);
}
.mode-icon {
  font-size: 1.1rem;
  line-height: 1;
}

/* ── Estados vacío / cargando ───────────────────────────────── */
.loading,
.empty {
  text-align: center;
  padding: 60px;
  color: var(--text-secondary);
  font-size: 1.1rem;
}

/* ── Modo VERTICAL ──────────────────────────────────────────── */
.pages--vertical {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

/* ── Modo HORIZONTAL ────────────────────────────────────────── */
.pages--horizontal {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  user-select: none;
}

.page-counter {
  position: absolute;
  top: -32px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.85rem;
  color: var(--text-secondary);
  background: var(--bg-card);
  border: 1px solid var(--border);
  padding: 2px 12px;
  border-radius: 20px;
}

/* Imagen en modo horizontal: ocupa el ancho disponible */
.page-img--single {
  width: 100%;
  max-height: 85vh;
  object-fit: contain;
  border-radius: 4px;
  animation: fadeIn 0.15s ease;
}

@keyframes fadeIn {
  from { opacity: 0.4; }
  to   { opacity: 1; }
}

/* Zonas táctiles invisibles (izquierda / derecha) */
.touch-zone {
  position: absolute;
  top: 0;
  bottom: 0;
  width: 30%;
  z-index: 2;
  cursor: pointer;
}
.touch-zone--left  { left: 0; }
.touch-zone--right { right: 0; }

/* Flechas flotantes */
.page-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 3;
  background: var(--bg-card);
  border: 1px solid var(--border);
  color: var(--text-primary);
  border-radius: 50%;
  width: 40px;
  height: 40px;
  font-size: 1.6rem;
  line-height: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.7;
  transition: opacity 0.2s;
}
.page-arrow:hover:not(:disabled) { opacity: 1; color: var(--accent); border-color: var(--accent); }
.page-arrow:disabled { opacity: 0.2; cursor: not-allowed; }
.page-arrow--left  { left: 8px; }
.page-arrow--right { right: 8px; }

/* ── Imagen general (modo vertical) ────────────────────────── */
.page-img {
  width: 100%;
  border-radius: 4px;
  display: block;
}

/* ── Nav de capítulos ───────────────────────────────────────── */
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

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 768px) {
  .reader-header {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }
  .chapter-select,
  .mode-toggle {
    width: 100%;
    justify-content: center;
  }
  .reader-nav button {
    padding: 8px 16px;
    font-size: 0.9rem;
  }
  .page-arrow {
    display: none; /* En móvil se usa swipe/zonas táctiles */
  }
}
</style>