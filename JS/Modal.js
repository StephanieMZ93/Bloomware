document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar los elementos necesarios
    const openModalButton = document.getElementById('openProductTableBtn');
    const closeModalButton = document.getElementById('closeProductTableBtn');
    const modalOverlay = document.getElementById('productTableModal');

    
    if (openModalButton && closeModalButton && modalOverlay) {
        openModalButton.addEventListener('click', function() {
            modalOverlay.classList.add('modal--open');
        });

        // Cerrar modal al hacer clic en el botón de cierre (X)
        closeModalButton.addEventListener('click', function() {
            modalOverlay.classList.remove('modal--open');
        });

        // Cerrar modal al hacer clic FUERA del contenido (en el overlay)
        modalOverlay.addEventListener('click', function(event) {
            if (event.target === modalOverlay) {
                modalOverlay.classList.remove('modal--open');
            }
        });

        // Cerrar modal al presionar la tecla 'Escape'
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modalOverlay.classList.contains('modal--open')) {
                modalOverlay.classList.remove('modal--open');
            }
        });

    } else {
        console.error("Error: No se encontraron los elementos del modal (botones o contenedor).");
    }
});