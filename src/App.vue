<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import Header from './components/Header.vue';
import NavBar from './components/NavBar.vue';
import Footer from './components/Footer.vue';

const datosSesion = ref(null);

const route = useRoute();

const isAdminRoute = computed(() => route.path === '/admin');

const actualizaDatosSesion = (datos) => {
  datosSesion.value = datos;
};

if (localStorage.getItem("sesion")) {
  datosSesion.value = JSON.parse(localStorage.getItem("sesion"));
} else {
  localStorage.removeItem("sesion");
}
</script>

<template>
  <div class="layout">
    <Header v-if="!isAdminRoute" :usuarioAutenticado="datosSesion" @sesionCerrada="actualizaDatosSesion" title="FreeTours" />
    <NavBar :datos="datosSesion"/>
    <RouterView @sesionIniciada="actualizaDatosSesion" ></RouterView>
    <Footer/>
  </div>
</template>

<style scoped>
.layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
}
</style>