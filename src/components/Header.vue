<template>
  <!--Añadir menú hamburguesa para facilitar responsividad-->
  <header class="p-3 bg-dark text-white shadow">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <RouterLink to="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
          <h1 class="h4">ApatitaTours</h1>
        </RouterLink>

        <ul class="nav col-12 col-lg-auto mb-2 justify-content-center mb-md-0">
          <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'admin'">
            <RouterLink to="/admin" class="nav-link px-2 text-white gestion-usuario">Gestión de usuario</RouterLink>
          </template>
          <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'guia'">
            <RouterLink to="/guia" class="nav-link px-2 text-white gestion-usuario">Gestión de guías</RouterLink>
          </template>
          <template v-if="usuarioAutenticado">
            <RouterLink to="/rutas" class="nav-link px-2 text-white gestion-usuario">Descubrir rutas</RouterLink>
          </template>
          <template v-if="usuarioAutenticado && usuarioAutenticado.rol === 'cliente'">
            <RouterLink to="/reservas" class="nav-link px-2 text-white gestion-usuario">Mis Rutas</RouterLink>
          </template>
          <template v-if="usuarioAutenticado">
            <RouterLink to="/valorar" class="nav-link px-2 text-white gestion-usuario">Valorar</RouterLink>
          </template>
          <li><RouterLink to="/acerca" class="nav-link px-2 text-white">Acerca de</RouterLink></li>
        </ul>

        <form class="d-flex col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search" @submit.prevent="handleSearch">
          <input type="search" class="form-control form-control-dark text-bg-dark me-2" placeholder="Buscar..." aria-label="Buscar" v-model="searchQuery">
          <button class="btn btn-outline-light" type="submit">Buscar</button>
        </form>

        <div class="text-end">
          <template v-if="usuarioAutenticado">
            <span class="text-white me-2">Bienvenido, {{ usuarioAutenticado.nombre }} ({{ usuarioAutenticado.rol }})</span>
            <button @click.prevent="cerrarSesion" class="btn btn-danger">Cerrar Sesión</button>
          </template>
          <template v-else>
            <RouterLink to="/login" class="btn btn-outline-light me-2">Iniciar sesión</RouterLink>
            <RouterLink to="/register" class="btn btn-aquamarine">Registrarse</RouterLink>          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const emit = defineEmits(['sesionCerrada']);

const props = defineProps({
  title: String,
  usuarioAutenticado: Object
});

const router = useRouter();
const searchQuery = ref('');

function cerrarSesion() {
  localStorage.removeItem('sesionUser'); 
  emit('sesionCerrada');  
  router.push({ name: 'home' }); 
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'buscar', query: { q: searchQuery.value } });
  }
};
</script>

<style scoped>
/* Estilos personalizados */
.gestion-usuario {
  background-color: #007bff;
  color: white;
  padding: 10px 15px;
  border-radius: 5px;
  margin: 0 5px;
  transition: background-color 0.3s ease;
}

.gestion-usuario:hover {
  background-color: #0056b3;
}

header {
  background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 30%, rgba(0,212,255,0.8) 100%);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.nav-link {
  font-weight: bold;
}

.form-control-dark {
  background-color: #343a40;
  border: none;
  color: white;
}

.form-control-dark::placeholder {
  color: #adb5bd;
}
/*Estilos de botones*/
.btn-aquamarine {
  background-color: rgb(4, 176, 119);
  color: white;
  border: none;
}

.btn-aquamarine:hover {
  background-color: #1d1067;
}
.btn-outline-light {
  border-color: #fff;
  color: #fff;
}

.btn-outline-light:hover {
  background-color: #fff;
  color: #000;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
  border: none;
}

.btn-danger:hover {
  background-color: #c82333;
}

@media (max-width: 992px) {
  .navbar-collapse {
    background-color: #343a40;
  }
}
</style>
