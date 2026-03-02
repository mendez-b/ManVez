<template>
  <nav class="navbar">
    <div class="nav-container">
      <RouterLink to="/" class="logo"> lastKingscans</RouterLink>

      <div class="nav-links">
        <RouterLink to="/">Inicio</RouterLink>
        <RouterLink to="/search">Mangas</RouterLink>
      </div>

      <!-- Barra de búsqueda rápida -->
      <div class="search-bar">
        <input
          v-model="searchQuery"
          @keyup.enter="goToSearch"
          placeholder="Buscar manga..."
          type="text"
        />
        <button @click="goToSearch">🔍</button>
      </div>

      <!-- Switch de tema -->
      <button class="theme-btn" @click="toggleTheme">
        {{ isDark ? '☀️' : '🌙' }}
      </button>

      <div class="auth-section">
        <!-- si el usuario no está autentificado mostramos el botón de login -->
        <template v-if="!isAuthenticated">
          <router-link to="/login">
            <button class="login-btn">Iniciar Sesión</button>
          </router-link>
        </template>
        <!-- si está logueado mostramos el icono/perfil y un menú desplegable -->
        <template v-else>
          <div class="profile-dropdown">
            <button class="profile-btn" @click="toggleMenu">👤</button>
            <div class="dropdown" v-if="menuOpen">
              <router-link to="/favorites" class="dropdown-item">Favoritos</router-link>
              <a href="#" class="dropdown-item" @click.prevent="logout">Cerrar sesión</a>
            </div>
          </div>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const searchQuery = ref('')
const isDark = ref(true)

// estado de autenticación leyendo el token del localStorage
const isAuthenticated = ref(!!localStorage.getItem('user_token'))
const menuOpen = ref(false)

function toggleMenu() {
  menuOpen.value = !menuOpen.value
}

function logout() {
  localStorage.removeItem('user_token')
  isAuthenticated.value = false
  menuOpen.value = false
  router.push('/login')
}

// el token puede cambiar en otra pestaña o después de un login, así que
// escuchamos el evento de storage para actualizar el estado
window.addEventListener('storage', () => {
  isAuthenticated.value = !!localStorage.getItem('user_token')
})
// evento que disparamos manualmente al iniciar sesión (no se dispara storage en la misma pestaña)
window.addEventListener('user-login', () => {
  isAuthenticated.value = true
})

// cerrar menú al hacer clic fuera
import { onMounted, onBeforeUnmount } from 'vue'

function handleDocumentClick(e) {
  if (!e.target.closest('.profile-dropdown')) {
    menuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleDocumentClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleDocumentClick)
})

function goToSearch() {
  if (searchQuery.value.trim()) {
    router.push({ path: '/search', query: { q: searchQuery.value } })
    searchQuery.value = ''
  }
}

function toggleTheme() {
  isDark.value = !isDark.value
  document.documentElement.setAttribute('data-theme', isDark.value ? 'dark' : 'light')
}
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
  padding: 12px 20px;
  display: flex;
  align-items: center;
  gap: 24px;
}

.logo {
  font-size: 1.3rem;
  font-weight: bold;
  color: var(--accent);
}

.nav-links {
  display: flex;
  gap: 16px;
}

.nav-links a {
  color: var(--text-secondary);
  transition: color 0.2s;
}

.nav-links a:hover,
.nav-links a.router-link-active {
  color: var(--text-primary);
}

.search-bar {
  display: flex;
  flex: 1;
  max-width: 320px;
  margin-left: auto;
}

.search-bar input {
  flex: 1;
  padding: 8px 12px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 6px 0 0 6px;
  color: var(--text-primary);
  outline: none;
}

.search-bar button {
  padding: 8px 12px;
  background: var(--accent);
  border: none;
  border-radius: 0 6px 6px 0;
  cursor: pointer;
}

.theme-btn {
  background: none;
  border: 1px solid var(--border);
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1.1rem;
}

/*ESTE ES EL ESTILO PARA EL LOGIN*/
.nav-container {
  display: flex;
  justify-content: space-between; /* Esto empuja el login a la derecha */
  align-items: center;
  padding: 0 20px;
}

.login-btn {
  background-color: #2ecc71; /* Un verde que resalte sobre el azul */
  color: white;
  padding: 8px 16px;
  border-radius: 5px;
  text-decoration: none;
  font-weight: bold;
  transition: background 0.3s;
}

.login-btn:hover {
  background-color: #27ae60;
}

/* estilos del perfil y menú */
.profile-dropdown {
  position: relative;
}

.profile-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
}

.dropdown {
  position: absolute;
  right: 0;
  top: 110%;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 6px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  display: flex;
  flex-direction: column;
  min-width: 140px;
  z-index: 200;
}

.dropdown-item {
  padding: 8px 12px;
  color: var(--text-primary);
  text-decoration: none;
  cursor: pointer;
}

.dropdown-item:hover {
  background: var(--bg-secondary);
}
</style>