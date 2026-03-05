//AQUI SE CREA UN REGISTRO DE DATOS PARA EL USUARIO
<template>
  <div class="register-page">
    <div class="register-card">

      <!-- Logo -->
      <div class="register-logo">
       <img src="/logo.svg" alt="LastKing" class="logo-img" />
      </div>

      <h2 class="register-title">LastKingScans</h2>
      <p class="register-subtitle">Crea tu cuenta gratis</p>

      <form @submit.prevent="handleRegister" class="register-form">

        <!-- Nombre de usuario -->
        <div class="input-group">
          <span class="input-icon">👤</span>
          <input
            v-model="username"
            type="text"
            placeholder="Nombre de usuario"
            required
            autocomplete="username"
          />
        </div>

        <!-- Email -->
        <div class="input-group">
          <span class="input-icon">✉️</span>
          <input
            v-model="email"
            type="email"
            placeholder="Correo electrónico"
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
            autocomplete="new-password"
          />
        </div>

        <!-- Confirmar contraseña -->
        <div class="input-group">
          <span class="input-icon">🔒</span>
          <input
            v-model="confirmPassword"
            :type="showConfirm ? 'text' : 'password'"
            placeholder="Confirmar contraseña"
            required
            autocomplete="new-password"
          />
        </div>

        <!-- Error -->
        <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>

        <!-- Éxito -->
        <p v-if="successMsg" class="success-msg">{{ successMsg }}</p>

        <!-- Botón -->
        <button type="submit" class="primary-btn" :disabled="loading">
          <span v-if="loading">Creando cuenta...</span>
          <span v-else>Registrarse</span>
        </button>

      </form>

      <!-- Login -->
      <p class="login-link">
        ¿Ya tienes cuenta?
        <router-link to="/login">Inicia sesión</router-link>
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const API             = import.meta.env.VITE_API_URL || 'http://localhost:8080';
const username        = ref('');
const email           = ref('');
const password        = ref('');
const confirmPassword = ref('');
const showPassword    = ref(false);
const showConfirm     = ref(false);
const loading         = ref(false);
const errorMsg        = ref('');
const successMsg      = ref('');
const router          = useRouter();

const handleRegister = async () => {
  errorMsg.value   = '';
  successMsg.value = '';

  if (password.value !== confirmPassword.value) {
    errorMsg.value = 'Las contraseñas no coinciden.';
    return;
  }

  loading.value = true;
  try {
    const response = await fetch(`${API}/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username: username.value,
        email: email.value,
        password: password.value
      })
    });

    if (response.ok) {
      successMsg.value = '¡Cuenta creada con éxito! Redirigiendo...';
      setTimeout(() => router.push('/login'), 1500);
    } else {
      errorMsg.value = 'Error al registrar. El correo podría estar en uso.';
    }
  } catch (error) {
    console.error('Error en el registro:', error);
    errorMsg.value = 'No se pudo conectar con el servidor.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.register-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  padding: 24px 16px;
}

/* ── Card ───────────────────────────────────────────────────── */
.register-card {
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
.register-logo {
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
.register-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--text-primary);
  margin: 0 0 6px;
}

.register-subtitle {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin: 0 0 28px;
}

/* ── Form ───────────────────────────────────────────────────── */
.register-form {
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

/* ── Mensajes ───────────────────────────────────────────────── */
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

.success-msg {
  color: var(--accent);
  font-size: 0.85rem;
  text-align: left;
  margin: 0;
  background: rgba(26, 173, 75, 0.1);
  border: 1px solid rgba(26, 173, 75, 0.3);
  padding: 8px 12px;
  border-radius: 6px;
}

/* ── Botón ──────────────────────────────────────────────────── */
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

/* ── Link login ─────────────────────────────────────────────── */
.login-link {
  margin-top: 20px;
  font-size: 0.88rem;
  color: var(--text-secondary);
}

.login-link a {
  color: var(--accent);
  font-weight: 700;
  text-decoration: none;
}
.login-link a:hover {
  text-decoration: underline;
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 480px) {
  .register-card {
    padding: 28px 20px;
  }
}
</style>