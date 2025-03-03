<template>
    <div class="container mt-5">
      <h2 class="text-center mb-4">Valorar Ruta</h2>
  
      <div v-if="rutasPasadas.length > 0">
        <h4 class="text-center mb-4">Selecciona una Ruta para Valorar</h4>
        <div class="list-group mb-5">
          <button
            v-for="ruta in rutasPasadas"
            :key="ruta.id"
            class="list-group-item list-group-item-action"
            @click="seleccionarRuta(ruta)"
          >
            {{ ruta.titulo }} - {{ ruta.fecha }}
          </button>
        </div>
      </div>
      <p v-else class="text-center text-muted">No tienes rutas pasadas para valorar.</p>
  
      <div v-if="rutaSeleccionada">
        <h3 class="text-center">{{ rutaSeleccionada.titulo }}</h3>
        <p class="text-center">{{ rutaSeleccionada.descripcion }}</p>
  
        <div v-if="usuarioAutenticado" class="mt-5">
          <h4>Deja tu valoración</h4>
          <form @submit.prevent="enviarValoracion">
            <div class="mb-3">
              <label for="estrellas" class="form-label">Puntuación</label>
              <StarRating v-model="nuevaValoracion.estrellas" />
            </div>
            <div class="mb-3">
              <label for="comentario" class="form-label">Comentario</label>
              <textarea v-model="nuevaValoracion.comentario" class="form-control" id="comentario" rows="3" placeholder="Escribe tu comentario (opcional)"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Valoración</button>
          </form>
        </div>
        <div v-else class="mt-5">
          <p class="text-center text-muted">Inicia sesión para dejar una valoración.</p>
        </div>
      </div>
  
      <div class="mt-5">
        <h4>Valoraciones</h4>
        <div v-if="valoraciones.length > 0">
          <div v-for="valoracion in valoraciones" :key="valoracion.id" class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">
                <StarRating :modelValue="valoracion.estrellas || 0" :readonly="true" />
              </h5>
              <p class="card-text">{{ valoracion.comentario }}</p>
              <p class="card-text"><small class="text-muted">Por {{ valoracion.cliente_nombre }} sobre la ruta {{ valoracion.ruta_titulo }}</small></p>
            </div>
          </div>
        </div>
        <p v-else class="text-center text-muted">No hay valoraciones disponibles.</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import Swal from 'sweetalert2';
  import StarRating from '../components/StarRating.vue';
  
  const rutasPasadas = ref([]);
  const rutaSeleccionada = ref(null);
  const valoraciones = ref([]);
  const usuarioAutenticado = JSON.parse(localStorage.getItem('sesionUser'));
  const nuevaValoracion = ref({
    user_id: usuarioAutenticado ? usuarioAutenticado.id : '',
    ruta_id: '',
    estrellas: 0,
    comentario: ''
  });
  
  // 🔹 Cargar rutas pasadas
  const fetchRutasPasadas = async () => {
    const sesionUser = JSON.parse(localStorage.getItem('sesionUser'));
    if (!sesionUser || !sesionUser.email) {
      Swal.fire({
        icon: 'warning',
        title: 'No estás autenticado',
        text: 'Por favor, inicia sesión para ver tus rutas pasadas.',
      });
      return;
    }
  
    try {
      const response = await fetch(`http://localhost:8008/api.php/reservas?email=${sesionUser.email}`);
      if (!response.ok) throw new Error('Error al obtener las reservas');
      const reservasData = await response.json();
  
      const now = new Date();
      const rutasPromises = reservasData.map(async (reserva) => {
        const rutaResponse = await fetch(`http://localhost:8008/api.php/rutas?id=${reserva.ruta_id}`);
        if (!rutaResponse.ok) throw new Error(`Error al obtener la ruta ${reserva.ruta_id}`);
        const rutaData = await rutaResponse.json();
        const rutaFecha = new Date(rutaData.fecha);
        if (rutaFecha < now) {
          return {
            ...rutaData,
            reserva_id: reserva.id
          };
        }
      });
  
      const todasRutas = await Promise.all(rutasPromises);
      rutasPasadas.value = todasRutas.filter(ruta => ruta !== undefined);
    } catch (err) {
      console.error('Error al obtener rutas pasadas:', err);
    }
  };
  
  // 🔹 Cargar todas las valoraciones
  const fetchValoraciones = async () => {
    try {
      const response = await fetch('http://localhost:8008/api.php/valoraciones');
      if (!response.ok) throw new Error('Error al obtener las valoraciones');
      valoraciones.value = await response.json();
    } catch (err) {
      console.error('Error al obtener las valoraciones:', err);
    }
  };
  
  // 🔹 Seleccionar ruta para valorar
  const seleccionarRuta = (ruta) => {
    rutaSeleccionada.value = ruta;
    nuevaValoracion.value.ruta_id = ruta.id;
  };
  
  // 🔹 Enviar una nueva valoración
  const enviarValoracion = async () => {
    console.log('Enviando valoración:', nuevaValoracion.value);
  
    try {
      const response = await fetch('http://localhost:8008/api.php/valoraciones', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(nuevaValoracion.value)
      });
  
      console.log('Estado de la respuesta:', response.status);
      const responseText = await response.text();
      console.log('Respuesta del servidor:', responseText);
  
      if (!response.ok) throw new Error(responseText);
  
      const data = JSON.parse(responseText);
      Swal.fire({ icon: 'success', title: 'Valoración enviada', text: 'Tu valoración ha sido enviada correctamente.' });
      
      // Actualizar lista de valoraciones
      valoraciones.value.push({
        ...data,
        cliente_nombre: usuarioAutenticado.nombre, // Agregar nombre del usuario
        ruta_titulo: rutaSeleccionada.value.titulo // Agregar título de la ruta
      });
  
      // Limpiar formulario
      nuevaValoracion.value.estrellas = 0;
      nuevaValoracion.value.comentario = '';
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Hubo un problema al enviar la valoración.' });
    }
  };
  
  // 🔹 Cargar datos al montar el componente
  onMounted(() => {
    fetchRutasPasadas();
    fetchValoraciones();
  });
  </script>