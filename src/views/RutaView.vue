<template>
  <div v-if="ruta" class="container mt-5">
    <h2 class="text-center mb-4">{{ ruta.titulo }}</h2>

    <div class="row align-items-center">
      <!-- Columna de la imagen -->
      <div class="col-md-6 text-center">
        <img v-if="ruta.foto" :src="ruta.foto" class="img-fluid rounded shadow" alt="Imagen de la ruta">
        <p v-else class="text-muted">No hay imagen disponible</p>
      </div>

      <!-- Columna de los detalles -->
      <div class="col-md-6">
        <h4><strong>Localidad:</strong> {{ ruta.localidad }}</h4>
        <p><strong>Descripción:</strong> {{ ruta.descripcion }}</p>
        <p><strong>Fecha:</strong> {{ ruta.fecha }}</p>
        <p><strong>Hora:</strong> {{ ruta.hora }}</p>
        <p><strong>Duración:</strong> {{ ruta.duracion }} horas</p>
        <p><strong>Coordenadas:</strong> Latitud {{ ruta.latitud }}, Longitud {{ ruta.longitud }}</p>
      </div>
    </div>
  </div>

  <p v-else class="text-center">Cargando detalles de la ruta...</p>
  <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

// Obtener el id de la ruta desde la URL
const route = useRoute();
const ruta = ref(null);
const error = ref(null);

// Obtener los detalles de la ruta desde la API
const fetchRuta = async () => {
  try {
    const response = await fetch(`http://localhost:8008/api.php/rutas?id=${route.params.id}`);
    if (!response.ok) {
      throw new Error('Error al obtener los detalles de la ruta');
    }
    ruta.value = await response.json();
  } catch (err) {
    error.value = err.message;
  }
};

// Cargar los datos al montar el componente
onMounted(fetchRuta);
</script>

<style scoped>
.container {
  max-width: 1700px;
  background: #f8f9fa;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 3rem;
}

h4, p {
  font-size: 1.6rem;
}

.img-fluid {
  max-width: 100%;
  height: auto;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>
