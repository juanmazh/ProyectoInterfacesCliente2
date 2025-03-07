<script setup>
import { ref } from 'vue';
import Swal from 'sweetalert2';

const users = ref([]);
const showModal = ref(false);
const selectedUser = ref({});

const fetchUsuarios = async () => {
  try {
    const response = await fetch('http://localhost:8008/api.php/usuarios', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const data = await response.json();
    users.value = data;
  } catch (error) {
    console.error('Error fetching usuarios:', error);
  }
};

// Llamada directa para cargar los usuarios al inicializar el script
fetchUsuarios();

const openEditModal = (user) => {
  selectedUser.value = { ...user };
  showModal.value = true;
};

const closeEditModal = () => {
  showModal.value = false;
};

const updateUser = async () => {
  try {
    const updatedRole = {
      rol: selectedUser.value.rol
    };

    // Hacer la solicitud PUT con la URL correcta y los datos del rol
    const response = await fetch(`http://localhost:8008/api.php/usuarios?id=${selectedUser.value.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(updatedRole),
    });

    if (!response.ok) {
      const errorText = await response.text();
      console.error('Error response text:', errorText);
      throw new Error(`Network response was not ok: ${errorText}`);
    }

    const data = await response.json();  // Asegúrate de que la respuesta contenga los datos actualizados

    // Actualizar la lista de usuarios localmente con los nuevos datos en tiempo real
    const index = users.value.findIndex(user => user.id === selectedUser.value.id);
    if (index !== -1) {
      // Actualiza solo el rol
      users.value[index] = { ...users.value[index], rol: data.rol };
    }

    // Cerrar el modal y mostrar alerta de éxito
    closeEditModal();

    Swal.fire({
      title: 'Usuario Actualizado',
      text: 'El rol del usuario ha sido actualizado correctamente.',
      icon: 'success',
      confirmButtonText: 'Aceptar'
    });
  } catch (error) {
    console.error('Error updating user:', error);
    Swal.fire({
      title: 'Error',
      text: 'Hubo un problema al actualizar el usuario.',
      icon: 'error',
      confirmButtonText: 'Aceptar'
    });
  }
};

const deleteUser = async (userId) => {
  try {
    // Confirmar eliminación
    const result = await Swal.fire({
      title: '¿Estás seguro?',
      text: "Esta acción eliminará al usuario permanentemente.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true
    });

    if (result.isConfirmed) {
      console.log(`Borrar usuario de id: ${userId}`);
      const response = await fetch(`http://localhost:8008/api.php/usuarios?id=${userId}`, {
        method: 'DELETE',
      });

      if (!response.ok) {
        const errorText = await response.text();
        console.error('Error response text:', errorText);
        throw new Error(`Network response was not ok: ${errorText}`);
      }

      // Eliminar el usuario de la lista local inmediatamente
      users.value = users.value.filter(user => user.id !== userId);

      // Alerta de éxito
      Swal.fire(
        'Eliminado!',
        'El usuario ha sido eliminado correctamente.',
        'success'
      );
    }
  } catch (error) {
    console.error('Error deleting user:', error);
    Swal.fire({
      title: 'Error',
      text: 'Hubo un problema al eliminar el usuario.',
      icon: 'error',
      confirmButtonText: 'Aceptar'
    });
  }
};
</script>

<template>
  <div class="user-management">
    <h1>Panel de Control</h1>
    <table class="table table-striped table-hover">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.nombre }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.rol }}</td>
          <td>
            <button class="btn btn-sm btn-primary me-2" @click="openEditModal(user)">Editar</button>
            <button class="btn btn-sm btn-danger" @click="deleteUser(user.id)">Borrar</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal para editar usuario -->
    <div v-if="showModal" class="modal">
      <div class="modal-content">
        <span class="close" @click="closeEditModal">&times;</span>
        <h2>Editar Usuario</h2>
        <form @submit.prevent="updateUser">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" v-model="selectedUser.nombre" id="nombre" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" v-model="selectedUser.email" id="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="rol">Rol</label>
            <select v-model="selectedUser.rol" id="rol" class="form-control" required>
              <option value="cliente">Cliente</option>
              <option value="admin">Admin</option>
              <option value="guia">Guía</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary mt-3">Guardar</button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
h1{
  text-align: center;
}
.user-management {
  padding: 20px;
}

.modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
  background-color: #fefefe;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  max-width: 500px;
  border-radius: 5px;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
}
</style>
