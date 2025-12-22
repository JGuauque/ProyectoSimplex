document.querySelectorAll('.dropdown-toggle').forEach(item => {
    item.addEventListener('click', function(event) {
      event.preventDefault(); // Evitar el comportamiento predeterminado del enlace.
      this.parentElement.classList.toggle('active'); // Agregar o quitar la clase 'active'.
    });
  });
  