<template>
    <h1>Bienvenido a FreeTours</h1>
    <p></p>
    <div>
      <Carusel />
    </div>
  </template>
  
  <script>
  import Carusel from '@/components/Carusel.vue';
  
  export default {
    components: {
      Carusel
    }
  };
  </script>
  
  <style scoped>
  h1{
    text-align: center;
  }
  </style>