
// AQUI SE CREA LA VISTA DEL LOGIN
<template>
  <div class="login-container">
    <form @submit.prevent="handleLogin">
      <h2>Iniciar Sesión</h2>
      <input v-model="email" type="email" placeholder="Correo" required />
      <input v-model="password" type="password" placeholder="Contraseña" required />
      <button type="submit">Entrar</button>
    </form>
  </div>
  <div class="login-page">
    <div class="login-card">
      <h2>MangaReads</h2>
      <p>Bienvenido de nuevo</p>
      
      <form @submit.prevent="handleLogin">
        <div class="input-group">
          <label>Correo Electrónico</label>
          <input v-model="email" type="email" placeholder="ejemplo@correo.com" required />
        </div>
        
        <div class="input-group">
          <label>Contraseña</label>
          <input v-model="password" type="password" placeholder="••••••••" required />
        </div>

        <button type="submit" class="primary-btn">Entrar</button>
      </form>

      <div class="auth-options">
        <router-link to="/forgot-password">¿Olvidaste tu contraseña?</router-link>
        <hr />
        <span>¿No tienes cuenta? <router-link to="/register">Regístrate aquí</router-link></span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const router = useRouter();

const handleLogin = async () => {
  try {
    // Aquí llamas a tu API de CodeIgniter
    const response = await fetch('http://localhost:8080/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        email: email.value, 
        password: password.value 
      })
    });

    if (response.ok) {
      const data = await response.json();
      // Guarda el token de sesión para que el router lo reconozca
      localStorage.setItem('user_token', data.token); 
      // Redirige al home tras el éxito
      router.push('/'); 
    } else {
      alert("Credenciales incorrectas");
    }
  } catch (error) {
    console.error("Error en el login:", error);
    alert("No se pudo conectar con el servidor. ¿Olvidaste php spark serve?");
  }
};
</script>

//ASYNC: esto permite que la app siga funcionando mientras se espera la respuesta del servidor, 
//evitando bloqueos en la interfaz de usuario.

//FETCH: es una herramienta nativa del navegador para hacer peticiones a servidores

//AWAIT: le dice a javascript "espere aqui hasta que el servidor responda", sin esto
//el codigo seguiria a la siguiente linea sin tener los datos

//'http://localhost:8080/login': es la direccion URL del servidor codelgniter donde esta programada 
//la logica para revisar uruarios

//por defecto las peticiones son tipo GET (para pedir datos)
//usamos POST porque se esta enviando informacion sencible (correo y contraseña) 

//BODY: es la caja donde donde se meten los datos que quiero enviar al servidor 

//{ email:email.value, ... }: aqui se esta tomando lo que el usuario escribio en los campos
//de texto del componente Vue

//JSON.stringify(...): esta funcion convierte mi objeto en un string de texto JSON
//el cual es el lenguage universal para que codelgniter y Vue puedan entenderse

<style scoped>

.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  color: white;
}

.login-card {
  background: #1e293b; /* Un azul muy oscuro para combinar */
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
  background: #2ecc71; /* El verde que usamos antes */
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

hr {
  margin: 1rem 0;
  border: 0;
  border-top: 1px solid #334155;
}

</style>