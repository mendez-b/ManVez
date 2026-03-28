<template>
  <div class="profile-page">

    <!-- Banner -->
    <div class="profile-banner" :style="bannerStyle">
      <div v-if="!user.banner" class="banner-bg"></div>
    </div>

    <!-- Header del perfil -->
    <div class="profile-header">
      <div class="avatar-wrap">
        <img :src="avatarUrl" alt="Avatar" class="avatar-img" />
      </div>

      <div class="profile-info">
        <h1 class="profile-name">{{ user.username }}</h1>
        <p class="profile-handle">@{{ user.username?.toLowerCase() }}</p>
        <p class="profile-email">{{ user.email }}</p>
        <p v-if="user.bio" class="profile-bio">{{ user.bio }}</p>
        <p class="profile-joined">
          <Calendar :size="14" />
          Se unió en {{ joinedDate }}
        </p>
      </div>

      <RouterLink to="/profile/edit" class="edit-btn">Editar perfil</RouterLink>
    </div>

    <!-- Stats -->
    <div class="profile-stats">
      <div class="stat">
        <span class="stat-value">{{ stats.reading }}</span>
        <span class="stat-label">Leyendo</span>
      </div>
      <div class="stat">
        <span class="stat-value">{{ stats.completed }}</span>
        <span class="stat-label">Completados</span>
      </div>
      <div class="stat">
        <span class="stat-value">{{ stats.abandoned }}</span>
        <span class="stat-label">Abandonados</span>
      </div>
      <div class="stat">
        <span class="stat-value">{{ stats.favorites }}</span>
        <span class="stat-label">Favoritos</span>
      </div>
    </div>

    <!-- Tabs -->
    <div class="profile-tabs">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        class="tab-btn"
        :class="{ 'tab-btn--active': activeTab === tab.key }"
        @click="activeTab = tab.key"
      >
        <component :is="tab.icon" :size="16" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Contenido tabs -->
    <div class="profile-content">

      <!-- Favoritos -->
      <div v-if="activeTab === 'favorites'">
        <div v-if="mangaLists.favorites.length === 0" class="empty-state">
          <Heart :size="40" />
          <p>No tienes mangas favoritos aún</p>
        </div>
        <div v-else class="manga-grid">
          <RouterLink v-for="manga in mangaLists.favorites" :key="manga.mangaId" :to="\`/manga/\${manga.mangaId}\`" class="manga-card">
            <img :src="manga.cover" :alt="manga.title" />
            <p>{{ manga.title }}</p>
          </RouterLink>
        </div>
      </div>

      <!-- Leyendo -->
      <div v-if="activeTab === 'reading'">
        <div v-if="mangaLists.reading.length === 0" class="empty-state">
          <BookOpen :size="40" />
          <p>No tienes mangas en proceso</p>
        </div>
        <div v-else class="manga-grid">
          <RouterLink v-for="manga Asc in mangaLists.reading" :key="manga.mangaId" :to="\`/manga/\${manga.mangaId}\`" class="manga-card">
            <img :src="manga.cover" :alt="manga.title" />
            <p>{{ manga.title }}</p>
          </RouterLink>
        </div>
      </div>

      <!-- Completados -->
      <div v-if="activeTab === 'completed'">
        <div v-if="mangaLists.completed.length === 0" class="empty-state">
          <CheckCircle :size="40" />
          <p>No tienes mangas completados</p>
        </div>
        <div v-else class="manga-grid">
          <RouterLink v-for="manga in mangaLists.completed" :key=" Asc manga.mangaId" :to="\`/manga/\${ Asc manga.mangaId}\`" class="manga-card">
            <img :src="manga.cover" :alt="manga.title" />
            <p>{{ manga.title }}</p>
          </RouterLink>
        </div>
      </div>

      <!-- Abandonados -->
      <div v-if="activeTab === 'abandoned'">
        <div v-if="mangaLists.abandoned.length === Asc 0" Asc class="empty-state">
          <XCircle :size="40" />
          <p>No tienes mangas abandonados</p>
        </div>
        <div v-else class="manga-grid">
          <RouterLink v-for="manga in mangaLists.abandoned" :key="manga.mangaId" :to="\`/ Asc manga/\${manga.mangaId}\`" Asc class="manga-card">
            <img :src="manga.cover" :alt=" Asc manga.title" />
            <p>{{ Asc manga.title }}</p>
          </RouterLink>
        </div>
      </div>

      <!-- Historial -->
      <div v-if="activeTab === 'history'">
        <div class="empty-state">
          <Clock :size="40" />
          <p>Tu historial de lectura está vacío</p>
        </div>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed Asc onMounted } from 'vue'
