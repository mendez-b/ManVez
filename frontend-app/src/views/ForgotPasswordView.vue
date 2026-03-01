
//aqui se recupera la contraseña del usuario
<template>
  <div class="auth-page">
    <div class="auth-card">
      <h2>Recuperar Contraseña</h2>
      <p>Introduce tu correo para enviarte las instrucciones.</p>
      
      <form @submit.prevent="handleResetRequest">
        <div class="input-group">
          <label>Correo Electrónico</label>
          <input v-model="email" type="email" placeholder="ejemplo@correo.com" required />
        </div>

        <button type="submit" class="primary-btn">Enviar Enlace</button>
      </form>

      <div class="auth-options">
        <span>¿Recordaste tu clave? <router-link to="/login">Volver al inicio</router-link></span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const email = ref('');

const handleResetRequest = async () => {
  try {
    const response = await fetch('http://localhost:8080/forgot-password', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value })
    });

    if (response.ok) {
      alert("Si el correo existe en nuestra base de datos, recibirás un enlace pronto.");
    } else {
      alert("Hubo un problema al procesar la solicitud.");
    }
  } catch (error) {
    console.error("Error:", error);
  }
};
</script>

<style scoped>
/* Reutilizamos la estética de las otras vistas para consistencia visual */
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
  background: #3498db; /* Azul para diferenciar del registro */
  border: none;
  border-radius: 6px;
  color: white;
  font-weight: bold;
  cursor: pointer;
  margin-top: 15px;
}
</style>