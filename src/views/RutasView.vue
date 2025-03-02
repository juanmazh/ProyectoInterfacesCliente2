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
    
    <!-- Botón 'Crear Ruta' solo visible para usuarios con rol 'guia' -->
    <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'guia'">
      <RouterLink to="/ruta/crear" class="btn btn-success mb-3">
        Crear Ruta
      </RouterLink>
    </template>

    <ul class="list-group">
      <li v-for="ruta in rutas" :key="ruta.id" class="list-group-item d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center w-100 justify-content-between">
          <img :src="ruta.imagen || ruta.foto" class="img-thumbnail me-3 img-fluid w-25" alt="Imagen ruta">
          <div class="d-flex flex-column align-items-center justify-content-center w-100 text-center">
            <span class="fw-bold ruta-titulo">{{ ruta.titulo }}</span>
            <span class="fw-bold">{{ ruta.localidad }}</span>
          </div>
          
          <div class="d-flex">
            <router-link :to="`/ruta/${ruta.id}`" class="btn btn-primary me-2">
              Ver más
            </router-link>
            
            <!-- Botón de eliminación solo visible para usuarios 'admin' -->
            <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'admin'">
              <button @click="eliminarRuta(ruta.id)" class="btn btn-danger">
                Eliminar
              </button>
            </template>
          </div>
        </div>
      </li>
    </ul>

    <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
    <p v-if="loading" class="text-center mt-3">Cargando rutas...</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import Swal from 'sweetalert2';

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

const eliminarRuta = async (rutaId) => {
  try {
    const response = await fetch(`http://localhost:8008/api.php/rutas?id=${rutaId}`, {
      method: 'DELETE',
    });
    if (!response.ok) throw new Error('Error al eliminar la ruta');
    
    // Eliminar la ruta de la lista local
    rutas.value = rutas.value.filter((ruta) => ruta.id !== rutaId);
    
    // Mostrar la alerta de SweetAlert2
    Swal.fire({
      icon: 'success',
      title: 'Ruta eliminada',
      text: 'La ruta ha sido eliminada correctamente.',
      confirmButtonText: 'Aceptar'
    });
  } catch (err) {
    error.value = err.message;
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Hubo un error al eliminar la ruta. Intenta nuevamente.',
      confirmButtonText: 'Aceptar'
    });
  }
};

fetchRutas();
</script>
