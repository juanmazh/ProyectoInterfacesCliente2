<template>
  <div class="user-management">
    <h1>Panel de Control</h1>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>
            <button class="btn-sm btn-edit" @click="editUser(user)">Editar</button>
            <button class="btn-sm btn-delete" @click="deleteUser(user)">Borrar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const users = ref([]);

const fetchUsuarios = async () => {
  try {
    const response = await fetch('http://localhost:8008/api.php', {
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

onMounted(() => {
  fetchUsuarios();
});
</script>

<style scoped>
.user-management {
  max-width: 90%;
  margin: 0 auto;
  padding: 20px;
  background-color: #f0f8ff;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  color: #333;
  font-size: 2.5em;
  margin-bottom: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #4CAF50;
  color: white;
}

tr:hover {
  background-color: #f5f5f5;
}

.btn-sm {
  padding: 10px 20px;
  font-size: 1em;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-edit {
  background-color: #007BFF;
}

.btn-edit:hover {
  background-color: #0056b3;
}

.btn-delete {
  background-color: #FF4136;
}

.btn-delete:hover {
  background-color: #c0392b;
}

@media (max-width: 768px) {
  .btn-sm {
    width: 100%;
    margin-bottom: 10px;
  }
}
</style>
