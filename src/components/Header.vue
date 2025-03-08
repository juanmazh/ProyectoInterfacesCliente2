<template>
  <header class="p-3 bg-dark text-white shadow">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between">
        <RouterLink to="/" class="d-flex align-items-center text-white text-decoration-none">
          <h1 class="h4">ApatitaTours</h1>
        </RouterLink>

        <!-- Botón de menú hamburguesa (solo en móviles) -->
        <button class="navbar-toggler d-lg-none" type="button" @click="menuAbierto = !menuAbierto">
          <span class="navbar-toggler-icon">&#9776;</span>
        </button>

        <!-- Menú de navegación -->
        <nav :class="['nav-menu', { 'show': menuAbierto }]">
          <ul class="nav flex-column flex-lg-row text-center text-lg-start">
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
        </nav>

        <form class="d-none d-lg-flex col-lg-auto me-lg-3" role="search" @submit.prevent="handleSearch">
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
            <RouterLink to="/register" class="btn btn-aquamarine">Registrarse</RouterLink>
          </template>
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
  usuarioAutenticado: Object
});

const router = useRouter();
const searchQuery = ref('');
const menuAbierto = ref(false);

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
  margin: 5px;
  transition: background-color 0.3s ease;
}

.gestion-usuario:hover {
  background-color: #0056b3;
}

header {
  background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 30%, rgba(0,212,255,0.8) 100%);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Botón hamburguesa */
.navbar-toggler {
  background: none;
  border: none;
  color: white;
  font-size: 1.8rem;
}

.navbar-toggler-icon {
  display: inline-block;
  cursor: pointer;
}

/* Menú responsivo */
.nav-menu {
  display: flex; /* Siempre visible en pantallas grandes */
}

@media (max-width: 992px) {
  .nav-menu {
    display: none;
  }

  .nav-menu.show {
    display: block;
    position: fixed;
    top: 60px;
    left: 0;
    width: 100%;
    background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 30%, rgba(0,212,255,0.8) 100%);
    padding: 15px;
    z-index: 1000;
  }
}

.nav {
  list-style: none;
  padding: 0;
}

.nav-link {
  font-weight: bold;
  display: block;
  padding: 10px;
}

/* Formularios y botones */
.form-control-dark {
  background-color: #343a40;
  border: none;
  color: white;
}

.form-control-dark::placeholder {
  color: #adb5bd;
}

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

/* Responsividad */
@media (max-width: 990px) {
  .nav-menu {
    text-align: center;
  }

  .nav-menu ul {
    flex-direction: column;
  }

  .d-lg-flex {
    display: none !important;
  }
}
</style>
