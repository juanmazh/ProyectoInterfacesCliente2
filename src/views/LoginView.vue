<script setup>
import {ref} from "vue"
import router from "@/router";

const emit = defineEmits(["sesionIniciada"]);
const form = ref({ usuario: '', password: '' });
const error = ref('');

async function iniciarSesion() {
  try {
    const response = await fetch('/usuarios.json'); 
    const usuarios = await response.json();

    const usuarioEncontrado = usuarios.find(
      (u) => u.usuario === form.value.usuario && u.password === form.value.password
    );

    if (usuarioEncontrado) {
      //TODO: HABRÍA QUE NOTIFICAR A APP.VUE CON UN EMIT PARA QUE SEPA QUE LA SESIÓN ESTÁ INICIADA
      emit("sesionIniciada",{usuario: usuarioEncontrado.usuario, rol:usuarioEncontrado.rol});
      error.value = '';
      router.push({name:"home"});

      
    } else {
      error.value = 'Usuario o contraseña incorrectos';
    }
  } catch (err) {
    error.value = 'Error al cargar los datos';
  }
}
</script>
<template>
<section class="vh-100">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6 text-black">

        <div class="px-5 ms-xl-4">
          <i class="fas fa-crow fa-2x me-3 pt-5 mt-xl-4" style="color: #709085;"></i>
          <span class="h1 fw-bold mb-0">Logo</span>
        </div>

        <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">

          <form style="width: 23rem;">

            <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Log in</h3>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="email" id="form2Example18" class="form-control form-control-lg" />
              <label class="form-label" for="form2Example18">Email address</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="password" id="form2Example28" class="form-control form-control-lg" />
              <label class="form-label" for="form2Example28">Password</label>
            </div>

            <div class="pt-1 mb-4">
              <button data-mdb-button-init data-mdb-ripple-init class="btn btn-info btn-lg btn-block" type="button">Login</button>
            </div>

            <p class="small mb-5 pb-lg-2"><a class="text-muted" href="#!">Forgot password?</a></p>
            <p>Don't have an account? <a href="#!" class="link-info">Register here</a></p>

          </form>

      </div>
    </div>
  </div>
</section>


  <!--  <form> 
        <label>Nombre</label>
        <input v-model="form.usuario" type="text" />
        <label>Contraseña</label>
        <input v-model="form.password"  type="password" />
        <p v-if="error" class="text-danger mt-2">{{ error }}</p>
        <button @click.prevent="iniciarSesion">Iniciar sesión</button> 
    </form>-->
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