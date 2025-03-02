<script setup>
import { ref } from 'vue';
import router from '@/router';
import Swal from 'sweetalert2';

const registerForm = ref({ nombre: '', email: '', contraseña: '' });
const registerError = ref('');
const loading = ref(false);
const successMessage = ref('');

async function registrarUsuario() {
  registerError.value = ''; // Limpiar cualquier error previo
  successMessage.value = ''; // Limpiar cualquier mensaje de éxito previo
  loading.value = true; // Activar el estado de carga

  try {
    const response = await fetch('http://localhost:8008/api.php/usuarios', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(registerForm.value),
    });

    loading.value = false; // Desactivar el estado de carga

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const data = await response.json();

    if (data.status === 'success') {
      // Limpiar formulario y mensaje de error
      registerForm.value = { nombre: '', email: '', contraseña: '' };
      registerError.value = '';

      // Mostrar alerta de éxito con SweetAlert2
      Swal.fire({
        icon: 'success',
        title: '¡Registro Exitoso!',
        text: 'Te has registrado correctamente. Serás redirigido al login.',
        confirmButtonText: 'Aceptar',
      });

      // Redirigir al login después de 3 segundos
      setTimeout(() => {
        router.push({ name: 'login' });
      }, 3000);
    } else {
      registerError.value = data.message || 'Error al registrar el usuario';
      // Mostrar alerta de error con SweetAlert2
      Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: registerError.value,
        confirmButtonText: 'Aceptar',
      });
    }
  } catch (err) {
    loading.value = false; // Desactivar el estado de carga
    registerError.value = 'Error al registrar el usuario';
    // Mostrar alerta de error con SweetAlert2
    Swal.fire({
      icon: 'error',
      title: '¡Error!',
      text: registerError.value,
      confirmButtonText: 'Aceptar',
    });
  }
}
</script>

<template>
<section class="vh-100 d-flex justify-content-center align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 text-black">

        <div class="px-5 ms-xl-4 text-center">
          <i class="fas fa-crow fa-2x me-3 pt-5 mt-xl-4" style="color: #709085;"></i>
          <span class="h1 fw-bold mb-0">Registrarse</span>
        </div>

        <div class="d-flex align-items-center h-custom-3 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5 justify-content-center">
          <form style="width: 23rem;" @submit.prevent="registrarUsuario">

            <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Crear Cuenta</h3>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="registerNombre" v-model="registerForm.nombre" class="form-control form-control-lg" />
              <label class="form-label" for="registerNombre">Nombre</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="email" id="registerEmail" v-model="registerForm.email" class="form-control form-control-lg" />
              <label class="form-label" for="registerEmail">Email</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="password" id="registerPassword" v-model="registerForm.contraseña" class="form-control form-control-lg" />
              <label class="form-label" for="registerPassword">Contraseña</label>
            </div>

            <div class="pt-1 mb-4">
              <button data-mdb-button-init data-mdb-ripple-init class="btn btn-info btn-lg btn-block" type="submit" :disabled="loading">
                <!-- Mostrar estado de carga en el botón -->
                <span v-if="loading">Cargando...</span>
                <span v-else>Crear Cuenta</span>
              </button>
            </div>

            <p class="small mb-5 pb-lg-2"><a class="text-muted" href="#!">¿Ya tienes una cuenta? Inicia sesión</a></p>
            <p v-if="registerError" class="text-danger">{{ registerError }}</p>
            <p v-if="successMessage" class="text-success">{{ successMessage }}</p>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>
</template>

<style scoped>
.vh-100 {
  height: 100vh;
}

.container {
  background-color: #f8f9fa;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 40px;
}

.h1 {
  font-size: 3em;
  color: #333;
}

.form-control {
  font-size: 1.2em;
  padding: 15px;
}

.btn {
  font-size: 1.2em;
  padding: 15px;
  background-color: #007BFF;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn:hover {
  background-color: #0056b3;
  transform: scale(1.05);
}

.form-label {
  font-size: 1.2em;
  color: #333;
}

.text-muted {
  color: #6c757d !important;
}

.text-danger {
  color: #dc3545;
}

.link-info {
  color: #007BFF;
  text-decoration: none;
  transition: color 0.3s ease;
}

.link-info:hover {
  color: #0056b3;
  text-decoration: underline;
}
</style>
