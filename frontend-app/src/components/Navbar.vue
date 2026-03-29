<template>
  <nav class="navbar">
    <div class="nav-container">
      <RouterLink to="/" class="logo">
        <img src="/logo.svg" alt="LastKing" class="logo-img" />
      </RouterLink>

      <!-- Links escritorio -->
      <div class="nav-links">
        <RouterLink to="/">Inicio</RouterLink>
        <RouterLink to="/favorites">Favoritos</RouterLink>
        <RouterLink to="/search">Mangas</RouterLink>
      </div>

      <!-- Barra de búsqueda normal (solo PC) -->
      <div class="search-bar search-bar--desktop">
        <input
          v-model="searchQuery"
          @keyup.enter="goToSearch"
          placeholder="Buscar manga..."
          type="text"
        />
        <button @click="goToSearch" class="search-btn">
          <Search :size="16" />
        </button>
      </div>

      <!-- Lupa expandible (solo móvil) -->
      <div class="search-wrap" :class="{ 'search-wrap--open': searchOpen }">
        <div v-if="searchOpen" class="search-bar search-bar--mobile">
          <Search :size="16" class="search-bar__icon" />
          <input
            ref="searchInput"
            v-model="searchQuery"
            @keyup.enter="goToSearch"
            placeholder="Buscar manga..."
            type="text"
          />
          <button class="search-bar__close" @click="closeSearch">
            <X :size="16" />
          </button>
        </div>
        <button v-else class="icon-btn icon-btn--mobile" @click="openSearch">
          <Search :size="20" />
        </button>
      </div>

      <!-- Botón Login -->
      <RouterLink v-if="!isLoggedIn" to="/login" class="login-btn">
        <User :size="15" />
        <span>Iniciar sesión</span>
      </RouterLink>

      <!-- Avatar con menú desplegable -->
      <div v-else class="avatar-menu" ref="avatarMenu">
        <button class="avatar-btn" @click="toggleDropdown">
          <img :src="avatarUrl" alt="Perfil" class="avatar-img" />
        </button>

        <div v-if="dropdownOpen" class="dropdown">
          <RouterLink to="/profile" class="dropdown-item" @click="dropdownOpen = false">
            <User :size="16" />
            Mi perfil
          </RouterLink>
          <RouterLink to="/favorites" class="dropdown-item" @click="dropdownOpen = false">
            <Bookmark :size="16" />
            Favoritos
          </RouterLink>
          <div class="dropdown-divider"></div>
          <button class="dropdown-item dropdown-item--danger" @click="handleLogout">
            <LogOut :size="16" />
            Cerrar sesión
          </button>
        </div>
      </div>

      <!-- Switch de tema -->
      <button class="theme-btn" @click="toggleTheme">
        <Sun v-if="!isDark" :size="18" />
        <Moon v-else :size="18" />
      </button>

      <!-- Botón hamburguesa (solo móvil) -->
      <button class="hamburger" :class="{ 'hamburger--hidden': searchOpen }" @click="openMenu">
        <Menu :size="22" />
      </button>
    </div>

    <!-- Overlay oscuro detrás del menú -->
    <div v-if="menuOpen" class="menu-overlay" @click="closeMenu"></div>

    <!-- Panel deslizante -->
    <div class="side-menu" :class="{ 'side-menu--open': menuOpen }">
      <button class="side-menu__close" @click="closeMenu">
        <X :size="20" />
      </button>

      <nav class="side-menu__nav">
        <RouterLink to="/" class="side-menu__item" @click="closeMenu">
          <Home :size="20" />
          Inicio
        </RouterLink>
        <RouterLink to="/search" class="side-menu__item" @click="closeMenu">
          <BookOpen :size="20" />
          Mangas
        </RouterLink>
        <RouterLink to="/favorites" class="side-menu__item" @click="closeMenu">
          <Bookmark :size="20" />
          Favoritos
        </RouterLink>
        <RouterLink v-if="isLoggedIn" to="/profile" class="side-menu__item" @click="closeMenu">
          <User :size="20" />
          Mi perfil
        </RouterLink>
        <RouterLink v-if="!isLoggedIn" to="/login" class="side-menu__item" @click="closeMenu">
          <User :size="20" />
          Iniciar sesión
        </RouterLink>
        <button v-else class="side-menu__item side-menu__item--btn" @click="handleLogout; closeMenu()">
          <LogOut :size="20" />
          Cerrar sesión
        </button>
        <button class="side-menu__item side-menu__item--btn" @click="toggleTheme; closeMenu()">
        <Sun v-if="!isDark" :size="20" />
          <Moon v-else :size="20" />
          {{ isDark ? 'Modo claro' : 'Modo oscuro' }}
        </button>
      </nav>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { Search, Sun, Moon, Menu, X, Home, BookOpen, Bookmark, User, LogOut } from 'lucide-vue-next'

