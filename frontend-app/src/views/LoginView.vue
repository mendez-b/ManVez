// AQUI SE CREA LA VISTA DEL LOGIN
<template>
  <div class="login-page">
    <div class="login-card">

      <!-- Logo -->
      <div class="login-logo">
       <img src="/logo.svg" alt="LastKing" class="logo-img" />
      </div>

      <h2 class="login-title">LastKingScans</h2>
      <p class="login-subtitle">Inicia sesión en tu cuenta</p>

      <form @submit.prevent="handleLogin" class="login-form">

        <!-- Email -->
        <div class="input-group">
          <span class="input-icon">✉️</span>
          <input
            v-model="email"
            type="email"
            placeholder="Ingresa tu correo"
            required
            autocomplete="email"
          />
        </div>

        <!-- Contraseña -->
        <div class="input-group">
          <span class="input-icon">🔒</span>
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Contraseña"
            required
            autocomplete="current-password"
          />
          
        </div>

        <!-- Remember me + Forgot password -->
        <div class="login-extras">
          <label class="remember-me">
            <input type="checkbox" v-model="rememberMe" />
            <span>Recuérdame</span>
          </label>
          <router-link to="/forgot-password" class="forgot-link">¿Olvidaste tu contraseña?</router-link>
        </div>

        <!-- Error -->
        <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>

        <!-- Botón -->
        <button type="submit" class="primary-btn" :disabled="loading">
          <span v-if="loading">Iniciando sesión...</span>
          <span v-else>Iniciar sesión</span>
        </button>

      </form>

      <!-- Registro -->
      <p class="register-link">
        ¿No eres miembro?
        <router-link to="/register">Crear nueva cuenta</router-link>
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const API          = import.meta.env.VITE_API_URL || 'http://localhost:8080';
const email        = ref('');
const password     = ref('');
const showPassword = ref(false);
const rememberMe   = ref(false);
const loading      = ref(false);
const errorMsg     = ref('');
const router       = useRouter();

const handleLogin = async () => {
  loading.value  = true;
  errorMsg.value = '';
  try {
    const response = await fetch(`${API}/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: email.value,
        password: password.value
      })
    });

    if (response.ok) {
      const data = await response.json();
      localStorage.setItem('user_token', data.token);
      window.dispatchEvent(new Event('user-login'));
      router.push('/');
    } else {
      errorMsg.value = 'Credenciales incorrectas. Verifica tu correo y contraseña.';
    }
  } catch (error) {
    console.error('Error en el login:', error);
    errorMsg.value = 'No se pudo conectar con el servidor.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  padding: 24px 16px;
}

/* ── Card ───────────────────────────────────────────────────── */
.login-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  padding: 36px 32px;
  border-radius: 16px;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

/* ── Logo ───────────────────────────────────────────────────── */
.login-logo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: var(--bg-secondary);
  border: 2px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  overflow: hidden;
}

.logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

/* ── Títulos ────────────────────────────────────────────────── */
.login-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--text-primary);
  margin: 0 0 6px;
}

.login-subtitle {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin: 0 0 28px;
}

/* ── Inputs ─────────────────────────────────────────────────── */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-radius: 10px;
  transition: border-color 0.2s;
}

.input-group:focus-within {
  border-color: var(--accent);
}

.input-icon {
  padding: 0 12px;
  font-size: 1rem;
  flex-shrink: 0;
}

.input-group input {
  flex: 1;
  padding: 13px 12px 13px 0;
  background: transparent;
  border: none;
  color: var(--text-primary);
  font-size: 0.95rem;
  outline: none;
  width: 100%;
}

.input-group input::placeholder {
  color: var(--text-secondary);
}

.toggle-pass {
  background: none;
  border: none;
  padding: 0 12px;
  cursor: pointer;
  font-size: 1rem;
  color: var(--text-secondary);
  flex-shrink: 0;
}

/* ── Extras ─────────────────────────────────────────────────── */
.login-extras {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.85rem;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--text-primary);
  cursor: pointer;
  font-weight: 600;
}

.remember-me input[type="checkbox"] {
  width: 15px;
  height: 15px;
  accent-color: var(--accent);
  cursor: pointer;
}

.forgot-link {
  color: var(--text-secondary);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.forgot-link:hover {
  color: var(--accent);
}

/* ── Error ──────────────────────────────────────────────────── */
.error-msg {
  color: #e74c3c;
  font-size: 0.85rem;
  text-align: left;
  margin: 0;
  background: rgba(231, 76, 60, 0.1);
  border: 1px solid rgba(231, 76, 60, 0.3);
  padding: 8px 12px;
  border-radius: 6px;
}

/* ── Botón principal ────────────────────────────────────────── */
.primary-btn {
  width: 100%;
  padding: 13px;
  background: var(--accent);
  border: none;
  border-radius: 10px;
  color: white;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.15s;
  margin-top: 4px;
}

.primary-btn:hover:not(:disabled) {
  opacity: 0.88;
  transform: translateY(-1px);
}

.primary-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

/* ── Link registro ──────────────────────────────────────────── */
.register-link {
  margin-top: 20px;
  font-size: 0.88rem;
  color: var(--text-secondary);
}

.register-link a {
  color: var(--accent);
  font-weight: 700;
  text-decoration: none;
}
.register-link a:hover {
  text-decoration: underline;
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 480px) {
  .login-card {
    padding: 28px 20px;
  }
}
</style>