window.addEventListener("load", function(){
   
    
    //Ejemplo para ver los guías disponibles
    const API_URL = 'http://localhost/APIFT/api.php/';

    fetch(API_URL+'asignaciones?fecha=2025-02-10')
    .then(response => response.json())
    .then(data => console.log("Guías disponibles:", data))
    .catch(error => console.error("Error:", error));

})
