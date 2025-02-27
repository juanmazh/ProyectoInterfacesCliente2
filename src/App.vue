<template>
  <div class="layout">
    <Header v-if="!isAdminRoute" :usuarioAutenticado="datosSesion" @sesionCerrada="handleSesionCerrada" title="FreeTours" />
    <NavBar :datos="datosSesion"/>
    <RouterView @sesionIniciada="actualizaDatosSesion"></RouterView>
    <RutasView :usuarioAutenticado="usuario" />
    <Footer/>
  </div>
</template>

<script>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import Header from './components/Header.vue';
import NavBar from './components/NavBar.vue';
import Footer from './components/Footer.vue';
import RutasView from './views/RutasView.vue';

export default {
  components: {
    Header,
    NavBar,
    Footer,
  },
  setup() {
    const route = useRoute();
    const datosSesion = ref(null);

    const isAdminRoute = computed(() => route.path === '/admin');

    // Esta función actualiza la sesión en la aplicación
    const actualizaDatosSesion = (datos) => {
      datosSesion.value = datos;
      // Almacenar la sesión en localStorage
      if (datos) {
        localStorage.setItem("sesionUser", JSON.stringify(datos));
      } else {
        localStorage.removeItem("sesionUser");
      }
    };

    // Esta función maneja el cierre de sesión
    const handleSesionCerrada = () => {
      datosSesion.value = null;
      localStorage.removeItem("sesionUser");
    };

    // Obtener la sesión almacenada al cargar la página
    const storedSesion = localStorage.getItem("sesionUser");
    if (storedSesion) {
      try {
        datosSesion.value = JSON.parse(storedSesion);
      } catch (error) {
        console.error("Error al parsear la sesión:", error);
        localStorage.removeItem("sesionUser");
      }
    }

    return {
      datosSesion,
      isAdminRoute,
      actualizaDatosSesion,
      handleSesionCerrada
    };
  }
};
</script>

<style scoped>
.layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
</style>