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
    if (this.registerForm.contraseña !== this.registerForm.confirmarContraseña) {
      Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: registerError.value,
        confirmButtonText: 'Aceptar',
      });
      this.loading = false;
      return;
    }

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
<section class="h-100 bg-white">
  <div class="container py-5 h-100 mt-3 mb-5">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col">
        <div class="card card-registration my-4">
          <div class="row g-0">
            <div class="col-xl-6 d-none d-xl-block">
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-registration/img4.webp"
                alt="Sample photo" class="img-fluid"
                style="border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
            </div>
            <div class="col-xl-6">
              <div class="card-body p-md-5 text-black">
                <h3 class="mb-5 text-uppercase">Crear Cuenta</h3>

                <form @submit.prevent="registrarUsuario">
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

                  <div data-mdb-input-init class="form-outline mb-4">
                    <input type="password" id="registerConfirmPassword" v-model="registerForm.confirmarContraseña" class="form-control form-control-lg" />
                    <label class="form-label" for="registerConfirmPassword">Confirmar Contraseña</label>
                  </div>

                  <div class="pt-1 mb-4">
                    <button data-mdb-button-init data-mdb-ripple-init class="btn btn-info btn-lg btn-block" type="submit" :disabled="loading">
                      <span v-if="loading">Cargando...</span>
                      <span v-else>Crear Cuenta</span>
                    </button>
                  </div>

                  <router-link to="/login">
                    <p class="small mb-5 pb-lg-2"><a class="text-muted">¿Ya tienes una cuenta? Inicia sesión</a></p>
                  </router-link>

                  <p v-if="registerError" class="text-danger">{{ registerError }}</p>
                  <p v-if="successMessage" class="text-success">{{ successMessage }}</p>
                </form>
              </div>
            </div>
          </div>
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
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
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
