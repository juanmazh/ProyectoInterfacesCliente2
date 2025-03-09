<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Mis Reservas</h2>
    <!--Como se hace la tabla de mis reservas, de manera que se actualiza según los cambios-->
    <div v-if="reservasFuturas.length > 0">
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Ruta</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Localidad</th>
              <th>Personas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(reserva, index) in reservasFuturas" :key="reserva.reserva_id">
              <td>{{ index + 1 }}</td>
              <td>{{ reserva.nombre_ruta || "Cargando..." }}</td>
              <td>{{ reserva.fecha_ruta || "Cargando..." }}</td>
              <td>{{ reserva.hora_ruta || "Cargando..." }}</td>
              <td>{{ reserva.localidad_ruta || "Cargando..." }}</td>
              <td>{{ reserva.num_personas }}</td>
              <td>
                <button class="btn btn-danger" @click="cancelarReserva(reserva.reserva_id)">
                  <i class="bi bi-x-circle"></i>
                </button>
                <button class="btn btn-primary" @click="cambiarAsistentes(reserva)">
    <i class="bi bi-person-plus"></i> 
  </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <p v-else class="text-center text-muted">No tienes reservas registradas.</p>

    <h2 class="text-center mb-4 mt-5">Historial de Reservas Pasadas</h2>
    <!--Como se hace la tabla de mis reservas pasadas, de manera que se actualiza según los cambios o rutas pasadas-->

    <div v-if="reservasPasadas.length > 0">
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Ruta</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Localidad</th>
              <th>Personas</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(reserva, index) in reservasPasadas" :key="reserva.reserva_id">
              <td>{{ index + 1 }}</td>
              <td>{{ reserva.nombre_ruta || "Cargando..." }}</td>
              <td>{{ reserva.fecha_ruta || "Cargando..." }}</td>
              <td>{{ reserva.hora_ruta || "Cargando..." }}</td>
              <td>{{ reserva.localidad_ruta || "Cargando..." }}</td>
              <td>{{ reserva.num_personas }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <p v-else class="text-center text-muted">No tienes reservas pasadas.</p>

    <p v-if="error" class="text-danger text-center mt-3">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";

const reservasFuturas = ref([]);
const reservasPasadas = ref([]);
const error = ref(null);

// Obtener el email del usuario desde el localStorage
const sesionUser = JSON.parse(localStorage.getItem("sesionUser"));

// Función para obtener las reservas
const fetchReservas = async () => {
  if (!sesionUser || !sesionUser.email) {
    Swal.fire({
      icon: "warning",
      title: "No estás autenticado",
      text: "Por favor, inicia sesión para ver tus reservas.",
    });
    return;
  }

  try {
    const response = await fetch(
      `http://localhost:8008/api.php/reservas?email=${sesionUser.email}`,
      { method: "GET" }
    );

    if (!response.ok) {
      throw new Error("Error al obtener las reservas");
    }

    let reservasData = await response.json();

    // Obtener los detalles de las rutas para cada reserva
    const rutasPromises = reservasData.map(async (reserva) => {
      try {
        const rutaResponse = await fetch(
          `http://localhost:8008/api.php/rutas?id=${reserva.ruta_id}`
        );
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
          nombre_ruta: "Desconocido",
          fecha_ruta: "Desconocida",
          hora_ruta: "Desconocida",
          localidad_ruta: "Desconocida",
        };
      }
    });

    // Esperar a que todas las rutas se obtengan
    const todasReservas = await Promise.all(rutasPromises);

    // Separar reservas futuras y pasadas
    const now = new Date();
    reservasFuturas.value = todasReservas.filter(reserva => {
      const reservaFecha = new Date(`${reserva.fecha_ruta}T${reserva.hora_ruta}`);
      return reservaFecha >= now;
    });
    reservasPasadas.value = todasReservas.filter(reserva => {
      const reservaFecha = new Date(`${reserva.fecha_ruta}T${reserva.hora_ruta}`);
      return reservaFecha < now;
    });

  } catch (err) {
    error.value = err.message;
  }
};

// Función para cancelar una reserva
const cancelarReserva = async (reservaId) => {
  if (!reservaId) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se pudo encontrar el ID de la reserva.",
    });
    return;
  }

  const apiUrl = `http://localhost:8008/api.php/reservas?id=${reservaId}`;
  console.log("ID de la reserva a cancelar:", reservaId);
  //Estoy adicto al SweetAlert2
  try {
    const confirmacion = await Swal.fire({
      icon: "warning",
      title: "¿Estás seguro?",
      text: "¿Quieres cancelar esta reserva?",
      showCancelButton: true,
      confirmButtonText: "Sí, cancelar",
      cancelButtonText: "No, mantener",
    });

    if (!confirmacion.isConfirmed) {
      return;
    }

    const response = await fetch(apiUrl, { method: "DELETE" });
    const data = await response.json();

    console.log("Respuesta de la API:", data);

    if (data.status === "success") {
      Swal.fire({
        icon: "success",
        title: "Reserva Cancelada",
        text: data.message,
      });

      // Eliminar la reserva de la lista
      reservasFuturas.value = reservasFuturas.value.filter(
        (reserva) => reserva.reserva_id !== reservaId
      );
    } else {
      throw new Error(data.message || "Error al cancelar la reserva");
    }
  } catch (err) {
    console.error("Error en la eliminación:", err);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: err.message || "Hubo un problema al cancelar la reserva.",
    });
  }
};
const cambiarAsistentes = async (reserva) => {
  const { value: numPersonas } = await Swal.fire({
    title: 'Cambiar número de asistentes',
    input: 'number',
    inputLabel: 'Número de personas',
    inputValue: reserva.num_personas,
    inputAttributes: {
      min: 1,
      step: 1
    },
    showCancelButton: true,
    confirmButtonText: 'Actualizar',
    cancelButtonText: 'Cancelar',
    preConfirm: (inputValue) => {
      // Verificar si el número es válido
      if (inputValue < 1) {
        Swal.showValidationMessage('El número de personas debe ser al menos 1');
        return false;
      }
      return inputValue;
    }
  });

  if (numPersonas) {
    try {
      // Actualizar la reserva en la base de datos (API)
      const response = await fetch(`http://localhost:8008/api.php/reservas?id=${reserva.reserva_id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ num_personas: numPersonas })
      });

      const data = await response.json();

      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Número de asistentes actualizado',
          text: 'El número de asistentes ha sido actualizado exitosamente.'
        });

        // Actualizar el número de asistentes en la vista
        reserva.num_personas = numPersonas;
      } else {
        throw new Error(data.message || 'Error al actualizar el número de asistentes');
      }
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err.message || 'Hubo un problema al actualizar la reserva.'
      });
    }
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

.table th,
.table td {
  padding: 15px;
}

.table tbody tr:hover {
  background-color: #f1f1f1;
}

.btn-danger {
  width: 50%;
  padding: 10px;
}
.btn-primary {
  width: 50%;
  padding: 10px;
}

.bi-x-circle {
  margin-right: 8px;
}
</style>