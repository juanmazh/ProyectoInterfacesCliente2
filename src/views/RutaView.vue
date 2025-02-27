<template>
    <div v-if="ruta" class="container mt-4">
      <h2 class="text-center mb-4">{{ ruta.titulo }}</h2>
  
      <!-- Detalles de la ruta -->
      <div class="row">
        <div class="col-md-6">
          <img :src="ruta.imagen || ruta.foto" class="img-fluid" alt="Imagen ruta">
        </div>
        <div class="col-md-6">
          <h3>Localidad: {{ ruta.localidad }}</h3>
          <p><strong>Descripción:</strong> {{ ruta.descripcion }}</p>
          <p><strong>Duración:</strong> {{ ruta.duracion }} horas</p>
        </div>
      </div>
    </div>
  
    <p v-else class="text-center">Cargando detalles de la ruta...</p>
    <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  
  // Obtener el id de la ruta desde los parámetros de la URL
  const route = useRoute();
  const ruta = ref(null);
  const error = ref(null);
  
  // Obtener el usuario autenticado
  const usuarioAutenticado = JSON.parse(localStorage.getItem('sesionUser')) || null;
  
  // Cargar los detalles de la ruta
  const fetchRuta = async () => {
    try {
      const response = await fetch(`http://localhost:8008/api.php/rutas/${route.params.id}`);
      if (!response.ok) {
        throw new Error('Error al obtener los detalles de la ruta');
      }
      ruta.value = await response.json();
    } catch (err) {
      error.value = err.message;
    }
  };
  
  // Llamar a la función para obtener los detalles de la ruta cuando se monte el componente
  onMounted(fetchRuta);
  </script>
  
  <style scoped>
  .img-fluid {
    max-width: 100%;
    height: auto;
  }
  
  h2 {
    font-size: 2.5rem;
  }
  
  h3 {
    font-size: 1.8rem;
  }
  
  p {
    font-size: 1.2rem;
  }
  
  .btn {
    font-size: 1.2rem;
    padding: 10px 20px;
  }
  </style>
  