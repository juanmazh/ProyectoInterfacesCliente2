<template>
  <div class="carousel-container">
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="../assets/images/jaen.jpg" class="d-block w-100" alt="...">
          <div class="carousel-caption d-none d-md-block">
            <h5>Calle Bernabe Soriano Jaén</h5>
            <p>Una de las emblematicas calles de la capital de Jaén con sus hermosas vistas de cara a la majestuosa catedral</p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="https://malagacatedral.com/wp-content/uploads/catedral-malaga-exterior-4.webp" class="d-block w-100" alt="...">
          <div class="carousel-caption d-none d-md-block">
            <h5>Catedral de Málaga</h5>
            <p>Increible y sensacional experiencia en la visita a la catedral de malaga, siendo un simbolo para toda la provincia malagueña</p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="https://fotografias.larazon.es/clipping/cmsimages01/2020/04/22/753D8399-AAB8-4F2B-B58F-DD27FE1B9B8F/98.jpg?crop=6000,3376,x0,y430&width=1900&height=1069&optimize=low&format=webply" class="d-block w-100" alt="...">
          <div class="carousel-caption d-none d-md-block">
            <h5>Sagrada Familia Barcelona</h5>
            <p>Una impresionante experiencia visitando el que es para muchos la mayor obra aún en construcción para admirar la increible obra de la humanidad</p>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.carousel-container {
  width: 116vh; /* Ajusta el tamaño según tus necesidades */
  margin: 0 auto; /* Centra el carrusel horizontalmente */
}

.carousel-caption {
  background-color: rgba(0, 0, 0, 0.5); /* Fondo semitransparente para mejorar el contraste */
  padding: 1rem;
  border-radius: 0.5rem;
}

.carousel-caption h5,
.carousel-caption p {
  color: #fff; /* Color blanco para el texto */
}
</style>