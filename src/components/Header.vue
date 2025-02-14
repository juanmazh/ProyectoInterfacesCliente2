<script setup>
import { useRouter } from 'vue-router';

const emit = defineEmits(['sesionCerrada']);

const props = defineProps({
  title: String,
  usuarioAutenticado: Object
});

const router = useRouter();

function cerrarSesion() {
  localStorage.removeItem('sesion'); 
  emit('sesionCerrada');  
  router.push({ name: 'home' }); 
}
</script>

<template>
  <header class="p-3 text-bg-dark">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <RouterLink to="/" class="d-flex align-items-left mb-2 mb-lg-0 text-white text-decoration"><p>{{ title }}</p></RouterLink>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <RouterLink to="/admin"><li class="nav-link px-2 text-white">Gestión de usuario</li></RouterLink>
          <li><RouterLink to="/acerca" class="nav-link px-2 text-white">Acerca de</RouterLink></li>
        </ul>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          <input type="search" class="form-control form-control-dark text-bg-dark" placeholder="Buscar..." aria-label="Buscar">
        </form>

        <div class="text-end">
          <template v-if="usuarioAutenticado">
            <span class="text-white me-2">{{ usuarioAutenticado.name }}</span>
            <button @click="cerrarSesion" class="btn btn-outline-light me-2">Cerrar sesión</button>
          </template>
          <template v-else>
            <RouterLink to="/login" class="btn btn-outline-light me-2">Iniciar sesión</RouterLink>
            <RouterLink to="/register" class="btn btn-warning">Registrarse</RouterLink>
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
/* Estilos personalizados */
</style>