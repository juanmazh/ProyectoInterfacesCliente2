<template>
<!-- Importar Bootstrap Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
  <footer class="footer bg-dark text-white py-4">
    <div class="container text-center">
      <p>&copy; {{ new Date().getFullYear() }} - Todos los derechos reservados.</p>
      <p class="autor">Creado por Juan Manuel</p>
      <div class="social-icons mt-3">
        <a href="https://github.com/juanmazh" target="_blank" class="text-white me-3">
          <i class="bi bi-github"></i>
        </a>
        <a href="https://es.linkedin.com/in/juan-manuel-zafra-hern%C3%A1ndez-5b2bb7339" target="_blank" class="text-white me-3">
          <i class="bi bi-linkedin"></i>
        </a>
        <a href="https://www.instagram.com/lil_capitolio/" target="_blank" class="text-white">
          <i class="bi bi-instagram"></i>
        </a>
      </div>
    </div>
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

.social-icons a {
  color: white;
  margin: 0 10px;
  transition: color 0.3s;
}

.social-icons a:hover {
  color: #3498db;
}
</style>
