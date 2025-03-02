<template>
    <div class="container mt-5">
      <h2 class="text-center mb-4">Mis Reservas</h2>
  
      <div v-if="reservas.length > 0">
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Ruta</th>
                <th>Fecha Ruta</th>
                <th>Hora Ruta</th>
                <th>Localidad</th>
                <th>Personas</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(reserva, index) in reservas" :key="reserva.id">
                <td>{{ index + 1 }}</td>
                <td>{{ reserva.nombre_ruta || 'Cargando...' }}</td>
                <td>{{ reserva.fecha_ruta || 'Cargando...' }}</td>
                <td>{{ reserva.hora_ruta || 'Cargando...' }}</td>
                <td>{{ reserva.localidad_ruta || 'Cargando...' }}</td>
                <td>{{ reserva.num_personas }}</td>
                <td>
                  <button
                    class="btn btn-danger"
                    @click="cancelarReserva(reserva.id)" 
                  >
                    Cancelar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  
      <p v-else class="text-center text-muted">No tienes reservas registradas.</p>
  
      <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import Swal from 'sweetalert2';
  
  const reservas = ref([]);
  const error = ref(null);
  
  // Obtener el email del usuario desde el localStorage
  const sesionUser = JSON.parse(localStorage.getItem('sesionUser'));
  
  // Función para obtener las reservas
  const fetchReservas = async () => {
    if (!sesionUser || !sesionUser.email) {
      Swal.fire({
        icon: 'warning',
        title: 'No estás autenticado',
        text: 'Por favor, inicia sesión para ver tus reservas.',
      });
      return;
    }
  
    try {
      const response = await fetch(`http://localhost:8008/api.php/reservas?email=${sesionUser.email}`, {
        method: 'GET',
      });
  
      if (!response.ok) {
        throw new Error('Error al obtener las reservas');
      }
  
      let reservasData = await response.json();
  
      // Obtener los detalles de las rutas para cada reserva
      const rutasPromises = reservasData.map(async (reserva) => {
        try {
          const rutaResponse = await fetch(`http://localhost:8008/api.php/rutas?id=${reserva.ruta_id}`);
          if (!rutaResponse.ok) {
            throw new Error(`Error al obtener la ruta ${reserva.ruta_id}`);
          }
          const rutaData = await rutaResponse.json();
          return {
            ...reserva,
            nombre_ruta: rutaData.titulo,
            fecha_ruta: rutaData.fecha,
            hora_ruta: rutaData.hora,
            localidad_ruta: rutaData.localidad,
          };
        } catch {
          return {
            ...reserva,
            nombre_ruta: 'Desconocido',
            fecha_ruta: 'Desconocida',
            hora_ruta: 'Desconocida',
            localidad_ruta: 'Desconocida',
          };
        }
      });
  
      // Esperar a que todas las rutas se obtengan
      reservas.value = await Promise.all(rutasPromises);
    } catch (err) {
      error.value = err.message;
    }
  };
  
  // Función para cancelar una reserva
  const cancelarReserva = async (reservaId) => {
    if (!reservaId) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo encontrar el ID de la reserva.',
      });
      return;
    }
  
    const apiUrl = `http://localhost:8008/api.php/reservas?id=${reservaId}`;
    console.log("ID de la reserva a cancelar:", reservaId); // Verificar que el ID se está recibiendo correctamente
  
    try {
      // Confirmar la acción con el usuario
      const confirmacion = await Swal.fire({
        icon: 'warning',
        title: '¿Estás seguro?',
        text: '¿Quieres cancelar esta reserva?',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, mantener',
      });
  
      if (!confirmacion.isConfirmed) {
        return; // Si el usuario cancela, no hacer nada
      }
  
      // Enviar la solicitud DELETE con el ID de la reserva en la URL
      const response = await fetch(apiUrl, {
        method: 'DELETE',
      });
  
      if (!response.ok) {
        throw new Error('Error al cancelar la reserva');
      }
  
      // Confirmar con el usuario que la reserva ha sido cancelada
      Swal.fire({
        icon: 'success',
        title: 'Reserva Cancelada',
        text: 'Tu reserva ha sido cancelada exitosamente.',
      });
  
      // Eliminar la reserva cancelada de la lista
      reservas.value = reservas.value.filter(reserva => reserva.id !== reservaId);
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Hubo un problema al cancelar la reserva.',
      });
    }
  };
  
  // Obtener las reservas al cargar el componente
  onMounted(fetchReservas);
  </script>
  
  <style scoped>
  .container {
    max-width: 1200px;
    background: #f8f9fa;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  h2 {
    font-size: 2.5rem;
  }
  
  .table {
    font-size: 1.2rem;
  }
  
  .table th,
  .table td {
    text-align: center;
    vertical-align: middle;
  }
  
  .btn-danger {
    width: 100%;
    padding: 10px;
  }
  </style>
  