<template>
    <div class="container mt-4">
      <h2 class="mb-4 text-center">Selecciona una Ruta</h2>
      <ul class="list-group">
        <li 
          v-for="ruta in rutas" 
          :key="ruta.id" 
          class="list-group-item d-flex align-items-center justify-content-between"
        >
          <div class="d-flex align-items-center">
            <img :src="ruta.imagen || 'placeholder.jpg'" class="img-thumbnail me-3" alt="Imagen ruta">
            <span class="fw-bold">{{ ruta.nombre }}</span>
          </div>
          <router-link :to="`/ruta/${ruta.id}`" class="btn btn-primary">
            Ver más
          </router-link>
        </li>
      </ul>
      <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
      <p v-if="loading" class="text-center mt-3">Cargando rutas...</p>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        rutas: [],
        loading: false,
        error: null
      };
    },
    methods: {
      async fetchRutas() {
        this.loading = true;
        this.error = null;
  
        try {
          const response = await fetch('http://localhost:8008/api.php/rutas', {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',  // Aseguramos el tipo de contenido
            },
          });
  
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
  
          const data = await response.json();
          this.rutas = data;  // Ajusta según la estructura exacta de la API
  
        } catch (err) {
          this.error = err.message;
        } finally {
          this.loading = false;
        }
      }
    },
    mounted() {
      this.fetchRutas();
    }
  };
  </script>
  
  <style scoped>
  .img-thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
  }
  .list-group-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  </style>
  