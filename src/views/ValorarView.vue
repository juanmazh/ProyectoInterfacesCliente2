<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Valorar Ruta</h2>
    <!--Visualizar las rutas que se pueden valorar-->
    <div v-if="rutasPasadas.length > 0">
      <h4 class="text-center mb-4">Selecciona una Ruta para Valorar</h4>
      <div class="list-group mb-5">
        <button
          v-for="ruta in rutasPasadas"
          :key="ruta.id"
          class="list-group-item list-group-item-action list-group-item-custom"
          @click="seleccionarRuta(ruta)"
        >
          {{ ruta.titulo }} - {{ ruta.fecha }}
        </button>
      </div>
    </div>
    <p v-else class="text-center text-muted">No tienes rutas pasadas para valorar.</p>
    <!--Conseguir que si una persona no esta autenticada, pues que estudie y se haga cuenta, primero si tiene, luego que no-->
    <div v-if="rutaSeleccionada">
      <h3 class="text-center">{{ rutaSeleccionada.titulo }}</h3>
      <p class="text-center">{{ rutaSeleccionada.descripcion }}</p>

      <div v-if="usuarioAutenticado" class="mt-5">
        <h4>Deja tu valoración</h4>
        <form @submit.prevent="enviarValoracion">
          <div class="mb-3">
            <label class="form-label">Puntuación</label>
            <StarRating v-model="nuevaValoracion.estrellas" :readonly="false" />
          </div>
          <div class="mb-3">
            <label class="form-label">Comentario</label>
            <textarea v-model="nuevaValoracion.comentario" class="form-control form-control-custom" rows="3" placeholder="Escribe tu comentario (opcional)"></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-submit">Enviar Valoración</button>
        </form>
      </div>
      <div v-else class="mt-5">
        <p class="text-center text-muted">Inicia sesión para dejar una valoración.</p>
      </div>
    </div>

    <div class="mt-5">
      <h4>Valoraciones</h4>
      <!--Valoraciones ya hechas por otros usuarios o por el propio-->
      <div class="row">
        <div v-for="valoracion in valoraciones" :key="valoracion.id" class="col-md-6 col-lg-4">
          <div class="card valoracion-card">
            <div class="card-body">
              <div class="valoracion-header">
                <h5 class="valoracion-usuario">{{ valoracion.cliente_nombre }}</h5>
                <StarRating :modelValue="valoracion.puntuacion || 0" :readonly="true" />
              </div>
              <p class="valoracion-comentario">{{ valoracion.comentario || 'Sin comentario.' }}</p>
              <p class="valoracion-ruta"><strong>Ruta:</strong> {{ valoracion.ruta_titulo }}</p>
            </div>
          </div>
        </div>
      </div>
      <p v-if="valoraciones.length === 0" class="text-center text-muted">No hay valoraciones disponibles.</p>
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
  ruta_id: '',  // Este valor se actualiza cuando se selecciona una ruta
  estrellas: 0, // Cambié 'puntuacion' por 'estrellas'
  comentario: ''
});

const fetchRutasPasadas = async () => {
  const sesionUser = JSON.parse(localStorage.getItem('sesionUser'));
  if (!sesionUser || !sesionUser.email) {
    Swal.fire({ icon: 'warning', title: 'No estás autenticado', text: 'Por favor, inicia sesión.' });
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
      if (rutaFecha < now) return { ...rutaData, reserva_id: reserva.id };
    });
    const todasRutas = await Promise.all(rutasPromises);
    rutasPasadas.value = todasRutas.filter(ruta => ruta !== undefined);
  } catch (err) {
    console.error('Error al obtener rutas pasadas:', err);
  }
};

const fetchValoraciones = async () => {
  try {
    const response = await fetch('http://localhost:8008/api.php/valoraciones');
    if (!response.ok) throw new Error('Error al obtener las valoraciones');
    valoraciones.value = await response.json();
  } catch (err) {
    console.error('Error al obtener las valoraciones:', err);
  }
};

const seleccionarRuta = (ruta) => {
  rutaSeleccionada.value = ruta;
  nuevaValoracion.value.ruta_id = ruta.id;
};

const enviarValoracion = async () => {
  try {
    const response = await fetch('http://localhost:8008/api.php/valoraciones', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        user_id: nuevaValoracion.value.user_id,
        ruta_id: nuevaValoracion.value.ruta_id,
        estrellas: nuevaValoracion.value.estrellas,
         // Cambié 'puntuacion' por 'estrellas' porque así se llama en la API, pero en la base de datos guarda puntuacion lo cual me ha quebrado la cabeza
        comentario: nuevaValoracion.value.comentario
      })
    });
    if (!response.ok) throw new Error(await response.text());
    const data = await response.json();
    Swal.fire({ icon: 'success', title: 'Valoración enviada', text: 'Tu valoración ha sido enviada correctamente.' });
    valoraciones.value.push({ ...data, cliente_nombre: usuarioAutenticado.nombre, ruta_titulo: rutaSeleccionada.value.titulo });
    nuevaValoracion.value.estrellas = 0;
    nuevaValoracion.value.comentario = '';

    // Refrescar rutas y valoraciones
    fetchRutasPasadas();
    fetchValoraciones();
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Hubo un problema al enviar la valoración.' });
  }
};

onMounted(() => {
  fetchRutasPasadas();
  fetchValoraciones();
});
</script>

<style scoped>
/* Estilo para los botones de la lista de rutas */
.list-group-item-custom {
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  transition: background-color 0.2s ease;
}

.list-group-item-custom:hover {
  background-color: #0069d9;
  color: white;
}

/* Estilo para el área de comentarios */
.form-control-custom {
  border-radius: 8px;
  padding: 15px;
}

/* Estilo para el botón de enviar */
.btn-submit {
  background-color: #0069d9;
  border-color: #0069d9;
}

.btn-submit:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}

/* Estilo para las tarjetas de valoración */
.valoracion-card {
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
  background: #fff;
}

.valoracion-card:hover {
  transform: scale(1.02);
}

.valoracion-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.valoracion-usuario {
  font-size: 1rem;
  font-weight: bold;
  color: #333;
}

.valoracion-comentario {
  font-size: 0.9rem;
  color: #555;
}

.valoracion-ruta {
  font-size: 0.8rem;
  color: #888;
}
</style>
