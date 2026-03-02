
//AQUI SE CREA UN REGISTRO DE DATOSPARA EL USUARIO
<template>
  <div class="register-page">
    <div class="register-card">
      <h2>Crear Cuenta</h2>
      <p>Únete a la comunidad de MangaReads</p>
      
      <form @submit.prevent="handleRegister">
        <div class="input-group">
          <label>Nombre de Usuario</label>
          <input v-model="username" type="text" placeholder="Tu nombre" required />
        </div>

        <div class="input-group">
          <label>Correo Electrónico</label>
          <input v-model="email" type="email" placeholder="ejemplo@correo.com" required />
        </div>
        
        <div class="input-group">
          <label>Contraseña</label>
          <input v-model="password" type="password" placeholder="••••••••" required />
        </div>

        <button type="submit" class="primary-btn">Registrarse</button>
      </form>

      <div class="auth-options">
        <span>¿Ya tienes cuenta? <router-link to="/login">Inicia sesión</router-link></span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('');
const email = ref('');
const password = ref('');
const router = useRouter();

const handleRegister = async () => {
  try {
    const API = import.meta.env.VITE_API_URL || 'http://localhost:8080';
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
      alert("¡Cuenta creada con éxito!");
      router.push('/login'); 
    } else {
      alert("Error al registrar. El correo podría estar en uso.");
    }
  } catch (error) {
    console.error("Error en el registro:", error);
  }
};
</script>

<style scoped>
/* Reutilizamos los estilos del login para mantener consistencia */
.register-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  color: white;
}

.register-card {
  background: #1e293b; 
  padding: 2.5rem;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.5);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.input-group {
  text-align: left;
  margin-bottom: 1.2rem;
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
  margin-top: 10px;
}

.auth-options {
  margin-top: 1.5rem;
  font-size: 0.9rem;
}

.auth-options a {
  color: #3498db;
  text-decoration: none;
}
</style>