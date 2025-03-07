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
<section class="h-100 bg-white">
  <div class="container py-5 h-100 mt-3 mb-5">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col">
        <div class="card card-registration my-4">
          <div class="row g-0">
            <!-- Formulario en la parte izquierda -->
            <div class="col-xl-6">
              <div class="card-body p-md-5 text-black">
                <h3 class="mb-5 text-uppercase">Iniciar Sesión</h3>

                <!-- Formulario -->
                <form @submit.prevent="iniciarSesion">
                  <!-- Campo de Email -->
                  <div class="form-outline mb-4">
                    <input 
                      type="email" 
                      v-model="loginForm.email" 
                      class="form-control form-control-lg" 
                      required
                    />
                    <label class="form-label" for="email">Correo Electrónico</label>
                  </div>

                  <!-- Campo de Contraseña -->
                  <div class="form-outline mb-4">
                    <input 
                      type="password" 
                      v-model="loginForm.contraseña" 
                      class="form-control form-control-lg" 
                      required
                    />
                    <label class="form-label" for="password">Contraseña</label>
                  </div>

                  <!-- Botón de envío -->
                  <div class="pt-1 mb-4">
                    <button
                      class="btn btn-warning btn-lg btn-block"
                      type="submit"
                      :disabled="loading"
                    >
                      <span v-if="loading">Cargando...</span>
                      <span v-else>Iniciar Sesión</span>
                    </button>
                  </div>

                  <!-- Enlace para registrarse -->
                  <RouterLink to="/register">
                    <p class="small mb-5 pb-lg-2">
                      <a class="text-muted">No tienes cuenta, ¡Registrate ahora!</a>
                    </p>
                  </RouterLink>

                  <!-- Mensaje de error -->
                  <p v-if="loginError" class="text-danger">{{ loginError }}</p>
                </form>
              </div>
            </div>

            <!-- Imagen en la parte derecha -->
            <div class="col-xl-6 d-none d-xl-block">
              <img 
                src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-registration/img4.webp"
                alt="Sample photo" 
                class="img-fluid"
                style="border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;" 
              />
            </div>
          </div>
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
  display: flex;
  justify-content: center;
  align-items: center;
}

.container {
  background-color: #f8f9fa;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
  padding: 40px;
  width: 100%;
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

/* Asegura que la imagen se mantenga con el tamaño original */
.col-xl-6.d-none.d-xl-block {
  display: block;
  max-width: 100%;
}

.card-body {
  padding: 40px;
}

@media (max-width: 768px) {
  .col-xl-6 {
    display: none; /* Oculta la imagen en pantallas pequeñas */
  }
  .container {
    padding: 20px; /* Menos padding en pantallas pequeñas */
  }
}
</style>

