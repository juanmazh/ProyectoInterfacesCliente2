<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Resultados de Búsqueda</h1>
      <div v-if="rutas.length">
        <div class="row">
          <div class="col-md-4" v-for="ruta in rutas" :key="ruta.id">
            <div class="card mb-4">
              <img :src="ruta.foto" class="card-img-top" :alt="ruta.titulo">
              <div class="card-body">
                <h5 class="card-title">{{ ruta.titulo }}</h5>
                <p class="card-text">{{ ruta.descripcion }}</p>
                <RouterLink :to="{ name: 'ruta', params: { id: ruta.id } }" class="btn btn-primary">Ver más</RouterLink>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <p class="text-center">No se encontraron rutas para "{{ searchQuery }}"</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue';
  import { useRoute } from 'vue-router';
  
  const route = useRoute();
  const searchQuery = ref(route.query.q || '');
  const rutas = ref([]);
  
  const fetchRutas = async () => {
    try {
      const response = await fetch(`http://localhost:8008/api.php/rutas?search=${searchQuery.value}`);
      if (!response.ok) throw new Error('Error al obtener las rutas');
      rutas.value = await response.json();
    } catch (err) {
      console.error(err);
    }
  };
  
  onMounted(fetchRutas);
  
  watch(() => route.query.q, (newQuery) => {
    searchQuery.value = newQuery;
    fetchRutas();
  });
  </script>
  
  <style scoped>
  .container {
    max-width: 1200px;
  }
  
  .card {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .card-img-top {
    height: 200px;
    object-fit: cover;
  }
  </style>