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
        <i class="bi bi-plus-circle me-2"></i>
      </RouterLink>
    </template>

    <ul class="list-group">
      <li v-for="ruta in rutas" :key="ruta.id" class="list-group-item d-flex align-items-center justify-content-between mb-3 border-0 rounded shadow-sm">
        <div class="d-flex align-items-center w-100 justify-content-between">
          <img :src="ruta.imagen || ruta.foto" class="img-thumbnail me-3 img-fluid w-25 rounded" alt="Imagen ruta">
          <div class="d-flex flex-column align-items-center justify-content-center w-100 text-center">
            <span class="fw-bold ruta-titulo">{{ ruta.titulo }}</span>
            <span class="fw-bold text-muted">{{ ruta.localidad }}</span>
          </div>
          
          <div class="d-flex">
            <router-link :to="`/ruta/${ruta.id}`" class="btn btn-primary me-2">
              <i class="bi bi-eye me-1"></i>
            </router-link>
            
            <!-- Botón de eliminación solo visible para usuarios 'admin' -->
            <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'admin'">
              <button @click="eliminarRuta(ruta.id)" class="btn btn-danger">
                <i class="bi bi-trash me-1"></i>
              </button>
            </template>
             <!-- Botón de duplicar solo visible para usuarios 'guia' -->
             <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'guia'">
              <button @click="duplicarRuta(ruta.id)" class="btn btn-info">
                <i class="bi bi-clipboard-plus-fill"></i>             
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

// Obtener el usuario autenticado del localStorage para proteger la vista
const usuarioAutenticado = JSON.parse(localStorage.getItem('sesionUser')) || null;

const fetchRutas = async () => {
  loading.value = true;
  try {
    const response = await fetch('http://localhost:8008/api.php/rutas');
    if (!response.ok) throw new Error('Error al obtener las rutas');
    const data = await response.json();
    
    // Filtrar rutas que ya han pasado
    const now = new Date();
    rutas.value = data.filter(ruta => {
      const rutaFecha = new Date(`${ruta.fecha}T${ruta.hora}`);
      return rutaFecha >= now;
    });

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
//Mirar el codigo porque no funciona del todo, hay que repasarlo
const duplicarRuta = async (rutaId) => {
  try {
    const { value: fecha } = await Swal.fire({
      title: 'Especifica la fecha de la ruta duplicada',
      html: `<input id="fechaRuta" type="date" class="swal2-input" placeholder="Fecha de la ruta">`,
      showCancelButton: true,
      confirmButtonText: 'Duplicar',
      cancelButtonText: 'Cancelar',
      preConfirm: () => {
        const fechaRuta = document.getElementById('fechaRuta').value;
        if (!fechaRuta) {
          Swal.showValidationMessage('La fecha es obligatoria');
          return false;
        }
        const fechaActual = new Date().toISOString().split('T')[0];
        if (fechaRuta < fechaActual) {
          Swal.showValidationMessage('La fecha no puede ser anterior a la actual');
          return false;
        }
        return fechaRuta;
      }
    });

    if (!fecha) return; // Si el usuario cancela el modal, no se hace nada

    const response = await fetch(`http://localhost:8008/api.php/rutas?id=${rutaId}`);
    if (!response.ok) throw new Error('Error al duplicar la ruta');
    const data = await response.json();
    const rutaDuplicada = { ...data, fecha };

    rutas.value = [...rutas.value, rutaDuplicada];
    Swal.fire({
      icon: 'success',
      title: 'Ruta duplicada',
      text: 'La ruta ha sido duplicada correctamente.',
      confirmButtonText: 'Aceptar'
    });
  } catch (err) {
    error.value = err.message;
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Hubo un error al duplicar la ruta. Intenta nuevamente.',
      confirmButtonText: 'Aceptar'
    });
  }
};
fetchRutas();
</script>

<style scoped>
/* Mejora de estilo */
.btn {
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
}

.btn-primary {
  background-color: #3498db;
  border-color: #3498db;
}

.btn-primary:hover {
  background-color: #2980b9;
  border-color: #2980b9;
}

.btn-success {
  background-color: #2ecc71;
  border-color: #2ecc71;
}

.btn-success:hover {
  background-color: #27ae60;
  border-color: #27ae60;
}

.btn-danger {
  background-color: #e74c3c;
  border-color: #e74c3c;
}

.btn-danger:hover {
  background-color: #c0392b;
  border-color: #c0392b;
}

/* Imagen de las rutas */
.img-thumbnail {
  max-height: 150px;
  object-fit: cover;
}

/* Estilos de la lista */
.list-group-item {
  background-color: #f8f9fa;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.ruta-titulo {
  font-size: 1.1rem;
  color: #333;
}

.text-muted {
  color: #6c757d;
}

li {
  border-radius: 10px;
}

/* Mejoras en la selección de orden */
.form-select {
  width: auto;
  border-radius: 5px;
  background-color: #f0f0f0;
}
</style>
