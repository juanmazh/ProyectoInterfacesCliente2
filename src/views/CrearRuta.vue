<template>
  <div class="container mt-4">
    <h2 class="text-center mb-4">Crear Nueva Ruta</h2>

    <form @submit.prevent="crearRuta">
      <!-- Título -->
      <div class="mb-3">
        <label class="form-label">Título</label>
        <input v-model="ruta.titulo" type="text" class="form-control" required />
      </div>

      <!-- Localidad -->
      <div class="mb-3">
        <label class="form-label">Localidad</label>
        <input v-model="ruta.localidad" type="text" class="form-control" required />
      </div>

      <!-- Descripción -->
      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea v-model="ruta.descripcion" class="form-control" rows="3" required></textarea>
      </div>

      <!-- Foto (URL) -->
      <div class="mb-3">
        <label class="form-label">URL de la Imagen</label>
        <input v-model="ruta.foto" type="text" class="form-control" required />
      </div>

      <!-- Fecha y Hora -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Fecha</label>
          <input v-model="ruta.fecha" type="date" class="form-control" required />
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Hora</label>
          <input v-model="ruta.hora" type="time" class="form-control" required />
        </div>
      </div>

      <!-- Ubicación -->
      <div class="mb-3">
        <label class="form-label">Ubicación</label>
        <input v-model="direccion" @keyup.enter="buscarUbicacion" type="text" class="form-control" placeholder="Buscar dirección y presionar Enter" />
      </div>

      <!-- Mapa -->
      <div id="map" style="height: 400px; margin-bottom: 20px;"></div>

      <!-- Botón de Enviar -->
      <button type="submit" class="btn btn-primary w-100">Crear Ruta</button>
    </form>

    <!-- Mensajes de Error -->
    <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const router = useRouter();

// Datos del formulario
const ruta = ref({
  titulo: '',
  localidad: '',
  descripcion: '',
  foto: '',
  fecha: '',
  hora: '',
  latitud: null,
  longitud: null
});

const direccion = ref('');
const error = ref(null);
let map, marker;

// Inicializar el mapa
onMounted(() => {
  map = L.map('map').setView([40.4168, -3.7038], 13); // Madrid por defecto

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map);

  // Evento para seleccionar ubicación manualmente en el mapa
  map.on('click', (e) => {
    actualizarUbicacion(e.latlng.lat, e.latlng.lng);
  });
});

// Buscar ubicación con OpenStreetMap
const buscarUbicacion = async () => {
  if (!direccion.value) return;

  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion.value)}`);
    const data = await response.json();

    if (data.length > 0) {
      const { lat, lon } = data[0];
      actualizarUbicacion(lat, lon);
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Ubicación no encontrada',
        text: 'Por favor, intenta con otra dirección.',
      });
    }
  } catch (err) {
    error.value = "Error al buscar la ubicación.";
  }
};

// Actualizar la ubicación en el mapa
const actualizarUbicacion = (lat, lng) => {
  ruta.value.latitud = lat;
  ruta.value.longitud = lng;

  if (marker) marker.remove();
  marker = L.marker([lat, lng]).addTo(map).bindPopup("Ubicación seleccionada").openPopup();

  map.setView([lat, lng], 13);
};

// Enviar datos a la API
const crearRuta = async () => {
  error.value = null;

  // Validar que la ubicación esté seleccionada
  if (!ruta.value.latitud || !ruta.value.longitud) {
    error.value = "Debes seleccionar una ubicación en el mapa.";
    return;
  }

  try {
    const response = await fetch('http://localhost:8008/api.php/rutas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(ruta.value)
    });

    const data = await response.json();
    if (!response.ok) throw new Error(data.message || "Error al crear la ruta");

    // Mostrar alerta de éxito con SweetAlert2
    Swal.fire({
      icon: 'success',
      title: '¡Ruta creada con éxito!',
      text: 'Serás redirigido a la lista de rutas.',
      timer: 2000,
      showConfirmButton: false,
    });

    // Redirigir a /rutas después de 2 segundos
    setTimeout(() => {
      router.push('/rutas');
    }, 2000);

  } catch (err) {
    error.value = err.message;
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: err.message,
    });
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: auto;
  padding-bottom: 20px;
}

#map {
  border-radius: 8px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
