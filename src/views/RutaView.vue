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
        <p><strong>Fecha:</strong> {{ ruta.fecha }}</p>
        <p><strong>Hora:</strong> {{ ruta.hora }}</p>
        <p><strong>Guía:</strong> {{ ruta.guia ? ruta.guia.nombre : 'No asignado' }}</p> <!-- Mostrar el guía asignado -->
      </div>

      <!-- Columna del mapa -->
      <div class="col-md-3">
        <div id="mapa"></div>
      </div>
    </div>

    <!-- Caja de la descripción -->
     <h4><strong>Descripción</strong></h4>
    <div class="descripcion-container">
      <p v-html="ruta.descripcion"></p> <!-- Muestra toda la descripción -->
    </div>

    <!-- Botón para reservar -->
    <div class="text-center mt-4">
      <button class="btn btn-primary" @click="openReservationModal">
        Reservar esta ruta
      </button>
    </div>

    <!-- Botón para asignar o cambiar guía, visible solo para administradores -->
    <div class="text-center mt-2" v-if="isAdmin">
      <button class="btn btn-secondary" @click="openAssignGuideModal">
        Asignar o cambiar guía
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
const isAdmin = ref(false); // Variable reactiva para el rol de administrador
let mapInstance = null; // Guardar la instancia del mapa

// Verificar si el usuario es administrador
const checkAdmin = () => {
  const sesionUser = JSON.parse(localStorage.getItem('sesionUser'));
  if (sesionUser && sesionUser.rol === 'admin') {
    isAdmin.value = true;
  }
};

// Obtener los detalles de la ruta desde la API
const fetchRuta = async () => {
  try {
    // Obtener detalles de la ruta
    const response = await fetch(`http://localhost:8008/api.php/rutas?id=${route.params.id}`);
    if (!response.ok) throw new Error('Error al obtener los detalles de la ruta');

    const rutaData = await response.json();

    // Obtener la asignación del guía
    const asignacionResponse = await fetch(`http://localhost:8008/api.php/asignaciones?ruta_id=${route.params.id}`);
    if (!asignacionResponse.ok) throw new Error('Error al obtener la asignación del guía');

    const asignacionData = await asignacionResponse.json();
    const guiaId = asignacionData.length > 0 ? asignacionData[0].guia_id : null;

    // Obtener datos del guía solo si existe guiaId
    if (guiaId) {
      const guiaResponse = await fetch(`http://localhost:8008/api.php/usuarios?id=${guiaId}`);
      if (!guiaResponse.ok) throw new Error('Error al obtener los detalles del guía');

      const guiaData = await guiaResponse.json();

      // Si la API devuelve un array, tomamos solo el guía con el ID correspondiente
      rutaData.guia = Array.isArray(guiaData) ? guiaData.find(user => user.id == guiaId) || null : guiaData;
    } else {
      rutaData.guia = null;
    }

    // Asignar la ruta con el guía
    ruta.value = rutaData;

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
      <input id="numPersonas" type="number" min="1" max="8" class="swal2-input" placeholder="Número de personas">
      <p style="text-align: center; margin-top: 10px;">Máx 8 personas</p>
    `,
    showCancelButton: true,
    confirmButtonText: 'Reservar',
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
      const numPersonas = document.getElementById('numPersonas').value;

      if (!numPersonas) {
        Swal.showValidationMessage('El número de personas es obligatorio');
        return false;
      }

      return { numPersonas };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      realizarReserva(sesionUser, result.value.numPersonas);
    }
  });
};

// Función para enviar la reserva a la API
const realizarReserva = async (sesionUser, numPersonas) => {
  const reservaData = {
    email: sesionUser.email, 
    ruta_id: ruta.value.id,
    num_personas: parseInt(numPersonas),
  };

  try {
    const response = await fetch('http://localhost:8008/api.php/reservas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(reservaData)
    });

    const text = await response.text(); // Obtener la respuesta como texto

    if (!text) {
      throw new Error('La respuesta de la API está vacía');
    }

    let data;
    try {
      data = JSON.parse(text); // Intentar parsear la respuesta como JSON
    } catch (err) {
      throw new Error(`Error al parsear la respuesta JSON: ${text}`);
    }

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

// Función para abrir el modal de asignación de guía
const openAssignGuideModal = async () => {
  try {
    const response = await fetch(`http://localhost:8008/api.php/asignaciones?fecha=${ruta.value.fecha}`);
    if (!response.ok) throw new Error('Error al obtener la lista de guías');

    const guias = await response.json();

    if (!Array.isArray(guias)) {
      throw new Error('La respuesta de la API no es un array');
    }

    const options = guias.filter(guia => !guia.asignado).map(guia => `<option value="${guia.id}">${guia.nombre}</option>`).join('');

    Swal.fire({
      title: 'Asignar o cambiar guía',
      html: `
        <select id="guiaSelect" class="swal2-input">
          ${options}
        </select>
      `,
      showCancelButton: true,
      confirmButtonText: 'Asignar',
      cancelButtonText: 'Cancelar',
      preConfirm: () => {
        const guiaId = document.getElementById('guiaSelect').value;
        if (!guiaId) {
          Swal.showValidationMessage('Debes seleccionar un guía');
          return false;
        }
        return { guiaId };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        assignGuide(result.value.guiaId);
      }
    });

  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.message,
    });
  }
};

// Función para asignar el guía seleccionado a la ruta
const assignGuide = async (guiaId) => {
  const asignacionData = {
    ruta_id: ruta.value.id,
    guia_id: guiaId
  };

  try {
    const response = await fetch('http://localhost:8008/api.php/asignaciones', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(asignacionData)
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Error al asignar el guía');
    }

    Swal.fire({
      icon: 'success',
      title: 'Guía asignado con éxito',
      text: 'El guía ha sido asignado correctamente.',
    });

    // Actualizar los detalles de la ruta
    fetchRuta();

  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.message,
    });
  }
};

// Cargar los datos al montar el componente
onMounted(() => {
  fetchRuta();
  checkAdmin(); // Verificar si el usuario es administrador
});

// Esperar a que la ruta cargue y luego iniciar el mapa
watch(ruta, (newRuta) => {
  if (newRuta && newRuta.latitud && newRuta.longitud) {
    initMap();
  }
});
</script>

<style scoped>
body {
  font-family: 'Poppins', sans-serif;
}

.container {
  max-width: 1200px;
  background: #ffffff;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease-in-out;
}

.container:hover {
  transform: translateY(-5px);
}

h2 {
  font-size: 3rem;
  font-weight: 700;
  color: #000000;
  background: linear-gradient(to right, #007bff, #00c6ff);
  -webkit-background-clip: text;
  background-clip: text;
  text-align: center;
  letter-spacing: 2px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 20px;
}

.descripcion-container {
  background-color: #f7f7f7;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  margin-top: 20px;
}

.descripcion-container p {
  font-size: 1.4rem;
  color: #333;
  line-height: 1.6;
}

h4, p {
  font-size: 1.4rem;
  color: #555;
}

#mapa {
  width: 100%;
  height: 300px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

#mapa:hover {
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.img-fluid {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

.row {
  margin-bottom: 30px;
}

.col-md-5, .col-md-4, .col-md-3 {
  padding: 15px;
}

.btn-primary {
  font-size: 1.5rem;
  padding: 12px 25px;
  background-color: #007bff;
  border: none;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
  background-color: #0056b3;
}

.text-center {
  text-align: center;
}
</style>
