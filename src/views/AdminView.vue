<template>
  <div class="back-button-container">
    <button class="btn" @click="goToIndex">Volver a la vista de usuario</button>
  </div>
  <div>
    <Admin />
  </div>
</template>

<script setup>
import Admin from '../components/Admin.vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const goToIndex = () => {
  router.push('/');
};
</script>

<style scoped>
.back-button-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  background-color: #4CAF50;
}

.btn {
  padding: 10px 20px;
  font-size: 1em;
  color: white;
  background-color: #007BFF;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #0056b3;
}

/* Ocultar el header previo */
header {
  display: none;
}
</style>