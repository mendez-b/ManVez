<template>
  <div class="profile-page">

    <!-- Banner -->
    <div class="profile-banner">
      <div class="banner-bg"></div>
    </div>

    <!-- Header del perfil -->
    <div class="profile-header">
      <div class="avatar-wrap">
        <img :src="avatarUrl" alt="Avatar" class="avatar-img" />
        <label class="avatar-edit" title="Cambiar foto">
          <Camera :size="16" />
          <input type="file" accept="image/*" @change="onAvatarChange" hidden />
        </label>
      </div>

      <div class="profile-info">
        <h1 class="profile-name">{{ user.username }}</h1>
        <p class="profile-handle">@{{ user.username?.toLowerCase() }}</p>
        <p class="profile-email">{{ user.email }}</p>
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
        <div v-if="favorites.length === 0" class="empty-state">
          <BookOpen :size="40" />
          <p>No tienes mangas favoritos aún</p>
        </div>
        <div v-else class="manga-grid">
          <div v-for="manga in favorites" :key="manga.id" class="manga-card">
            <img :src="manga.cover" :alt="manga.title" />
            <p>{{ manga.title }}</p>
          </div>
        </div>
      </div>

      <!-- Leyendo -->
      <div v-if="activeTab === 'reading'">
        <div class="empty-state">
          <BookOpen :size="40" />
          <p>No tienes mangas en proceso</p>
        </div>
      </div>

      <!-- Completados -->
      <div v-if="activeTab === 'completed'">
        <div class="empty-state">
          <CheckCircle :size="40" />
          <p>No tienes mangas completados</p>
        </div>
      </div>

      <!-- Abandonados -->
      <div v-if="activeTab === 'abandoned'">
        <div class="empty-state">
          <XCircle :size="40" />
          <p>No tienes mangas abandonados</p>
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
import { ref, computed, onMounted } from 'vue'
import { Camera, Calendar, BookOpen, CheckCircle, XCircle, Clock, Heart } from 'lucide-vue-next'

const API = import.meta.env.VITE_API_URL || 'http://localhost:8080'

const user = ref({
  username: '',
  email: '',
  avatar: null,
  created_at: null
})

const avatarUrl = computed(() => {
  if (user.value.avatar) return user.value.avatar
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value.username || 'U')}&background=1AAD4B&color=fff&size=128`
})

const joinedDate = computed(() => {
  if (!user.value.created_at) return ''
  const d = new Date(user.value.created_at)
  return d.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })
})

const stats = ref({ reading: 0, completed: 0, abandoned: 0, favorites: 0 })
const favorites = ref([])
const activeTab = ref('favorites')

const tabs = [
  { key: 'favorites',  label: 'Favoritos',  icon: Heart },
  { key: 'reading',    label: 'Leyendo',    icon: BookOpen },
  { key: 'completed',  label: 'Completados', icon: CheckCircle },
  { key: 'abandoned',  label: 'Abandonados', icon: XCircle },
  { key: 'history',    label: 'Historial',  icon: Clock },
]

async function onAvatarChange(e) {
  const file = e.target.files[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => {
    user.value.avatar = ev.target.result
    // Aquí puedes guardar en el backend
  }
  reader.readAsDataURL(file)
}

onMounted(() => {
  const stored = localStorage.getItem('user_data')
  if (stored) {
    try { user.value = { ...user.value, ...JSON.parse(stored) } } catch {}
  }
})
</script>

<style scoped>
.profile-page {
  max-width: 900px;
  margin: 0 auto;
  padding-bottom: 60px;
}

/* ── Banner ─────────────────────────────────────────────────── */
.profile-banner {
  height: 180px;
  position: relative;
  overflow: hidden;
  border-radius: 0 0 12px 12px;
}

.banner-bg {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #0f172a 0%, #1a2744 40%, #0d2f1e 100%);
}

/* ── Header ─────────────────────────────────────────────────── */
.profile-header {
  display: flex;
  align-items: flex-end;
  gap: 20px;
  padding: 0 24px 20px;
  margin-top: -50px;
  flex-wrap: wrap;
}

.avatar-wrap {
  position: relative;
  flex-shrink: 0;
}

.avatar-img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border: 4px solid var(--bg-primary, #0f172a);
  object-fit: cover;
  background: var(--bg-secondary);
}

.avatar-edit {
  position: absolute;
  bottom: 4px;
  right: 4px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--accent, #1AAD4B);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: opacity 0.2s;
}
.avatar-edit:hover { opacity: 0.85; }

.profile-info {
  flex: 1;
  min-width: 0;
  padding-top: 52px;
}

.profile-name {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--text-primary);
  margin: 0 0 2px;
}

.profile-handle {
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin: 0 0 4px;
}

.profile-email {
  color: var(--text-secondary);
  font-size: 0.85rem;
  margin: 0 0 6px;
}

.profile-joined {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--text-secondary);
  font-size: 0.85rem;
  margin: 0;
}

.edit-btn {
  padding: 8px 18px;
  border: 1px solid var(--border);
  border-radius: 20px;
  color: var(--text-primary);
  background: none;
  font-weight: 600;
  font-size: 0.9rem;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.2s;
  align-self: flex-start;
  margin-top: 52px;
}
.edit-btn:hover {
  background: var(--bg-card);
}

/* ── Stats ──────────────────────────────────────────────────── */
.profile-stats {
  display: flex;
  gap: 32px;
  padding: 16px 24px;
  border-bottom: 1px solid var(--border);
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.stat-value {
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--text-primary);
}

.stat-label {
  font-size: 0.78rem;
  color: var(--text-secondary);
}

/* ── Tabs ───────────────────────────────────────────────────── */
.profile-tabs {
  display: flex;
  border-bottom: 1px solid var(--border);
  padding: 0 24px;
  gap: 4px;
  overflow-x: auto;
}

.tab-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 14px 16px;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  white-space: nowrap;
  transition: color 0.2s, border-color 0.2s;
}

.tab-btn--active {
  color: var(--accent, #1AAD4B);
  border-bottom-color: var(--accent, #1AAD4B);
}

.tab-btn:hover {
  color: var(--text-primary);
}

/* ── Contenido ──────────────────────────────────────────────── */
.profile-content {
  padding: 24px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 60px 0;
  color: var(--text-secondary);
  font-size: 0.95rem;
}

.manga-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 16px;
}

.manga-card img {
  width: 100%;
  border-radius: 8px;
  aspect-ratio: 2/3;
  object-fit: cover;
}

.manga-card p {
  font-size: 0.8rem;
  color: var(--text-primary);
  margin: 6px 0 0;
  text-align: center;
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 600px) {
  .profile-header {
    padding: 0 16px 16px;
  }

  .profile-stats {
    gap: 16px;
    padding: 16px;
    justify-content: space-around;
  }

  .edit-btn {
    width: 100%;
    text-align: center;
    margin-top: 12px;
  }

  .profile-info {
    padding-top: 12px;
  }
}
</style>