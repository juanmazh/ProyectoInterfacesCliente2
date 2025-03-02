<template>
  <div v-if="ruta" class="container mt-5">
    <h2 class="text-center mb-4">{{ ruta.titulo }}</h2>

    <div class="row align-items-start">
      <!-- Columna de la imagen -->
      <div class="col-md-5 text-center">
        <img v-if="ruta.foto" :src="ruta.foto" class="img-fluid rounded shadow" alt="Imagen de la ruta">
        <p v-else class="text-muted">No hay imagen disponible</p>
      </div>

      <!-- Columna de los detalles -->
      <div class="col-md-4">
        <h4><strong>Localidad:</strong> {{ ruta.localidad }}</h4>
        <p><strong>Descripción:</strong> {{ ruta.descripcion }}</p>
        <p><strong>Fecha:</strong> {{ ruta.fecha }}</p>
        <p><strong>Hora:</strong> {{ ruta.hora }}</p>
      </div>

      <!-- Columna del mapa -->
      <div class="col-md-3">
        <div id="mapa"></div>
      </div>
    </div>

    <!-- Botón para reservar debajo de toda la información -->
    <div class="text-center mt-4">
      <button class="btn btn-primary" @click="openReservationModal">
        Reservar esta ruta
      </button>
    </div>
  </div>

  <p v-else class="text-center">Cargando detalles de la ruta...</p>
  <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import L from 'leaflet'; // Importar Leaflet
import Swal from 'sweetalert2';

// Obtener el id de la ruta desde la URL
const route = useRoute();
const ruta = ref(null);
const error = ref(null);
let mapInstance = null; // Guardar la instancia del mapa

// Obtener los detalles de la ruta desde la API
const fetchRuta = async () => {
  try {
    const response = await fetch(`http://localhost:8008/api.php/rutas?id=${route.params.id}`);
    if (!response.ok) {
      throw new Error('Error al obtener los detalles de la ruta');
    }
    ruta.value = await response.json();
  } catch (err) {
    error.value = err.message;
  }
};

// Inicializar el mapa con Leaflet
const initMap = async () => {
  await nextTick(); // Esperar a que el DOM se renderice completamente

  if (!document.getElementById('mapa')) return; // Evita errores si el div aún no existe

  if (mapInstance) {
    mapInstance.remove(); // Si el mapa ya existe, eliminarlo antes de crear uno nuevo
  }

  mapInstance = L.map('mapa').setView([ruta.value.latitud, ruta.value.longitud], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(mapInstance);

  L.marker([ruta.value.latitud, ruta.value.longitud])
    .addTo(mapInstance)
    .bindPopup(`<b>${ruta.value.titulo}</b><br>${ruta.value.localidad}`)
    .openPopup();
};

// Función para abrir el modal de reserva con SweetAlert2
const openReservationModal = () => {
  const sesionUser = JSON.parse(localStorage.getItem('sesionUser'));

  if (!sesionUser) {
    Swal.fire({
      icon: 'warning',
      title: 'No estás autenticado',
      text: 'Inicia sesión para realizar una reserva.',
    });
    return;
  }

  Swal.fire({
    title: 'Reserva tu ruta',
    html: `
      <input id="numPersonas" type="number" min="1" class="swal2-input" placeholder="Número de personas">
      <input id="fechaReserva" type="date" class="swal2-input" placeholder="Fecha de reserva">
    `,
    showCancelButton: true,
    confirmButtonText: 'Reservar',
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
      const numPersonas = document.getElementById('numPersonas').value;
      const fechaReserva = document.getElementById('fechaReserva').value;

      if (!numPersonas || !fechaReserva) {
        Swal.showValidationMessage('Todos los campos son obligatorios');
        return false;
      }

      return { numPersonas, fechaReserva };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      realizarReserva(sesionUser, result.value.numPersonas, result.value.fechaReserva);
    }
  });
};

// Función para enviar la reserva a la API
const realizarReserva = async (sesionUser, numPersonas, fechaReserva) => {
  const reservaData = {
    ruta_id: ruta.value.id,
    cliente_id: sesionUser.id,
    num_personas: parseInt(numPersonas),
    fecha_reserva: fechaReserva,
  };

  try {
    const response = await fetch('http://localhost:8008/api.php/reservas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(reservaData)
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Error al realizar la reserva');
    }

    Swal.fire({
      icon: 'success',
      title: 'Reserva realizada con éxito',
      text: 'Tu reserva ha sido registrada correctamente.',
    });

  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.message,
    });
  }
};

// Cargar los datos al montar el componente
onMounted(fetchRuta);

// Esperar a que la ruta cargue y luego iniciar el mapa
watch(ruta, (newRuta) => {
  if (newRuta && newRuta.latitud && newRuta.longitud) {
    initMap();
  }
});
</script>

<style scoped>
.container {
  max-width: 1200px;
  background: #f8f9fa;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

#mapa {
  width: 100%;
  height: 250px; /* Haciendo el mapa más pequeño */
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 2.5rem;
}

h4, p {
  font-size: 1.4rem;
}

.img-fluid {
  max-width: 100%;
  height: auto;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-primary {
  font-size: 1.4rem;
  padding: 10px 20px;
}
</style>
