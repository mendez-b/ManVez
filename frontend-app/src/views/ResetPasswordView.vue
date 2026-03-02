<template>
  <div class="auth-page">
    <div class="auth-card">
      <h2>Nueva Contraseña</h2>
      <p>Ingresa tu nueva clave de acceso para MangaReads.</p>
      
      <form @submit.prevent="handleReset">
        <div class="input-group">
          <label>Nueva Contraseña</label>
          <input v-model="password" type="password" placeholder="••••••••" required />
        </div>
        
        <div class="input-group">
          <label>Confirmar Contraseña</label>
          <input v-model="confirmPassword" type="password" placeholder="••••••••" required />
        </div>

        <button type="submit" class="primary-btn">Actualizar Contraseña</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const password = ref('');
const confirmPassword = ref('');
const token = ref('');

// Al cargar la vista, extraemos el token de la URL (ej: ?token=XYZ123)
onMounted(() => {
  token.value = route.query.token;
  if (!token.value) {
    alert("Token no válido o ausente.");
    router.push('/login');
  }
});

const handleReset = async () => {
  if (password.value !== confirmPassword.value) {
    alert("Las contraseñas no coinciden.");
    return;
  }

  try {
    const API = import.meta.env.VITE_API_URL || 'http://localhost:8080';
    const response = await fetch(`${API}/reset-password`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        token: token.value, 
        password: password.value 
      })
    });

    const data = await response.json();

    if (response.ok) {
      alert("¡Contraseña actualizada! Ya puedes iniciar sesión.");
      router.push('/login');
    } else {
      alert(data.error || "Error al actualizar.");
    }
  } catch (error) {
    console.error("Error:", error);
  }
};
</script>

<style scoped>
/* Reutilizamos los estilos oscuros para mantener la estética */
.auth-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  color: white;
}
.auth-card {
  background: #1e293b;
  padding: 2.5rem;
  border-radius: 12px;
  width: 100%;
  max-width: 400px;
  text-align: center;
}
input {
  width: 100%;
  padding: 12px;
  margin-top: 5px;
  background: #0f172a;
  border: 1px solid #334155;
  border-radius: 6px;
  color: white;
}
.primary-btn {
  width: 100%;
  padding: 12px;
  background: #2ecc71;
  border: none;
  border-radius: 6px;
  color: white;
  font-weight: bold;
  cursor: pointer;
  margin-top: 15px;
}
</style>