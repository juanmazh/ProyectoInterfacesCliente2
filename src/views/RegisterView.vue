<script setup>
import { ref } from 'vue';
import router from '@/router';

const registerForm = ref({ nombre: '', email: '', contraseña: '' });
const registerError = ref('');

async function registrarUsuario() {
  try {
    const response = await fetch('http://localhost:8008/api.php/usuarios', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(registerForm.value),
    });

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const data = await response.json();
    if (data.status === 'success') {
      registerError.value = '';
      //Cambiar, no sea un alert, un flash mesage con redireccion al login a los 3 segundos
      alert('Registro exitoso. Ahora puedes iniciar sesión.');
      router.push({ name: 'login' });
    } else {
      registerError.value = data.message || 'Error al registrar el usuario';
    }
  } catch (err) {
    registerError.value = 'Error al registrar el usuario';
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
              <button data-mdb-button-init data-mdb-ripple-init class="btn btn-info btn-lg btn-block" type="submit">Crear Cuenta</button>
            </div>

            <p class="small mb-5 pb-lg-2"><a class="text-muted" href="#!">¿Ya tienes una cuenta? Inicia sesión</a></p>
            <p v-if="registerError" class="text-danger">{{ registerError }}</p>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>
</template>
<style scoped>
.bg-image-vertical {
position: relative;
overflow: hidden;
background-repeat: no-repeat;
background-position: right center;
background-size: auto 100%;
}

@media (min-width: 1025px) {
.h-custom-2 {
height: 100%;
}
}
</style>