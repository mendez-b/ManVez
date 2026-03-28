<template>
  <div class="edit-page">

    <!-- Banner editable -->
    <div class="edit-banner" :style="bannerStyle">
      <label class="banner-edit-btn">
        <Camera :size="18" />
        <span>Cambiar portada</span>
        <input type="file" accept="image/*" @change="onBannerChange" hidden />
      </label>
    </div>

    <!-- Avatar editable -->
    <div class="edit-avatar-wrap">
      <div class="avatar-container">
        <img :src="previewAvatar" alt="Avatar" class="edit-avatar" />
        <label class="avatar-edit-btn" title="Cambiar foto">
          <Camera :size="16" />
          <input type="file" accept="image/*" @change="onAvatarChange" hidden />
        </label>
      </div>
    </div>

    <!-- Formulario -->
    <div class="edit-form-wrap">
      <div class="edit-header">
        <h2>Editar perfil</h2>
        <div class="edit-actions">
          <RouterLink to="/profile" class="cancel-btn">Cancelar</RouterLink>
          <button class="save-btn" @click="saveProfile" :disabled="saving">
            {{ saving ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </div>

      <!-- Nombre -->
      <div class="field-group">
        <label class="field-label">Nombre</label>
        <input
          v-model="form.username"
          type="text"
          class="field-input"
          placeholder="Tu nombre"
          maxlength="50"
        />
        <span class="field-count">{{ form.username?.length || 0 }}/50</span>
      </div>

      <!-- Descripción -->
      <div class="field-group">
        <label class="field-label">Descripción</label>
        <textarea
          v-model="form.bio"
          class="field-input field-textarea"
          placeholder="Cuéntanos algo sobre ti..."
          maxlength="160"
          rows="3"
        ></textarea>
        <span class="field-count">{{ form.bio?.length || 0 }}/160</span>
      </div>

      <p v-if="successMsg" class="success-msg">{{ successMsg }}</p>
      <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Camera } from 'lucide-vue-next'

const router     = useRouter()
const saving     = ref(false)
const successMsg = ref('')
const errorMsg   = ref('')
const API        = import.meta.env.VITE_API_URL || 'http://localhost:8080'

const form = ref({ username: '', bio: '' })
const previewAvatar = ref('')
const previewBanner = ref('')
const newAvatar     = ref(null) // base64 nueva imagen
const newBanner     = ref(null) // base64 nueva portada

const bannerStyle = computed(() => {
  if (previewBanner.value) {
    return { backgroundImage: `url(${previewBanner.value})`, backgroundSize: 'cover', backgroundPosition: 'center' }
  }
  return {}
})

function toBase64(file) {
  return new Promise((resolve) => {
    const reader = new FileReader()
    reader.onload = (e) => resolve(e.target.result)
    reader.readAsDataURL(file)
  })
}

async function onAvatarChange(e) {
  const file = e.target.files[0]
  if (!file) return
  const b64 = await toBase64(file)
  previewAvatar.value = b64
  newAvatar.value = b64
}

async function onBannerChange(e) {
  const file = e.target.files[0]
  if (!file) return
  const b64 = await toBase64(file)
  previewBanner.value = b64
  newBanner.value = b64
}

async function saveProfile() {
  saving.value  = true
  errorMsg.value = ''

  try {
    const stored = JSON.parse(localStorage.getItem('user_data') || '{}')

    const body = {
      user_id:  stored.id,
      username: form.value.username,
      bio:      form.value.bio,
    }
    if (newAvatar.value) body.avatar = newAvatar.value
    if (newBanner.value) body.banner = newBanner.value

    const response = await fetch(`${API}/profile`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    })

    if (response.ok) {
      const data = await response.json()
      localStorage.setItem('user_data', JSON.stringify(data.user))
      window.dispatchEvent(new Event('user-login')) // actualizar navbar
      successMsg.value = '¡Perfil actualizado!'
      setTimeout(() => router.push('/profile'), 1200)
    } else {
      const err = await response.json()
      errorMsg.value = err.error || 'Error al actualizar perfil.'
    }
  } catch (err) {
    console.error(err)
    errorMsg.value = 'Error de conexión.'
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  const stored = localStorage.getItem('user_data')
  if (stored) {
    const data = JSON.parse(stored)
    form.value.username = data.username || ''
    form.value.bio      = data.bio      || ''
    previewAvatar.value = data.avatar   || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.username || 'U')}&background=1AAD4B&color=fff&size=128`
    previewBanner.value = data.banner   || ''
  }
})
</script>

<style scoped>
.edit-page {
  max-width: 600px;
  margin: 0 auto;
  padding-bottom: 60px;
}

.edit-banner {
  height: 180px;
  background: linear-gradient(135deg, #0f172a 0%, #1a2744 40%, #0d2f1e 100%);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.banner-edit-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: rgba(0,0,0,0.5);
  color: white;
  border-radius: 20px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.2s;
  backdrop-filter: blur(4px);
}
.banner-edit-btn:hover { background: rgba(0,0,0,0.7); }

.edit-avatar-wrap {
  padding: 0 24px;
  margin-top: -50px;
  margin-bottom: 16px;
}

.avatar-container {
  position: relative;
  display: inline-block;
}

.edit-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border: 4px solid var(--bg-primary, #0f172a);
  object-fit: cover;
  background: var(--bg-secondary);
  display: block;
}

.avatar-edit-btn {
  position: absolute;
  bottom: 4px;
  right: 4px;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: var(--accent, #1AAD4B);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: opacity 0.2s;
}
.avatar-edit-btn:hover { opacity: 0.85; }

.edit-form-wrap { padding: 0 24px; }

.edit-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.edit-header h2 {
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--text-primary);
  margin: 0;
}

.edit-actions { display: flex; gap: 10px; align-items: center; }

.cancel-btn {
  padding: 8px 16px;
  border: 1px solid var(--border);
  border-radius: 20px;
  color: var(--text-primary);
  background: none;
  font-size: 0.9rem;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.2s;
}
.cancel-btn:hover { background: var(--bg-card); }

.save-btn {
  padding: 8px 20px;
  background: var(--text-primary);
  color: var(--bg-primary, #0f172a);
  border: none;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 700;
  cursor: pointer;
  transition: opacity 0.2s;
}
.save-btn:hover:not(:disabled) { opacity: 0.85; }
.save-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.field-group { position: relative; margin-bottom: 20px; }

.field-label {
  display: block;
  font-size: 0.85rem;
  color: var(--text-secondary);
  margin-bottom: 6px;
  font-weight: 600;
}

.field-input {
  width: 100%;
  padding: 12px 14px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-primary);
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
}
.field-input:focus { border-color: var(--accent); }

.field-textarea { resize: none; font-family: inherit; line-height: 1.5; }

.field-count {
  position: absolute;
  right: 0;
  bottom: -18px;
  font-size: 0.75rem;
  color: var(--text-secondary);
}

.success-msg {
  color: var(--accent);
  font-size: 0.9rem;
  background: rgba(26, 173, 75, 0.1);
  border: 1px solid rgba(26, 173, 75, 0.3);
  padding: 10px 14px;
  border-radius: 8px;
  margin-top: 8px;
}

.error-msg {
  color: #e74c3c;
  font-size: 0.9rem;
  background: rgba(231, 76, 60, 0.1);
  border: 1px solid rgba(231, 76, 60, 0.3);
  padding: 10px 14px;
  border-radius: 8px;
  margin-top: 8px;
}

@media (max-width: 600px) {
  .edit-form-wrap { padding: 0 16px; }
  .edit-avatar-wrap { padding: 0 16px; }
}
</style>