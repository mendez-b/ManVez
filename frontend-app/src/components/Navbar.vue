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
        <router-link to="/login">
          <button class="login-btn">Iniciar Sesión</button>
        </router-link>
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
</style>