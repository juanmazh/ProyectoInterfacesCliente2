<template>
    <div class="container mt-5">
      <h2 class="text-center mb-4">Crear Nueva Ruta</h2>
      <form @submit.prevent="crearRuta">
        <div class="mb-3">
          <label class="form-label">Título</label>
          <input v-model="ruta.titulo" type="text" class="form-control" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Localidad</label>
          <input v-model="ruta.localidad" type="text" class="form-control" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea v-model="ruta.descripcion" class="form-control" required></textarea>
        </div>
        
        <div class="mb-3">
          <label class="form-label">URL de la Foto</label>
          <input v-model="ruta.foto" type="url" class="form-control">
        </div>
        
        <div class="mb-3">
          <label class="form-label">Fecha</label>
          <input v-model="ruta.fecha" type="date" class="form-control" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Hora</label>
          <input v-model="ruta.hora" type="time" class="form-control" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Latitud</label>
          <input v-model="ruta.latitud" type="text" class="form-control" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Longitud</label>
          <input v-model="ruta.longitud" type="text" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Crear Ruta</button>
        <p v-if="mensaje" class="mt-3 text-success">{{ mensaje }}</p>
        <p v-if="error" class="mt-3 text-danger">{{ error }}</p>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  const ruta = ref({
    titulo: '',
    localidad: '',
    descripcion: '',
    foto: '',
    fecha: '',
    hora: '',
    latitud: '',
    longitud: ''
  });
  const mensaje = ref('');
  const error = ref('');
  
  const crearRuta = async () => {
    try {
      const response = await fetch('http://localhost/api.php/rutas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(ruta.value)
      });
  
      const data = await response.json();
      if (response.ok) {
        mensaje.value = 'Ruta creada exitosamente';
        setTimeout(() => router.push('/rutas'), 2000);
      } else {
        throw new Error(data.error || 'Error al crear la ruta');
      }
    } catch (err) {
      error.value = err.message;
    }
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 600px;
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  </style>
  