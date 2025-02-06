<script setup>
import router from "@/router";
import {ref} from "vue"

const emit = defineEmits(["sesionCerrada"]);

const props= defineProps({
  title: String,
  usuarioAutenticado: Object
});




function cerrarSesion() {
  
  
  emit("sesionCerrada");
  router.push({name:"home"});



}
</script>


<template>
    <!--<header class="bg-dark text-white  p-3">
      <div class="row">
        <h1 class="col-8">{{ title }}</h1>
        <div class="col-4">
        
        <div v-if="usuarioAutenticado" class="container text-end">
          <span>Bienvenido, {{ usuarioAutenticado.usuario }} ({{ usuarioAutenticado.rol }})</span>
          <button @click.prevent="cerrarSesion" class="btn btn-danger">Cerrar Sesión</button>
        </div>
      </div>
      </div>
  </header>-->


  <!--Tengo que cambiar los anchor por como se hace en vue-->
  <!--Falta implementar mejoras al estilo-->
  <header class="p-3 text-bg-dark">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
          <RouterLink to="/" class="d-flex align-items-left mb-2 mb-lg-0 text-white text-decoration"><p>FreeTours</p></RouterLink>

          <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
            <RouterLink to="/admin"><li class="nav-link px-2 text-white">Gestion de usuario</li></RouterLink>
            <li><a href="#" class="nav-link px-2 text-white">Acerca de</a></li>
          </ul>

          <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
            <input type="search" class="form-control form-control-dark text-bg-dark" placeholder="Buscar..." aria-label="Buscar">
          </form>

          <div class="text-end">
     
            <RouterLink to="/login"><button type="button" class="btn btn-outline-light me-2">Iniciar sesión</button></RouterLink>
            <RouterLink to="/register"><button type="button" class="btn btn-warning">Regístrate</button></RouterLink>
          </div>
        </div>
      </div>
    </header>
</template>
  
 
<style scoped>

</style>
  