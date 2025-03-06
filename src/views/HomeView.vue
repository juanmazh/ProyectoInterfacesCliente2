<template>
  <div class="home-container">
    <div class="overlay">
      <div class="content">
        <h1 class="title">Bienvenido a <span class="highlight">ApatitaTours</span></h1>
        <p class="subtitle">Explora las mejores rutas turísticas con guías expertos, ¡totalmente gratis!</p>

        <router-link to="/rutas">
          <button class="explore-button">Explorar rutas</button>
        </router-link>
      </div>
    </div>

    <div class="carousel-container">
      <Carusel />
    </div>

    <!-- Nueva Sección de Texto y Video -->
    <div class="info-container container mt-5">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h2 class="info-title">Descubre nuestras rutas</h2>
          <p class="info-text">
            En ApatitaTours, te llevamos a los lugares más impresionantes con guías expertos.
            ¡Descubre paisajes inolvidables y vive una experiencia única!
          </p>
        </div>
        <div class="col-md-6">
          <!--Echar un ojo a esto porque no funciona-->
          <div class="video-container">
            <video id="video" width="100%" height="315" src="../assets/video/video.mp4"></video>
            <div class="controles">
              <button id="play" class="btn btn-primary"><i class="bi bi-play-fill"></i></button>
              <button id="pausa" class="btn btn-danger"><i class="bi bi-pause-circle"></i></button>
              <button id="rewind" class="btn btn-secondary"><i class="bi bi-caret-left-fill"></i></button>
              <button id="forward" class="btn btn-secondary"><i class="bi bi-caret-right-fill"></i></button>
              <button id="fullscreen" class="btn btn-secondary"><i class="bi bi-fullscreen"></i></button>
              <input type="range" value="1" min="0" max="1" step="0.1" id="volumen">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sección de Valoraciones-->
    <div class="valoraciones-container" v-if="valoraciones.length">
      <h2 class="valoraciones-title">Opiniones de nuestros viajeros</h2>
      <div class="row">
        <div class="col-md-4" v-for="valoracion in valoraciones" :key="valoracion.id">
          <div class="card mb-3 valoracion-card">
            <div class="card-body">
              <h5 class="card-title">{{ valoracion.ruta_titulo }}</h5>
              <p class="card-text">"{{ valoracion.comentario }}"</p>
              <p class="card-text"><small class="text-muted">Puntuación: {{ valoracion.puntuacion }}/5</small></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Carusel from '../components/Carusel.vue';

const valoraciones = ref([]);

const obtenerValoraciones = async () => {
  try {
    const response = await fetch("http://localhost:8008/api.php/valoraciones");
    if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
    valoraciones.value = await response.json();
  } catch (error) {
    console.error("Error al obtener las valoraciones:", error);
  }
};

onMounted(() => {
  obtenerValoraciones();

  //Botones del video
  const video = document.getElementById("video");
  const btnPlay = document.getElementById("play");
  const btnPause = document.getElementById("pausa");
  const volumen = document.getElementById("volumen");
  const btnFullscreen = document.getElementById("fullscreen");
  const btnRewind = document.getElementById("rewind");
  const btnForward = document.getElementById("forward");

  try {
    if (btnPlay && btnPause && video && volumen && btnFullscreen && btnRewind && btnForward) {
      btnPlay.addEventListener("click", () => {
        video.play();
      });
      btnPause.addEventListener("click", () => {
        video.pause();
      });
      volumen.addEventListener("change", () => {
        video.volume = volumen.value;
      });
      btnFullscreen.addEventListener("click", () => {
        if (video.requestFullscreen) {
          video.requestFullscreen();
        } else if (video.mozRequestFullScreen) { /* Firefox */
          video.mozRequestFullScreen();
        } else if (video.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
          video.webkitRequestFullscreen();
        } else if (video.msRequestFullscreen) { /* IE/Edge */
          video.msRequestFullscreen();
        }
      });
      btnRewind.addEventListener("click", () => {
        video.currentTime -= 5;
      });
      btnForward.addEventListener("click", () => {
        video.currentTime += 5;
      });
    } else {
      throw new Error("Elementos del video no encontrados");
    }
  } catch (error) {
    console.error("Error al agregar eventos a los botones del video:", error);
  }
});
</script>


<style scoped>
/********** Estilos de Home **********/
.home-container {
  position: relative;
  width: 100%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  color: rgb(0, 0, 0);
  background: white;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.4);
  border-radius: 20px;
  padding: 20px;
  margin: auto;
}

.overlay {
  padding: 50px 20px;
}

.title {
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 10px;
}

.highlight {
  color: #2c6bc9;
}

.subtitle {
  font-size: 1.4rem;
  max-width: 600px;
  margin: 0 auto 20px;
}

.explore-button {
  background-color: #181b80;
  color: #cfcfcf;
  font-size: 1.2rem;
  font-weight: bold;
  padding: 12px 24px;
  border: 2px solid;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease-in-out;
}

.explore-button:hover {
  background-color: rgb(4, 176, 119);
  transition: background 0.3s ease-in-out;
  transform: scale(1.05);
}

.carousel-container {
  width: 90%;
  max-width: 1200px;
}

/********** Estilos de Info **********/
.info-container {
  width: 90%;
  max-width: 1200px;
  text-align: left;
  padding: 20px;
}

.info-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 10px;
}

.info-text {
  font-size: 1.2rem;
  color: #444;
}

.video-container {
  text-align: center;
}

.video-container iframe {
  border-radius: 12px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
}

/********** Estilos de Valoraciones **********/
.valoraciones-container {
  width: 90%;
  max-width: 1200px;
  margin-top: 40px;
  text-align: center;
}

.valoraciones-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 20px;
}

.valoracion-card {
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.valoracion-card:hover {
  transform: scale(1.05);
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
}
/* Estilos para el video */
.video-container {
  position: relative;
  max-width: 800px;
  margin: auto;
  background: #000;
  padding: 10px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}

.controls {
  display: flex;
  justify-content: space-around;
  align-items: center;
  margin-top: 10px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.btn-primary {
  background-color: #14406e;
  color: white;
}

.btn-danger {
  background-color: #3374eb;
  color: white;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

input[type="range"] {
  width: 100px;
}
</style>
