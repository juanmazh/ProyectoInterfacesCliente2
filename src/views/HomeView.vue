<template>
  <div class="home-container">
    <div class="overlay">
      <div class="content">
        <h1 class="title">Bienvenido a <span class="highlight">FreeTours</span></h1>
        <p class="subtitle">Explora las mejores rutas turísticas con guías expertos, ¡totalmente gratis!</p>
        
        <router-link to="/rutas">
          <button class="explore-button">Explorar rutas</button>
        </router-link>
      </div>
    </div>

    <div class="carousel-container">
      <Carusel />
    </div>

    <!-- Sección de Valoraciones -->
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

<script>
import Carusel from '@/components/Carusel.vue';

export default {
  components: {
    Carusel
  },
  data: () => ({
    valoraciones: []
  }),
  mounted() {
    this.obtenerValoraciones();
  },
  methods: {
    async obtenerValoraciones() {
      try {
        const response = await fetch('http://localhost:8008/api.php/valoraciones');
        if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
        this.valoraciones = await response.json();
      } catch (error) {
        console.error('Error al obtener las valoraciones:', error);
      }
    }
  }
};
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
.card-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 10px;
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
