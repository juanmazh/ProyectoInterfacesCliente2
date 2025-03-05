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
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/D-F4L5Gfhik?autoplay=1"
              title="Walking from Myeongdong to Dongdaemun on a summer night | Walking Tour Seoul 4K HDR"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
            </iframe>
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
import Carusel from '@/components/Carusel.vue';
import { ref, onMounted } from "vue";

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

onMounted(obtenerValoraciones);
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
  margin: 20px;
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
</style>
