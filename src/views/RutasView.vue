<template>
  <div class="container mt-4">
    <h2 class="mb-4 text-center">Selecciona una Ruta</h2>

    <!-- Opciones de ordenación en la esquina superior derecha -->
    <div class="d-flex justify-content-end mb-3">
      <select v-model="ordenSeleccionado" @change="ordenarRutas" class="form-select w-auto">
        <option value="titulo">Ordenar por Título</option>
        <option value="localidad">Ordenar por Localidad</option>
      </select>
    </div>
    <!--Hacer /ruta/crear y /ruta/id/editar pensar como hacerlo-->
    <!-- Botón 'Crear Ruta' solo visible para usuarios con rol 'guia' -->
    <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'guia'">
      <router-link to="/ruta/crear" class="btn btn-success mb-3">
        Crear Ruta
      </router-link>
    </template>

    <ul class="list-group">
      <li v-for="ruta in rutas" :key="ruta.id"
        class="list-group-item d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center w-100 justify-content-between">
          <img :src="ruta.imagen || ruta.foto" class="img-thumbnail me-3 img-large" alt="Imagen ruta">
          <div class="d-flex flex-column align-items-center justify-content-center w-100 text-center">
            <span class="fw-bold ruta-titulo">{{ ruta.titulo }}</span>
            <span class="fw-bold">{{ ruta.localidad }}</span>
          </div>
          <router-link :to="`/ruta/${ruta.id}`" class="btn btn-primary">
            Ver más
          </router-link>


          <!-- Botón de editar visible solo si es guía -->
          <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'guia'">
            <router-link :to="`/ruta/${ruta.id}/editar`" class="btn btn-primary">
              Editar
            </router-link>
          </template>
        </div>
      </li>
    </ul>

    <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
    <p v-if="loading" class="text-center mt-3">Cargando rutas...</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Variables reactivas
const rutas = ref([]);
const loading = ref(false);
const error = ref(null);
const ordenSeleccionado = ref('titulo');
// Obtener el usuario autenticado del localStorage
const usuarioAutenticado = JSON.parse(localStorage.getItem('sesionUser')) || null;

const fetchRutas = async () => {
  loading.value = true;
  try {
    const response = await fetch('http://localhost:8008/api.php/rutas');
    if (!response.ok) throw new Error('Error al obtener las rutas');
    rutas.value = await response.json();
    ordenarRutas(); // Ordenar las rutas inmediatamente después de cargarlas
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};
const ordenarRutas = () => {
  if (ordenSeleccionado.value === 'titulo') {
    rutas.value.sort((a, b) => a.titulo.localeCompare(b.titulo));
  } else if (ordenSeleccionado.value === 'localidad') {
    rutas.value.sort((a, b) => a.localidad.localeCompare(b.localidad));
  }
};

fetchRutas();
</script>