const router      = useRouter()
const searchQuery = ref('')
const searchOpen  = ref(false)
const searchInput = ref(null)
const isDark      = ref(true)
const menuOpen    = ref(false)
const isLoggedIn  = ref(false)
const dropdownOpen = ref(false)
const avatarMenu  = ref(null)
const userData    = ref(null)

const avatarUrl = computed(() => {
  if (userData.value?.avatar) return userData.value.avatar
  if (userData.value?.profile_pic_url) return userData.value.profile_pic_url
  const name = userData.value?.username || 'U'
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1AAD4B&color=fff&size=64`
})

function checkAuth() {
  isLoggedIn.value = !!localStorage.getItem('user_token')
  const stored = localStorage.getItem('user_data')
  if (stored) {
    try { userData.value = JSON.parse(stored) } catch {}
  }
}

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value
}

function handleClickOutside(e) {
  if (avatarMenu.value && !avatarMenu.value.contains(e.target)) {
    dropdownOpen.value = false
  }
}

async function openSearch() {
  searchOpen.value = true
  await nextTick()
  searchInput.value?.focus()
}

function closeSearch() {
  searchOpen.value = false
  searchQuery.value = ''
}

function goToSearch() {
  if (searchQuery.value.trim()) {
    router.push({ path: '/search', query: { q: searchQuery.value } })
    searchQuery.value = ''
    searchOpen.value = false
  }
}

function toggleTheme() {
  isDark.value = !isDark.value
  document.documentElement.setAttribute('data-theme', isDark.value ? 'dark' : 'light')
}

function handleLogout() {
  localStorage.removeItem('user_token')
  localStorage.removeItem('user_data')
  isLoggedIn.value = false
  dropdownOpen.value = false
  router.push('/')
}

function openMenu()  { menuOpen.value = true  }
function closeMenu() { menuOpen.value = false }

onMounted(() => {
  checkAuth()
  window.addEventListener('user-login', checkAuth)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.navbar {
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 10px 20px;
  display: flex;
  align-items: center;
  gap: 20px;
}

/* ── Logo ───────────────────────────────────────────────────── */
.logo {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.logo-img {
  height: 40px;
  width: auto;
  object-fit: contain;
}

/* ── Nav links ──────────────────────────────────────────────── */
.nav-links {
  display: flex;
  gap: 16px;
}

.nav-links a {
  color: var(--text-secondary);
  transition: color 0.2s;
  font-size: 0.95rem;
}

.nav-links a:hover,
.nav-links a.router-link-active {
  color: var(--text-primary);
}

/* ── Search bar desktop ─────────────────────────────────────── */
.search-bar--desktop {
  display: flex;
  flex: 1;
  max-width: 320px;
  margin-left: auto;
}

.search-bar--desktop input {
  flex: 1;
  padding: 8px 12px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 6px 0 0 6px;
  color: var(--text-primary);
  outline: none;
  font-size: 0.9rem;
}

.search-btn {
  padding: 8px 12px;
  background: var(--accent);
  border: none;
  border-radius: 0 6px 6px 0;
  cursor: pointer;
  display: flex;
  align-items: center;
  color: white;
}

/* ── Search expandible móvil ────────────────────────────────── */
.search-wrap {
  display: none;
  align-items: center;
}

.search-wrap--open {
  flex: 1;
}

.icon-btn--mobile {
  background: none;
  border: 1px solid var(--border);
  color: var(--text-primary);
  padding: 7px 9px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  transition: border-color 0.2s, color 0.2s;
}
.icon-btn--mobile:hover {
  border-color: var(--accent);
  color: var(--accent);
}

.search-bar--mobile {
  display: flex;
  align-items: center;
  flex: 1;
  background: var(--bg-card);
  border: 1px solid var(--accent);
  border-radius: 6px;
  padding: 0 10px;
  gap: 8px;
  animation: expandSearch 0.2s ease;
}

@keyframes expandSearch {
  from { opacity: 0; transform: scaleX(0.85); }
  to   { opacity: 1; transform: scaleX(1); }
}

.search-bar__icon {
  color: var(--text-secondary);
  flex-shrink: 0;
}

.search-bar--mobile input {
  flex: 1;
  padding: 8px 0;
  background: transparent;
  border: none;
  color: var(--text-primary);
  outline: none;
  font-size: 0.9rem;
}

.search-bar--mobile input::placeholder {
  color: var(--text-secondary);
}

.search-bar__close {
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  flex-shrink: 0;
  transition: color 0.2s;
}
.search-bar__close:hover {
  color: var(--text-primary);
}

/* ── Botón login ────────────────────────────────────────────── */
.login-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  white-space: nowrap;
  transition: opacity 0.2s;
  flex-shrink: 0;
}

.login-btn:hover {
  opacity: 0.85;
}

/* ── Avatar menu ────────────────────────────────────────────── */
.avatar-menu {
  position: relative;
  flex-shrink: 0;
}

.avatar-btn {
  background: none;
  border: 2px solid var(--accent);
  border-radius: 50%;
  padding: 0;
  cursor: pointer;
  display: flex;
  align-items: center;
  transition: border-color 0.2s;
}

.avatar-btn:hover {
  border-color: var(--text-primary);
}

.avatar-img {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
}

.dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 10px;
  min-width: 180px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.3);
  overflow: hidden;
  animation: fadeDown 0.15s ease;
  z-index: 300;
}

@keyframes fadeDown {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 16px;
  color: var(--text-primary);
  text-decoration: none;
  font-size: 0.9rem;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: background 0.15s;
}

.dropdown-item:hover {
  background: color-mix(in srgb, var(--accent) 10%, transparent);
}

.dropdown-item--danger {
  color: #e74c3c;
}

.dropdown-item--danger:hover {
  background: rgba(231, 76, 60, 0.1);
}

.dropdown-divider {
  height: 1px;
  background: var(--border);
  margin: 4px 0;
}

/* ── Tema ───────────────────────────────────────────────────── */
.theme-btn {
  background: none;
  border: 1px solid var(--border);
  padding: 7px 9px;
  border-radius: 6px;
  cursor: pointer;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

/* ── Hamburguesa ────────────────────────────────────────────── */
.hamburger {
  display: none;
  background: none;
  border: 1px solid var(--border);
  color: var(--text-primary);
  padding: 6px 8px;
  border-radius: 6px;
  cursor: pointer;
  align-items: center;
}

/* ── Overlay ────────────────────────────────────────────────── */
.menu-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  z-index: 200;
}

/* ── Panel lateral ──────────────────────────────────────────── */
.side-menu {
  position: fixed;
  top: 0;
  right: 0;
  width: 75vw;
  max-width: 300px;
  height: 100dvh;
  background: var(--bg-secondary);
  border-left: 1px solid var(--border);
  z-index: 201;
  display: flex;
  flex-direction: column;
  padding: 20px;
  transform: translateX(100%);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.side-menu--open {
  transform: translateX(0);
}

.side-menu__close {
  align-self: flex-end;
  background: none;
  border: none;
  color: var(--text-primary);
  cursor: pointer;
  padding: 4px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
}

.side-menu__nav {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.side-menu__item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 14px 12px;
  color: var(--text-primary);
  text-decoration: none;
  font-size: 1.05rem;
  font-weight: 500;
  border-radius: 8px;
  transition: background 0.2s;
}

.side-menu__item:hover,
.side-menu__item.router-link-active {
  background: color-mix(in srgb, var(--accent) 15%, transparent);
  color: var(--accent);
}

.side-menu__item--btn {
  background: none;
  border: none;
  cursor: pointer;
  width: 100%;
  text-align: left;
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 768px) {
  .nav-links,
  .login-btn,
  .search-bar--desktop,
  .theme-btn {
    display: none;
  }

  .search-wrap {
    display: flex;
    margin-left: auto;
  }

  .hamburger {
    display: flex;
  }

  .hamburger--hidden {
    display: none !important;
  }
}
</style>