<template>
  <div class="user-management">
    <h1>Panel de Control</h1>
    <table>
      <thead>
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
            <button class="btn-sm btn-edit" @click="openEditModal(user)">Editar</button>
            <button class="btn-sm btn-delete" @click="deleteUser(user.id)">Borrar</button>
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
            <input type="text" v-model="selectedUser.nombre" id="nombre" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" v-model="selectedUser.email" id="email" required>
          </div>
          <div class="form-group">
            <label for="rol">Rol</label>
            <select v-model="selectedUser.rol" id="rol" required>
              <option value="user">Usuario</option>
              <option value="admin">Admin</option>
              <option value="guide">Guía</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

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

const openEditModal = (user) => {
  selectedUser.value = { ...user };
  showModal.value = true;
};

const closeEditModal = () => {
  showModal.value = false;
};

const updateUser = async () => {
  try {
    console.log('Updating user:', selectedUser.value);
    const response = await fetch(`http://localhost:8008/api.php/usuarios/${selectedUser.value.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(selectedUser.value),
    });
    if (!response.ok) {
      const errorText = await response.text();
      console.error('Error response text:', errorText);
      throw new Error(`Network response was not ok: ${errorText}`);
    }
    const data = await response.json();
    // Actualizar la lista de usuarios localmente
    const index = users.value.findIndex(user => user.id === data.id);
    if (index !== -1) {
      users.value[index] = data;
    }
    closeEditModal();
  } catch (error) {
    console.error('Error updating user:', error);
  }
};

const deleteUser = async (userId) => {
  try {
    console.log(`Deleting user with ID: ${userId}`);
    const response = await fetch(`http://localhost:8008/api.php/usuarios/${userId}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    if (!response.ok) {
      const errorText = await response.text();
      console.error('Error response text:', errorText);
      throw new Error(`Network response was not ok: ${errorText}`);
    }
    // Eliminar el usuario de la lista local después de eliminarlo en el servidor
    users.value = users.value.filter(user => user.id !== userId);
  } catch (error) {
    console.error('Error deleting user:', error);
  }
};

onMounted(() => {
  fetchUsuarios();
});
</script>

<style scoped>
.user-management {
  padding: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
}

th {
  background-color: #f2f2f2;
}

.btn-sm {
  padding: 5px 10px;
  margin: 2px;
}

.btn-edit {
  background-color: #4CAF50;
  color: white;
}

.btn-delete {
  background-color: #f44336;
  color: white;
}

/* Estilos para el modal */
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