<script setup>
import Content from './components/Content.vue';
import Card from './components/Card.vue';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
import NavBar from './components/NavBar.vue';
import router from "@/router";


import {ref} from 'vue'

//Habría que controlar cuando se inicia sesión y se cierra con un emit desde el hijo y tener un dato
// de sesión reactivo para pasarlo a la barra de navegación como props
const datosSesion= ref(JSON.parse(localStorage.getItem('sesion')));

function actualizaDatosSesion(usuario){
 
  datosSesion.value=usuario;
  if(usuario ){
    localStorage.setItem("sesion", JSON.stringify(usuario));
  }

  else
    localStorage.removeItem("sesion");

}
</script>

<template>
  <div class="layout">

    <Header :usuarioAutenticado="datosSesion" @sesionCerrada="actualizaDatosSesion" title="FreeTours" />
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