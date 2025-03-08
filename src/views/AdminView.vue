<template>
  <div v-if="esAdmin" class="admin-container">
    <div class="back-button-container">
      <button class="btn" @click="goToIndex">Volver a la vista de usuario</button>
    </div>
    <div class="admin-content">
<!--Componente admin que muestra los usuarios-->
      <Admin />
    </div>
  </div>
</template>

<script setup>
import Admin from '../components/Admin.vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';

const router = useRouter();
const esAdmin = ref(false);
//Proteger la vista de admin de que no entre mediante la URL mi primo que es jacker
onMounted(() => {
  const sesion = localStorage.getItem('sesionUser');
  if (sesion) {
    const usuario = JSON.parse(sesion);
    if (usuario.rol === 'admin') {
      esAdmin.value = true;
    } else {
      router.push('/');
    }
  } else {
    router.push('/');
  }
});
//La logica del boton de volver a la vista de usuario, con un sweetalert de confirmacion, chulo eh
const goToIndex = () => {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "Volverás a la vista de usuario",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, volver',
    cancelButtonText: 'Cancelar',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      router.push('/');
      Swal.fire(
        'Redirigiendo...',
        'Te hemos llevado a la vista de usuario.',
        'success'
      );
    }
  });
};
</script>

<style scoped>
.admin-container {
  padding: 20px;
  background-color: #f4f4f9;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
}

.back-button-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 15px 20px;
  width: 100%;
  background-color: #007bff;
  margin-bottom: 30px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
}

.btn {
  padding: 12px 24px;
  font-size: 1.1em;
  color: white;
  background-color: #007bff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn:hover {
  background-color: #0056b3;
  transform: scale(1.05);
}

.admin-content {
  width: 80%;
  max-width: 1200px;
  margin-top: 30px;
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

header {
  display: none;
}
</style>
