<script setup>
import { ref } from 'vue';
import router from '@/router';

const emit = defineEmits(["sesionIniciada"]);
const loginForm = ref({ email: '', contraseña: '' });
const loginError = ref('');
const loading = ref(false);

// Validar formato del correo electrónico
const isValidEmail = (email) => {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
};

// Función para iniciar sesión
async function iniciarSesion() {
  // Validar que el email sea correcto antes de enviar
  if (!isValidEmail(loginForm.value.email)) {
    loginError.value = 'Correo electrónico no válido';
    return;
  }

  loading.value = true;
  try {
    const response = await fetch('http://localhost:8008/api.php/usuarios?login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(loginForm.value),
    });

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const data = await response.json();
    if (data.status === 'success') {
      loginError.value = '';
      // Guardar los datos de sesión (por ejemplo, token) en localStorage
      localStorage.setItem('sesionUser', JSON.stringify(data.user));
      // Emitir el evento sesionIniciada
      emit('sesionIniciada', data.user);
      // Redirigir al usuario al home
      router.push({ name: 'home' });
    } else {
      loginError.value = data.message || 'Usuario o contraseña incorrectos';
    }
  } catch (err) {
    loginError.value = 'Error al cargar los datos: ' + err.message;
  } finally {
    loading.value = false;
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
          <span class="h1 fw-bold mb-0">Iniciar Sesion</span>
        </div>

        <div class="d-flex align-items-center h-custom-3 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5 justify-content-center">
          <form style="width: 23rem;" @submit.prevent="iniciarSesion">

            <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Entrar</h3>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="email" id="loginEmail" v-model="loginForm.email" class="form-control form-control-lg" required />
              <label class="form-label" for="loginEmail">Correo Electrónico</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="password" id="loginPassword" v-model="loginForm.contraseña" class="form-control form-control-lg" required />
              <label class="form-label" for="loginPassword">Contraseña</label>
            </div>

            <div class="pt-1 mb-4">
              <button data-mdb-button-init data-mdb-ripple-init class="btn btn-info btn-lg btn-block" type="submit" :disabled="loading">
                <span v-if="loading">Cargando...</span>
                <span v-else>Iniciar Sesión</span>
              </button>
            </div>

            <p class="small mb-5 pb-lg-2"><a class="text-muted" href="#!">¿Has olvidado tu contraseña?</a></p>
            <p v-if="loginError" class="text-danger">{{ loginError }}</p>
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