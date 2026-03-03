<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="visible" class="modal-backdrop" @click.self="close">
        <div class="modal-box">
          <!-- Icono -->
          <div class="modal-icon">📚</div>

          <!-- Texto -->
          <h2 class="modal-title">¡Bienvenido a LastKingScans!</h2>
          <p class="modal-subtitle">
            Inicia sesión o regístrate para seguir disfrutando del contenido de <strong>LastKing</strong>.
          </p>

          <!-- Botones -->
          <div class="modal-actions">
            <RouterLink to="/login" class="btn btn--primary" @click="close">
              Iniciar sesión
            </RouterLink>
            <RouterLink to="/register" class="btn btn--secondary" @click="close">
              Registrarse
            </RouterLink>
          </div>

          <!-- Cerrar -->
          <button class="modal-close" @click="close">✕</button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const visible = ref(false)

function close() {
  visible.value = false
}

onMounted(() => {
  // Solo mostrar si el usuario no está logueado
  const token = localStorage.getItem('user_token')
  if (!token) {
    setTimeout(() => {
      visible.value = true
    }, 3000)
  }
})
</script>

<style scoped>
/* ── Backdrop ───────────────────────────────────────────────── */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  /* Fondo borroso */
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  background: rgba(0, 0, 0, 0.45);
}

/* ── Caja modal ─────────────────────────────────────────────── */
.modal-box {
  position: relative;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 36px 32px 28px;
  max-width: 420px;
  width: 100%;
  text-align: center;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5);
}

/* ── Ícono ──────────────────────────────────────────────────── */
.modal-icon {
  font-size: 2.4rem;
  margin-bottom: 12px;
}

/* ── Título ─────────────────────────────────────────────────── */
.modal-title {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--text-primary);
  margin: 0 0 10px;
  line-height: 1.3;
}

/* ── Subtítulo ──────────────────────────────────────────────── */
.modal-subtitle {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin: 0 0 24px;
  line-height: 1.6;
}
.modal-subtitle strong {
  color: var(--accent);
}

/* ── Botones ────────────────────────────────────────────────── */
.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.btn {
  padding: 10px 28px;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.2s, transform 0.15s;
  cursor: pointer;
}
.btn:hover {
  opacity: 0.88;
  transform: translateY(-1px);
}

.btn--primary {
  background: var(--accent);
  color: white;
  border: none;
}

.btn--secondary {
  background: transparent;
  color: var(--text-primary);
  border: 1px solid var(--border);
}
.btn--secondary:hover {
  border-color: var(--accent);
  color: var(--accent);
}

/* ── Botón cerrar ───────────────────────────────────────────── */
.modal-close {
  position: absolute;
  top: 12px;
  right: 14px;
  background: none;
  border: none;
  color: var(--text-secondary);
  font-size: 1rem;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 4px;
  transition: color 0.2s;
  line-height: 1;
}
.modal-close:hover {
  color: var(--text-primary);
}

/* ── Animación entrada/salida ───────────────────────────────── */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.modal-enter-active .modal-box,
.modal-leave-active .modal-box {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.modal-enter-from {
  opacity: 0;
}
.modal-enter-from .modal-box {
  transform: scale(0.92) translateY(16px);
  opacity: 0;
}
.modal-leave-to {
  opacity: 0;
}
.modal-leave-to .modal-box {
  transform: scale(0.92) translateY(16px);
  opacity: 0;
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 480px) {
  .modal-box {
    padding: 28px 20px 22px;
  }
  .modal-actions {
    flex-direction: column;
  }
  .btn {
    width: 100%;
    text-align: center;
  }
}
</style>