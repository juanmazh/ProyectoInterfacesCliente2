<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Rutas Asignadas</h2>
  
    <table class="table table-hover table-bordered table-striped">
      <thead class="thead-custom">
        <tr>
          <th scope="col">Título</th>
          <th scope="col">Fecha</th>
          <th scope="col">Hora</th>
          <th scope="col">Localidad</th>
          <th scope="col">Participantes</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ruta in rutas" :key="ruta.ruta_id">
          <td>{{ ruta.ruta_titulo }}</td>
          <td>{{ ruta.ruta_fecha }}</td>
          <td>{{ ruta.ruta_hora }}</td>
          <td>{{ ruta.ruta_localidad }}</td>
          <td>
            <span :class="{ 'text-danger fw-bold': totalParticipantes(ruta) < 10, 'text-success': totalParticipantes(ruta) >= 10 }">
              {{ totalParticipantes(ruta) }}
              <span v-if="totalParticipantes(ruta) < 10">⚠️</span>
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';

// Variables reactivas
const rutas = ref([]);

// Obtener el ID del guía desde localStorage
const sesionUser = localStorage.getItem('sesionUser');
const guiaId = sesionUser ? JSON.parse(sesionUser).id : null;

if (!guiaId) {
  Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'No se encontró la sesión del usuario.',
  });
}

// Función para obtener las rutas asignadas al guía
const fetchRutasAsignadas = async () => {
  try {
    const response = await fetch(`http://localhost:8008/api.php/asignaciones?guia_id=${guiaId}`);
    if (!response.ok) throw new Error('Error al obtener las rutas asignadas');

    const data = await response.json();
    console.log('Datos obtenidos:', data); // Debugging
    rutas.value = data.sort((a, b) => new Date(a.ruta_fecha) - new Date(b.ruta_fecha));
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.message,
    });
  }
};

// Función para calcular el total de participantes en una ruta
const totalParticipantes = (ruta) => {
  if (!ruta.reservas || ruta.reservas.length === 0) return 0;
  return ruta.reservas.reduce((total, reserva) => total + reserva.num_personas, 0);
};

// Cargar las rutas asignadas al montar el componente
onMounted(fetchRutasAsignadas);
</script>

<style scoped>
/* Estilos para la tabla de rutas asignadas */
.container {
  max-width: 1200px;
  background: #ffffff;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 2.5rem;
  font-weight: bold;
}

.table {
  margin-top: 20px;
  border-radius: 8px;
  overflow: hidden;
}

.table th {
  background-color: #0069d9;
  color: #fff;
  text-align: center;
  font-weight: 600;
}

.table th, .table td {
  padding: 15px;
}

.table tbody tr:hover {
  background-color: #f1f1f1;
}

.text-danger {
  color: red;
}

.text-success {
  color: green;
}

.thead-custom {
  background-color: #0069d9;
  color: white;
}

</style>
