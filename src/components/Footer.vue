<template>
  <!-- Importar Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
  <footer class="footer bg-dark text-white py-4">
    <div class="container text-center">
      <p>&copy; {{ new Date().getFullYear() }} - Todos los derechos reservados.</p>
      <p class="autor" xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/juanmazh/ProyectoInterfacesCliente2">ApatitaTours</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/juanmazh">Juan Manuel Zafra Hernández</a> is licensed under <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International<img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/cc.svg?ref=chooser-v1" alt=""><img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/by.svg?ref=chooser-v1" alt=""><img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/nc.svg?ref=chooser-v1" alt=""><img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/sa.svg?ref=chooser-v1" alt=""></a></p> 
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
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.7), rgba(52, 152, 219, 0.1));
  color: white;
  padding: 2rem 1rem;
  text-align: center;
  position: relative;
  bottom: 0;
  width: 100%;
  margin-top: auto;
}

.autor {
  font-size: 0.9rem;
  color: #bdc3c7;
  margin-top: 10px;
}

.social-icons a {
  color: white;
  margin: 0 15px;
  font-size: 1.5rem;
  transition: color 0.3s, transform 0.3s;
}

.social-icons a:hover {
  color: #3498db;
  transform: scale(1.1);
}

@media (max-width: 576px) {
  .social-icons a {
    font-size: 1.25rem;
    margin: 0 10px;
  }
}
</style>
