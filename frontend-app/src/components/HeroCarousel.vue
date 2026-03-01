<template>
  <div class="carousel" @mouseenter="pause" @mouseleave="resume">

    <!-- Slides -->
    <div class="carousel-track" :style="{ transform: `translateX(-${current * 100}%)` }">
      <div
        v-for="(manga, i) in mangas"
        :key="manga.id"
        class="carousel-slide"
        :style="{ backgroundImage: `url(${getBg(manga)})` }"
      >
        <!-- Overlay gradiente -->
        <div class="slide-overlay"></div>

        <!-- Contenido -->
        <div class="slide-content">
          <div class="slide-left">
            <!-- Badge tipo -->
            <span class="badge">{{ getType(manga) }}</span>

            <h2 class="slide-title">{{ getTitle(manga) }}</h2>

            <p class="slide-genres">{{ getGenres(manga) }}</p>

            <div class="slide-summary-label">SUMMARY</div>
            <p class="slide-summary">{{ getSummary(manga) }}</p>

            <p class="slide-status">
              <strong>Status:</strong> {{ getStatus(manga) }}
            </p>


          </div>

          <!-- Portada -->
          <RouterLink :to="`/manga/${manga.id}`" class="slide-cover-link">
            <img :src="getCover(manga)" :alt="getTitle(manga)" class="slide-cover" />
          </RouterLink>
        </div>
      </div>
    </div>

<!-- Dots -->
    <div class="dots">
      <button
        v-for="(_, i) in mangas"
        :key="i"
        class="dot"
        :class="{ 'dot--active': i === current }"
        @click="goTo(i)"
      ></button>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="carousel-skeleton">
      <div class="sk-bg"></div>
      <div class="sk-content">
        <div class="sk-line sk-line--sm"></div>
        <div class="sk-line sk-line--lg"></div>
        <div class="sk-line sk-line--md"></div>
        <div class="sk-line sk-line--md"></div>
      </div>
      <div class="sk-cover"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const BASE   = 'https://manvez-backend.onrender.com/api/mangadex'
const COVERS = 'https://manvez-backend.onrender.com/covers'

const mangas  = ref([])
const loading = ref(true)
const current = ref(0)
let timer = null

async function load() {
  try {
    const res = await axios.get(
      `${BASE}?path=/manga&query=limit=10%26order[followedCount]=desc%26includes[]=cover_art%26contentRating[]=safe`
    )
    mangas.value = res.data.data
  } catch (e) {
    console.error('Error carrusel:', e)
  } finally {
    loading.value = false
    startAuto()
  }
}

function startAuto() {
  timer = setInterval(() => {
    current.value = (current.value + 1) % mangas.value.length
  }, 5000)
}
function pause()  { clearInterval(timer) }
function resume() { startAuto() }
function next()   { current.value = (current.value + 1) % mangas.value.length }
function prev()   { current.value = (current.value - 1 + mangas.value.length) % mangas.value.length }
function goTo(i)  { current.value = i }

// Helpers
function getTitle(m) {
  const t = m.attributes?.title
  const raw = t?.en || t?.['ja-ro'] || Object.values(t || {})[0] || 'Sin título'
  return raw.length > 40 ? raw.slice(0, 40) + '…' : raw
}

function getCover(m) {
  const rel = m.relationships?.find(r => r.type === 'cover_art')
  const file = rel?.attributes?.fileName
  return file ? `${COVERS}/${m.id}/${file}.512.jpg` : ''
}

function getBg(m) {
  // Usa la misma portada como fondo borroso
  return getCover(m)
}

function getGenres(m) {
  return (m.attributes?.tags || [])
    .filter(t => t.attributes?.group === 'genre')
    .map(t => t.attributes?.name?.en)
    .filter(Boolean)
    .slice(0, 6)
    .join(', ')
}

function getSummary(m) {
  const desc = m.attributes?.description
  const raw  = desc?.en || desc?.es || Object.values(desc || {})[0] || ''
  return raw.length > 160 ? raw.slice(0, 160) + ' …' : raw
}

function getStatus(m) {
  const map = { ongoing: 'Ongoing', completed: 'Completed', hiatus: 'Hiatus', cancelled: 'Cancelled' }
  return map[m.attributes?.status] || m.attributes?.status || '—'
}

