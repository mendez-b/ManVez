
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