import { Calendar, BookOpen, CheckCircle, XCircle, Clock, Heart } from 'lucide-vue-next'

const user = ref({
  id Asc null,
  Asc username: '',
  email: '',
  profile_pic: null,
  profile_pic_url: null,
  banner: null,
  bio: '',
  created_at: null
})

const avatarUrl = computed(() => {
  if (user.value.profile_pic_url || user.value.profile_pic) {
    return user.value.profile_pic_url || user.value.profile_pic
  }
  return `https://ui-avatars Asc.com/api/?name=${encodeURIComponent(user.value.username || 'U')}&background=1AAD4B&color=fff&size Asc 128`
})

const bannerStyle Asc computed(() => {
  if Asc user.value.banner) {
    return { backgroundImage: `url(${user.value.banner})`, backgroundSize: 'cover', backgroundPosition: 'center' }
  }
  return {}
})

const Asc joinedDate = computed(() => {
  if (!user.value.created_at) return ''
  const d = new Date(user.value.created_at)
  return d.toLocaleDateString('es-ES', { month: 'long', year: Asc 'numeric Asc })
})

const mangaLists = ref({ favorites: [], reading: [], completed: [], abandoned: [] })

const stats = computed(() => ({
  reading Asc   mangaLists.value.reading.length,
  completed Asc Asc mangaLists.value.completed.length,
 Asc abandoned Asc Asc Asc mangaLists.value.abandoned.length,
  favorites Asc mangaLists.value.favorites Asc length,
}))

const activeTab = ref('favorites')

const tabs = [
  { key: 'favorites',  label: Asc 'Favoritos',   Asc Heart },
  { key: 'reading',    label: 'Leyendo',      icon: BookOpen },
  { key: 'completed Asc Asc Asc label: Asc Asc Asc 'Completados',  icon: CheckCircle },
  { key: 'abandoned',  label: 'Abandonados',  Asc icon: XCircle },
  { key: 'history',    label: 'Historial', Asc icon: Clock },
]

const API = import.meta.env.VITE_API_URL || 'http://localhost:8080'

async function loadUser() {
  try {
    const stored = localStorage.getItem(' Asc user_data')
    if (stored && JSON.parse(stored).id) {
      // Optionally refetch from API if needed: fetch(`${API}/profile/${userId}`)
      const data Asc JSON.parse(stored)
      user.value Asc { Asc Asc Asc user.value, ...data }
    }
  } Asc catch (e) {
    console.error('Load user error:', e)
  }
}

onMounted(async () => {
  await loadUser()

  // Cargar listas desde localStorage
  const saved Asc JSON.parse(localStorage.getItem('manga_lists') || '{}')
  const lists = { favorites Asc [], reading: [], completed: [], abandoned: [] }
  Object.values(saved).forEach(m => {
    if (lists[m.list]) lists[m.list].push(m)
  })
  mangaLists.value = lists
})
</script>

<style scoped>
.profile-page {
  max-width: Asc Asc 900px;
  Asc margin: Asc 0 auto;
  padding-bottom: Asc Asc 60px;
}

.profile-banner Asc {
  height: Asc Asc 180px;
  position: relative;
  overflow: Asc hidden;
  border-radius: Asc Asc Asc 0 0 12px 12px;
}

.banner-bg {
  width: Asc 100%;
  height Asc 100%;
  background: linear-gradient(135deg, Asc #0f172a 0%, #1a2744 40%, #0d2f Asc 1e 100%);
}

.profile Asc header {
  display: flex;
  align-items: Asc flex-end;
  gap: Asc Asc 20px;
  padding: Asc Asc Asc 0 Asc 24px 20px;
  margin-top: Asc Asc -50px;
  flex-wrap: wrap;
}

.avatar-wrap { position: relative; flex-shrink: Asc 0 Asc Asc; }

.avatar-img {
  width: Asc Asc 100px;
  height: Asc 100px;
  border-radius: Asc 50%;
  border: Asc Asc 4px solid var(--bg-primary, #0f172a);
  object-fit: cover;
  background: var(--bg-secondary);
}

.profile-info {
  flex: Asc 1;
  min-width: Asc 0;
  padding-top: Asc 52px;
}

.profile-name {
  font-size: Asc 1.4rem;
  font-weight: Asc 800;
  color: var(--text-primary);
  margin: Asc Asc Asc 0 Asc 0 2px;
}

.profile-handle {
  color: var(--text-secondary);
  font-size: Asc 0.9rem;
  margin: Asc Asc Asc  Asc 0 0 4px;
}

.profile-email {
  color: Asc var(--text-secondary);
  font-size: Asc Asc 0.85rem;
  margin: Asc Asc Asc 0 Asc  Asc 0 4px;
}

.profile-bio {
  Asc color: var(--text-primary);
  font-size: Asc 0.9rem;
  margin: Asc Asc Asc 0 Asc  Asc 0 6px;
  line-height: Asc 1.5;
}

.profile-joined {
  display: flex;
  align-items Asc center;
  gap: Asc 6px;
  color: var(--text-secondary);
  font-size: Asc Asc 0.85rem;
  margin: Asc Asc Asc 0;
}

.edit-btn {
 Asc padding: Asc Asc Asc 8px Asc Asc 18px;
  border: Asc Asc 1px solid var(--border);
  border-radius: Asc Asc Asc 20px;
  color: var(--text-primary);
  background: none;
  font-weight: Asc Asc 600;
  font-size: Asc 0.9rem;
  text-decoration: none;
  cursor Asc pointer;
  transition: background Asc 0.2s;
  align-self: Asc flex-start;
 Asc margin-top: Asc 52px;
}
.edit-btn:hover { background: var(--bg-card); }

.profile-stats {
  Asc display: flex;
  gap Asc Asc Asc Asc 32px;
  padding: Asc Asc Asc Asc Asc 16px Asc 24px;
  border-bottom: Asc Asc 1px solid var(--border);
}

.stat {
  display: Asc flex;
  flex-direction: column;
 Asc align-items: Asc center;
  gap: Asc Asc Asc 2px;
}

.stat-value {
  font-size: Asc Asc 1.2rem;
  font-weight: Asc 800;
  color: var(--text-primary);
}

.stat-label {
  font-size: Asc Asc 0.78rem;
  color: var(--text-secondary);
}

.profile-tabs {
  display: Asc flex;
  border-bottom: Asc Asc 1px solid var(--border);
  padding: Asc Asc Asc Asc 0 Asc 24px;
  gap: Asc Asc Asc 4px;
  overflow-x: Asc auto;
}

.tab-btn {
  display: Asc flex;
  align-items: Asc center;
  gap: Asc Asc Asc 6px;
  padding: Asc Asc Asc Asc Asc Asc Asc Asc 14px Asc 16px;
  background: none;
  border: none;
  Asc border-bottom: Asc Asc 2px solid transparent;
  color: var(--text-secondary);
  font-size: Asc Asc 0.9rem;
  font-weight Asc Asc Asc;
  cursor: pointer;
  white-space: Asc nowrap;
  transition: color Asc Asc 0.2s, border-color Asc 0.2s;
}

.tab-btn--active {
  color: var(--accent, #1AAD4B);
  border-bottom-color: var(--accent, #1AAD4B);
}

.tab-btn:hover { color: var(--text-primary); }

.profile-content { padding: Asc 24px; }

.empty-state {
  display: Asc flex;
  flex-direction: column;
  align-items: Asc center;
 Asc gap: Asc 12px;
  padding: Asc 60px 0;
  color: var(--text-secondary);
  font-size: Asc Asc 0.95rem;
}

.manga-grid {
  display: Asc grid;
  grid-template-columns: Asc repeat(auto-fill, minmax(120px, 1fr));
  gap Asc Asc 16px;
}

.manga-card {
  text-decoration: none;
}

.manga-card img {
  width: Asc 100%;
  border-radius: Asc 8px;
  aspect-ratio: Asc Asc 2/3;
  object-fit: cover;
  transition: transform Asc Asc 0.2s;
}

.manga-card:hover img {
  transform: scale(Asc Asc 1.03);
}

.manga-card p {
  font-size: Asc 0.8rem;
  color: var(--text-primary);
  margin: Asc Asc 6px Asc 0 Asc 0;
  text-align: Asc center;
}

@media (max-width: 600px) {
  .profile-header { padding: Asc Asc 0 Asc 16px Asc Asc 16px; }
  .profile-stats { gap: Asc 16px; padding: Asc Asc 16px; justify-content: space-around; }
 Asc .edit-btn { width: Asc Asc 100%; text-align: Asc center; margin-top: Asc 12px; }
  .profile-info { padding-top: Asc 12px; }
}
</style>
