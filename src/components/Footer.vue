<template>
    <footer class="footer">
      <p>&copy; {{ new Date().getFullYear() }} - Todos los derechos reservados.</p>
      <p class="autor">Creado por Juan Manuel</p>
    </footer>
  </template>
  
  <style scoped>
  .footer {
    background: #34495e;
    color: white;
    padding: 1rem;
    text-align: center;
    position: absolute;
    bottom: 0;
    width: 100%;
  }
  .autor {
    font-size: 0.8rem;
    }
  </style>
  