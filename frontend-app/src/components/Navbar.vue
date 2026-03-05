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

      <!-- Botón Login / Usuario -->
      <RouterLink v-if="!isLoggedIn" to="/login" class="login-btn">
        <User :size="15" />
        <span>Iniciar sesión</span>
      </RouterLink>
      <button v-else class="login-btn login-btn--user" @click="handleLogout">
        <User :size="15" />
        <span>Cerrar sesión</span>
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
        <RouterLink v-if="!isLoggedIn" to="/login" class="side-menu__item" @click="closeMenu">
          <User :size="20" />
          Iniciar sesión
        </RouterLink>
        <button v-else class="side-menu__item side-menu__item--btn" @click="handleLogout; closeMenu()">
          <User :size="20" />
          Cerrar sesión
        </button>
      </nav>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { Search, Sun, Moon, Menu, X, Home, BookOpen, Bookmark, User } from 'lucide-vue-next'

const router      = useRouter()
const searchQuery = ref('')
const searchOpen  = ref(false)
const searchInput = ref(null)
const isDark      = ref(true)
const menuOpen    = ref(false)
const isLoggedIn  = ref(false)

async function openSearch() {
  searchOpen.value = true
  await nextTick()
  searchInput.value?.focus()
}

function closeSearch() {
  searchOpen.value = false
  searchQuery.value = ''
}

function checkAuth() {
  isLoggedIn.value = !!localStorage.getItem('user_token')
}

function goToSearch() {
  if (searchQuery.value.trim()) {
    router.push({ path: '/search', query: { q: searchQuery.value } })
    searchQuery.value = ''
    searchOpen.value = false
  }
}

function handleLogout() {
  localStorage.removeItem('user_token')
  isLoggedIn.value = false
  router.push('/')
}

function openMenu()  { menuOpen.value = true  }
function closeMenu() { menuOpen.value = false }

onMounted(() => {
  checkAuth()
  window.addEventListener('user-login', checkAuth)
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

.login-btn--user {
  background: var(--bg-card);
  color: var(--text-primary);
  border: 1px solid var(--border);
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
  display: none;
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
  .search-bar--desktop {
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