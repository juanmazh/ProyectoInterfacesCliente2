<template>
  <div class="container mt-4">
    <h2 class="mb-4 text-center">Selecciona una Ruta</h2>

    <!-- Opciones de ordenación -->
    <div class="d-flex justify-content-end mb-3">
      <select v-model="ordenSeleccionado" @change="ordenarRutas" class="form-select w-auto">
        <option value="titulo">Ordenar por Título</option>
        <option value="localidad">Ordenar por Localidad</option>
      </select>
    </div>

    <!-- Botón 'Crear Ruta' solo visible para usuarios con rol 'guia' -->
    <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'guia'">
      <RouterLink to="/ruta/crear" class="btn btn-success mb-3">
        <i class="bi bi-plus-circle me-2"></i> Crear Ruta
      </RouterLink>
    </template>

    <ul class="list-group">
      <li v-for="ruta in rutasPaginadas" :key="ruta.id" class="list-group-item d-flex align-items-center justify-content-between mb-3 border-0 rounded shadow-sm">
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
            <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'admin'">
              <button @click="eliminarRuta(ruta.id)" class="btn btn-danger">
                <i class="bi bi-trash me-1"></i>
              </button>
            </template>
            <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'guia'">
              <button @click="duplicarRuta(ruta.id)" class="btn btn-info">
                <i class="bi bi-clipboard-plus-fill"></i>             
              </button>
            </template>
          </div>
        </div>
      </li>
    </ul>

    <!-- Paginación -->
    <nav v-if="totalPaginas > 1" class="mt-3">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: paginaActual === 1 }">
          <button class="page-link" @click="paginaActual--">Anterior</button>
        </li>
        <li v-for="pagina in totalPaginas" :key="pagina" class="page-item" :class="{ active: pagina === paginaActual }">
          <button class="page-link" @click="paginaActual = pagina">{{ pagina }}</button>
        </li>
        <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
          <button class="page-link" @click="paginaActual++">Siguiente</button>
        </li>
      </ul>
    </nav>

    <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
    <p v-if="loading" class="text-center mt-3">Cargando rutas...</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { RouterLink } from 'vue-router';
import Swal from 'sweetalert2';

// Variables reactivas
const rutas = ref([]);
const loading = ref(false);
const error = ref(null);
const ordenSeleccionado = ref('titulo');

// Variables para la paginación
const paginaActual = ref(1);
const rutasPorPagina = 5;

// Calcular rutas paginadas
const rutasPaginadas = computed(() => {
  const inicio = (paginaActual.value - 1) * rutasPorPagina;
  return rutas.value.slice(inicio, inicio + rutasPorPagina);
});

// Calcular total de páginas
const totalPaginas = computed(() => Math.ceil(rutas.value.length / rutasPorPagina));

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

    ordenarRutas();
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
//Dduplicar ruta
const duplicarRuta = async (rutaId) => {
  try {
    // Obtener los datos de la ruta original
    const response = await fetch(`http://localhost:8008/api.php/rutas?id=${rutaId}`);
    if (!response.ok) throw new Error('Error al obtener los datos de la ruta');

    const rutaOriginal = await response.json();
    
    if (!rutaOriginal || !rutaOriginal.id) {
      throw new Error('No se encontraron datos de la ruta.');
    }

    // Mostrar modal para ingresar la nueva fecha
    const { value: nuevaFecha } = await Swal.fire({
      title: 'Duplicar Ruta',
      text: 'Ingrese la nueva fecha para la ruta duplicada:',
      input: 'date',
      inputAttributes: {
        min: new Date().toISOString().split('T')[0] // Evitar fechas pasadas
      },
      showCancelButton: true,
      confirmButtonText: 'Duplicar',
      cancelButtonText: 'Cancelar',
      inputValidator: (value) => {
        if (!value) return 'Debe ingresar una fecha válida';
      }
    });

    if (!nuevaFecha) return; // Si el usuario cancela, no hacer nada

    // Crear nueva ruta con los mismos datos pero diferente fecha
    const nuevaRuta = {
      titulo: rutaOriginal.titulo || '',
      localidad: rutaOriginal.localidad || '',
      descripcion: rutaOriginal.descripcion || '',
      foto: rutaOriginal.foto || '',
      fecha: nuevaFecha,
      hora: rutaOriginal.hora || '',
      latitud: rutaOriginal.latitud || '',
      longitud: rutaOriginal.longitud || '',
      guia_id: rutaOriginal.guia_id || null
    };

    console.log("Datos enviados:", nuevaRuta); // Depuración

    // Enviar la nueva ruta a la API
    const postResponse = await fetch('http://localhost:8008/api.php/rutas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(nuevaRuta)
    });

    const result = await postResponse.json();
    console.log("Respuesta del servidor:", result); // Depuración

    if (!postResponse.ok || result.status === 'error') {
      throw new Error(result.message || 'Error desconocido al duplicar la ruta');
    }

    // Agregar la nueva ruta a la lista
    rutas.value.push(nuevaRuta);

    // Mostrar mensaje de éxito
    Swal.fire({
      icon: 'success',
      title: 'Ruta duplicada',
      text: 'La ruta ha sido duplicada correctamente.',
      confirmButtonText: 'Aceptar'
    });

  } catch (err) {
    console.error('Error:', err.message);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: `Hubo un problema al duplicar la ruta: ${err.message}`,
      confirmButtonText: 'Aceptar'
    });
  }
};


const eliminarRuta = async (rutaId) => {
  try {
    const response = await fetch(`http://localhost:8008/api.php/rutas?id=${rutaId}`, {
      method: 'DELETE',
    });
    if (!response.ok) throw new Error('Error al eliminar la ruta');
    
    rutas.value = rutas.value.filter((ruta) => ruta.id !== rutaId);
    
    Swal.fire({
      icon: 'success',
      title: 'Ruta eliminada',
      text: 'La ruta ha sido eliminada correctamente.',
      confirmButtonText: 'Aceptar'
    });

    // Resetear la paginación si la página actual queda vacía
    if (rutasPaginadas.value.length === 0 && paginaActual.value > 1) {
      paginaActual.value--;
    }
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

<style scoped>
/* Estilos de la paginación */
.pagination .page-item.active .page-link {
  background-color: #3498db;
  border-color: #3498db;
  color: white;
}

.pagination .page-link {
  color: #3498db;
}

.pagination .page-link:hover {
  background-color: #2980b9;
  color: white;
}

/* Botones */
.btn {
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
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

/* Mejoras en la selección de orden */
.form-select {
  width: auto;
  border-radius: 5px;
  background-color: #f0f0f0;
}
</style>
