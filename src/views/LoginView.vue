<script setup>
import { ref } from 'vue';
import router from '@/router';
import Swal from 'sweetalert2';

const emit = defineEmits(['sesionIniciada']);
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
    // Mostrar alerta de error
    Swal.fire({
      icon: 'error',
      title: '¡Error!',
      text: loginError.value,
      confirmButtonText: 'Aceptar',
    });
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
    if (data.status === 'success' && data.user) {
      loginError.value = '';
      
      // Verificar que el usuario tiene rol
      if (!data.user.rol) {
        throw new Error("El usuario autenticado no tiene un rol definido.");
      }

      // Guardar usuario en localStorage
      localStorage.setItem('sesionUser', JSON.stringify(data.user));

      // Emitir evento con los datos del usuario
      emit('sesionIniciada', data.user);

      // Mostrar alerta de éxito
      Swal.fire({
        icon: 'success',
        title: '¡Bienvenido!',
        text: 'Has iniciado sesión correctamente.',
        confirmButtonText: 'Aceptar',
      });

      // Redirigir al home o cualquier página
      router.push({ name: 'home' });
    } else {
      loginError.value = data.message || 'Usuario o contraseña incorrectos';
      // Mostrar alerta de error
      Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: loginError.value,
        confirmButtonText: 'Aceptar',
      });
    }
  } catch (err) {
    loginError.value = 'Error al cargar los datos: ' + err.message;
    // Mostrar alerta de error
    Swal.fire({
      icon: 'error',
      title: '¡Error!',
      text: loginError.value,
      confirmButtonText: 'Aceptar',
    });
  } finally {
    loading.value = false;
  }
}
</script>

<template>
    <!--Dar una vuelta al diseño del login-->

<section class="vh-100 d-flex justify-content-center align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 text-black">
        <div class="px-5 text-center">
          <h1 class="fw-bold mb-3">Iniciar Sesión</h1>
        </div>
        <div class="d-flex align-items-center px-5 mt-5 justify-content-center">
          <form style="width: 23rem;" @submit.prevent="iniciarSesion">
            <h3 class="fw-normal mb-3 pb-3">Entrar</h3>

            <div class="form-outline mb-4">
              <input
                type="email"
                v-model="loginForm.email"
                class="form-control form-control-lg"
                required
              />
              <label class="form-label">Correo Electrónico</label>
            </div>

            <div class="form-outline mb-4">
              <input
                type="password"
                v-model="loginForm.contraseña"
                class="form-control form-control-lg"
                required
              />
              <label class="form-label">Contraseña</label>
            </div>

            <div class="pt-1 mb-4">
              <button
                class="btn btn-info btn-lg btn-block"
                type="submit"
                :disabled="loading"
              >
                <span v-if="loading">Cargando...</span>
                <span v-else>Iniciar Sesión</span>
              </button>
            </div>
            <RouterLink to="/register"><p class="small mb-5 pb-lg-2"><a class="text-muted">No tienes cuenta, ¡Registrate ahora!</a></p></RouterLink>

            <p v-if="loginError" class="text-danger">{{ loginError }}</p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
</template>

<style scoped>
/* Estilos del formulario */
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
