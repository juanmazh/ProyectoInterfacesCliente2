<template>
    <div class="container mt-4">
      <h2 class="mb-4 text-center">Selecciona una Ruta</h2>
      <ul class="list-group">
        <li 
          v-for="ruta in rutas" 
          :key="ruta.id" 
          class="list-group-item d-flex align-items-center justify-content-between"
        >
          <div class="d-flex align-items-center w-100 justify-content-between">
            <!-- Imagen más grande -->
            <img :src="ruta.imagen || ruta.foto" class="img-thumbnail me-3 img-large" alt="Imagen ruta">
            <div class="d-flex flex-column align-items-center justify-content-center w-100 text-center">
              <!-- Título centrado -->
              <span class="fw-bold ruta-titulo">{{ ruta.titulo }}</span>
              <span class="fw-bold">{{ ruta.localidad }}</span>
            </div>
            <router-link :to="`/ruta/${ruta.id}`" class="btn btn-primary">
              Ver más
            </router-link>
  
            <!-- Solo se muestra si el usuario está autenticado y es 'guia' -->
            <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'guia'">
              <router-link :to="`/ruta/${ruta.id}/editar`" class="btn btn-primary">
                Editar
              </router-link>
            </template>
            <!-- Si no es 'guia', muestra un mensaje de depuración -->
            <p v-if="usuarioAutenticado && usuarioAutenticado.rol !== 'guia'" class="text-muted">Acción no disponible para este rol</p>
          </div>
        </li>
      </ul>
      <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
      <p v-if="loading" class="text-center mt-3">Cargando rutas...</p>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      usuarioAutenticado: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        rutas: [],
        loading: false,
        error: null,
      };
    },
    mounted() {
      this.fetchRutas(); // Usamos mounted en lugar de created
    },
    methods: {
      async fetchRutas() {
        this.loading = true;
        this.error = null;
  
        try {
          const response = await fetch('http://localhost:8008/api.php/rutas', {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
            },
          });
  
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
  
          const data = await response.json();
          console.log('Rutas:', data);
          this.rutas = data;
        } catch (error) {
          this.error = error.message;
        } finally {
          this.loading = false;
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .img-thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
  }
  
  .img-large {
    width: 120px; /* Aumenta el tamaño de la imagen */
    height: 120px; /* Aumenta el tamaño de la imagen */
    object-fit: cover;
  }
  
  .list-group-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .ruta-titulo {
    font-size: 1.5rem; /* Tamaño de fuente mayor para el título */
    font-weight: bold;
    margin-bottom: 5px; /* Añadir margen para separar los elementos */
    line-height: 1.2;
  }
  
  /* Aseguramos que el contenedor del título y localidad esté centrado correctamente */
  .d-flex {
    display: flex;
  }
  
  .align-items-center {
    align-items: center;
  }
  
  .justify-content-center {
    justify-content: center;
  }
  
  /* Aseguramos que el contenedor de imagen y texto ocupe toda la fila */
  .w-100 {
    width: 100%;
  }
  </style>
  