function getType(m) {
  const pub = m.attributes?.publicationDemographic
  const fmt = m.attributes?.originalLanguage
  if (fmt === 'ko') return 'MANHWA'
  if (fmt === 'zh' || fmt === 'zh-hk') return 'MANHUA'
  return 'MANGA'
}

onMounted(load)
onUnmounted(pause)
</script>

<style scoped>
/* ── Contenedor ─────────────────────────────────────────────── */
.carousel {
  position: relative;
  width: 100%;
  height: 260px;
  overflow: hidden;
  border-radius: 12px;
  margin-bottom: 40px;
  background: var(--bg-card);
}

/* ── Track (slider horizontal) ──────────────────────────────── */
.carousel-track {
  display: flex;
  height: 100%;
  transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform;
}

/* ── Cada slide ─────────────────────────────────────────────── */
.carousel-slide {
  min-width: 100%;
  height: 100%;
  position: relative;
  background-size: cover;
  background-position: center;
  flex-shrink: 0;
}

/* Fondo borroso con la portada */
.carousel-slide::before {
  content: '';
  position: absolute;
  inset: 0;
  background: inherit;
  background-size: cover;
  background-position: center;
  filter: blur(18px) brightness(0.35) saturate(1.4);
  transform: scale(1.08);
  z-index: 0;
}

/* Gradiente encima del blur */
.slide-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    90deg,
    rgba(0,0,0,0.75) 0%,
    rgba(0,0,0,0.45) 55%,
    rgba(0,0,0,0.15) 100%
  );
  z-index: 1;
}

/* ── Contenido del slide ────────────────────────────────────── */
.slide-content {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
  padding: 20px 24px;
  gap: 16px;
}

.slide-left {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

/* Badge */
.badge {
  display: inline-block;
  background: var(--accent);
  color: white;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  padding: 2px 8px;
  border-radius: 3px;
  width: fit-content;
  margin-bottom: 2px;
}

.slide-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  text-shadow: 0 2px 8px rgba(0,0,0,0.6);
}

.slide-genres {
  font-size: 0.75rem;
  color: rgba(255,255,255,0.75);
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.slide-summary-label {
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: var(--accent);
  margin-top: 4px;
}

.slide-summary {
  font-size: 0.78rem;
  color: rgba(255,255,255,0.8);
  line-height: 1.5;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.slide-status {
  font-size: 0.78rem;
  color: rgba(255,255,255,0.7);
  margin: 0;
}
.slide-status strong {
  color: #fff;
}



/* ── Portada ────────────────────────────────────────────────── */
.slide-cover-link {
  flex-shrink: 0;
}
.slide-cover {
  height: 210px;
  width: auto;
  border-radius: 6px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.6);
  object-fit: cover;
  display: block;
  transition: transform 0.3s;
}
.slide-cover:hover { transform: scale(1.03); }



/* ── Dots ───────────────────────────────────────────────────── */
.dots {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
  z-index: 10;
}
.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(255,255,255,0.35);
  border: none;
  padding: 0;
  cursor: pointer;
  transition: background 0.25s, transform 0.25s;
}
.dot--active {
  background: var(--accent);
  transform: scale(1.35);
}

/* ── Skeleton ───────────────────────────────────────────────── */
.carousel-skeleton {
  position: absolute;
  inset: 0;
  z-index: 20;
  background: var(--bg-card);
  display: flex;
  align-items: center;
  padding: 20px 24px;
  gap: 16px;
}
.sk-bg {
  position: absolute;
  inset: 0;
  background: var(--bg-card);
}
.sk-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 1;
}
.sk-cover {
  width: 140px;
  height: 210px;
  border-radius: 6px;
  background: linear-gradient(90deg, var(--border) 25%, color-mix(in srgb, var(--border) 60%, transparent) 50%, var(--border) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
  z-index: 1;
  flex-shrink: 0;
}
.sk-line {
  height: 12px;
  border-radius: 4px;
  background: linear-gradient(90deg, var(--border) 25%, color-mix(in srgb, var(--border) 60%, transparent) 50%, var(--border) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}
.sk-line--sm { width: 60px;  height: 18px; }
.sk-line--lg { width: 80%;   height: 22px; }
.sk-line--md { width: 90%; }

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 600px) {
  .carousel { height: 200px; }
  .slide-cover { height: 150px; }
  .slide-title { font-size: 1rem; }
  .slide-summary { display: none; }
  .slide-genres { display: none; }
  .slide-btn { display: none; }
  .arrow { display: none; }
}
</style>