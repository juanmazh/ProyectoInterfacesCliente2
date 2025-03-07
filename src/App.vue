<template>
  <div class="layout">
    <!-- Mostrar el Header solo si no es la ruta de administrador -->
    <Header v-if="!isAdminRoute" :usuarioAutenticado="datosSesion" @sesionCerrada="handleSesionCerrada" title="FreeTours" />
    <!-- Vista de rutas -->
    <RouterView @sesionIniciada="actualizaDatosSesion" />
    <Footer />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';

// Estado de la sesión del usuario
const datosSesion = ref(null);
const route = useRoute();

// Verifica si estamos en la ruta de administración
const isAdminRoute = route.path === '/admin';

// Actualiza los datos de la sesión
const actualizaDatosSesion = (datos) => {
  datosSesion.value = datos;
  if (datos) {
    localStorage.setItem('sesionUser', JSON.stringify(datos));
  } else {
    localStorage.removeItem('sesionUser');
  }
};

// Maneja el cierre de sesión
const handleSesionCerrada = () => {
  datosSesion.value = null;
  localStorage.removeItem('sesionUser');
};

// Cargar la sesión almacenada en el localStorage directamente al inicializar la aplicación
const storedSesion = localStorage.getItem('sesionUser');
if (storedSesion) {
  try {
    datosSesion.value = JSON.parse(storedSesion);
  } catch (error) {
    console.error('Error al cargar la sesión:', error);
    localStorage.removeItem('sesionUser');
  }
}
</script>

<style scoped>
.layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
</style>
