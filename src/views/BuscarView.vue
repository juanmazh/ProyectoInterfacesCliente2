<!--Vista despues de buscar-->
<template>
  <div class="container mt-5">
    <h1 class="text-center mb-4">Resultados de Búsqueda</h1>
    <div v-if="rutas.length">
      <div class="row">
        <div class="col-md-4" v-for="ruta in rutas" :key="ruta.id">
          <div class="card mb-4 shadow-sm">
            <img :src="ruta.foto" class="card-img-top" :alt="ruta.titulo">
            <div class="card-body">
              <h5 class="card-title">{{ ruta.titulo }}</h5>
              <p class="card-text text-muted">{{ ruta.descripcion }}</p>
              <RouterLink :to="{ name: 'ruta', params: { id: ruta.id } }" class="btn btn-primary w-100">Ver más</RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      <p class="text-center text-muted">No se encontraron rutas para "{{ searchQuery }}"</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const searchQuery = ref(route.query.q || '');
const rutas = ref([]);
//Llamada a la API para obtener todas las rutas, en proceso de cambiar
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
/* Estilos para las tarjetas de rutas */
.container {
  max-width: 1200px;
}

.card {
  border-radius: 10px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-img-top {
  height: 200px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.card-img-top:hover {
  transform: scale(1.05);
}

.card-body {
  padding: 1.25rem;
}

.card-title {
  font-size: 1.2rem;
  font-weight: bold;
}

.card-text {
  font-size: 0.9rem;
  color: #555;
}

.btn {
  font-size: 1rem;
  border-radius: 5px;
}

.text-muted {
  font-size: 1.1rem;
}
</style>
