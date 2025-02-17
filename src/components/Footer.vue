<template>
  <footer class="footer">
    <p>&copy; {{ new Date().getFullYear() }} - Todos los derechos reservados.</p>
    <p class="autor">Creado por Juan Manuel</p>
  </footer>
</template>

<script setup>
</script>

<style scoped>
.footer {
  background: #34495e;
  color: white;
  padding: 1rem;
  text-align: center;
  position: relative;
  bottom: 0;
  width: 100%;
  margin-top: auto;
}

.autor {
  font-size: 0.8rem;
}
</style>