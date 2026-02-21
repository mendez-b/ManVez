📚 ManVez — Guía del Proyecto
Página web para leer Manga y Manhwa | Vue.js + CodeIgniter 4 + Docker

📁 Estructura del Proyecto

El repositorio tiene dos carpetas principales:
ManVez/
├── frontend-app/    ← Vue.js (lo que el usuario ve)
└── backend/
└── appstarter/     ← CodeIgniter 4 (el servidor)

🛠️ Tecnologías Usadas

Comando	Para qué sirve
Vue.js + Vite	Frontend — lo que el usuario ve en el navegador
Vue Router	Navegación entre páginas (Home, Búsqueda, Lector)
Axios	Hace peticiones HTTP al backend desde Vue
CodeIgniter 4	Backend en PHP — actúa como proxy de MangaDex
Docker	Empaqueta el backend para desplegarlo en la nube
MangaDex API	Fuente de datos de mangas y manhwas
Vercel	Hosting gratuito del frontend
Render	Hosting gratuito del backend con Docker


🐙 Comandos Git

Comandos básicos que usas siempre

Comando	Para qué sirve

git add .	Prepara todos los archivos modificados para subir
git commit -m "mensaje"	Guarda los cambios con una descripción
git push	Sube los cambios a GitHub
git status	Ver qué archivos cambiaron
git log	Ver historial de commits

Prefijos para los commits (Conventional Commits)
Comando	Para qué sirve
init:	Primera vez que subes el proyecto
feat:	Agregas algo nuevo (una página, un componente)
fix:	Corriges un error
style:	Solo cambios visuales (colores, tamaños)
refactor:	Reorganizas código sin cambiar lo que hace


🖥️ Comandos del Frontend (Vue.js)

Todos estos comandos se ejecutan dentro de la carpeta frontend-app/

Comando	Para qué sirve
cd frontend-app	Entrar a la carpeta del frontend
npm install	Instalar dependencias (solo la primera vez o si hay errores)
npm run dev	Iniciar servidor de desarrollo en localhost:5173
npm run build	Construir el proyecto para producción
npm install vue-router axios	Instalar librerías de Vue Router y Axios

💡 Recuerda

Cada vez que quieras probar el proyecto localmente, ejecuta npm run dev y abre http://localhost:5173 en el navegador.


⚙️ Comandos del Backend (CodeIgniter 4)

Todos estos comandos se ejecutan dentro de backend/appstarter/

Comando	Para qué sirve

cd backend/appstarter	Entrar a la carpeta del backend
php spark serve	Iniciar servidor local en localhost:8080
php spark make:controller Nombre	Crear un nuevo controlador PHP
composer install	Instalar dependencias de PHP
php -m | findstr intl	Verificar que la extensión intl está activa
php -m | findstr zip	Verificar que la extensión zip está activa


📄 Archivos Importantes

Frontend

Comando	Para qué sirve

src/assets/main.css	Variablesde colores y estilos globales
src/router/index.js	Rutas/páginas de la aplicación
src/views/HomeView.vue	Página principal con mangas populares
src/views/SearchView.vue	Búsqueda y filtros por géneros
src/views/ReaderView.vue	Lector de capítulos
src/components/Navbar.vue	Barra de navegación con switch de tema
src/components/MangaCard.vue	Tarjeta de cada manga
vercel.json	Configuración de Vercel (proxy de rutas)

Backend

Comando	Para qué sirve

app/Controllers/MangaProxy.php	Controlador que hace proxy a MangaDex
app/Config/Routes.php	Rutas del backend (/api/mangadex y /covers)
Dockerfile	Instrucciones para crear el contenedor Docker
apache.conf	Configuración del servidor Apache en el contenedor
.env	Variables de entorno (CI_ENVIRONMENT, baseURL)


🎨 Cambiar Colores del Tema

Para cambiar la paleta de colores, edita el archivo src/assets/main.css:

Comando	Para qué sirve

--bg-primary	Fondo principal (azul noche: #0a0f1e)
--bg-secondary	Fondo de la navbar
--bg-card	Fondo de las tarjetas de manga
--accent	Color principal de acento (verde troll: #024F32)
--accent-hover	Color del acento al pasar el mouse
--text-primary	Color del texto principal
--text-secondary	Color del texto secundario


🔗 URLs del Proyecto

Comando	Para qué sirve

localhost:5173	Frontend en desarrollo local
localhost:8080	Backend en desarrollo local
localhost:8080/api/mangadex?path=/manga&query=limit=1	Probar el proxy del backend
https://manvez-backend.onrender.com	Backend en producción (Render)
https://manvez-backend.onrender.com/api/mangadex?path=/manga&query=limit=1	Probar backend en producción

⚠️ Importante — Plan gratuito de Render

El servidor de Render se duerme después de 15 minutos de inactividad. La primera vez que lo abres puede tardar 50 segundos en despertar. Esto es normal en el plan gratuito.


🚀 Cómo Actualizar el Proyecto en Producción

Cuando hagas cambios en el código y quieras verlos en Vercel/Render, solo ejecuta estos 3 comandos:

git add .
git commit -m "descripcion de lo que cambiaste"
git push

✅ Automático

Vercel detecta el push y actualiza el frontend automáticamente. Render hace lo mismo con el backend. No tienes que hacer nada más.


🐛 Errores Comunes y Soluciones

Comando	Para qué sirve

npm: no se reconoce	Node.js no está instalado o no está en el PATH. Reinstala Node.js desde nodejs.org
composer: no se reconoce	Composer no está instalado. Descárgalo desde getcomposer.org
Could not resolve router	El archivo src/router/index.js no existe. Créalo manualmente.
SSL certificate error	PHP no puede verificar HTTPS. Descarga cacert.pem y configúralo en php.ini
Port scan timeout (Render)	El puerto configurado no coincide. Verifica que apache.conf use ${PORT}
Whoops! (CodeIgniter)	Falta permisos en carpeta writable/. Agrega chmod 777 en el Dockerfile
Las portadas no cargan	Cambia la URL de coverUrl en MangaCard.vue para que apunte al backend


📝 Notas Finales

El nombre del logo está en src/components/Navbar.vue — busca la línea con 'MangaApp' o el nombre que pusiste
Para agregar nuevos géneros, edita el array 'genres' en src/views/SearchView.vue con los IDs de MangaDex
El switch de tema claro/oscuro funciona con el botón ☀️/🌙 en la navbar
MangaDex solo devuelve mangas con contenido 'safe' por defecto en este proyecto
El backend está configurado para no verificar SSL localmente (solo en Windows)
