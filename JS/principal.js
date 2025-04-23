document.addEventListener('DOMContentLoaded', () => {

    const cards = document.querySelectorAll('.card');

    cards.forEach(card => {
        card.addEventListener('click', () => {

            const titleElement = card.querySelector('.card-title');
            if (titleElement) {
                const cardTitle = titleElement.textContent;
            switch (cardTitle) {
                case 'Usuarios':
                    window.location.href = 'http://localhost/Bloomware/principal.php/usuarios.php';
                    break;
                case 'Proveedores':
                    window.location.href = 'http://localhost/Bloomware/principal.php/proveedores.php';
                    break;
                case 'Productos':
                    window.location.href = 'http://localhost/Bloomware/principal.php/producto.php'; 
                    break;
                case 'Clientes':
                    window.location.href = 'http://localhost/Bloomware/principal.php/cliente.php';    
                    break;
                default:
                    console.log('Clic en tarjeta sin acción definida.' cardTitle);
                }
            } else {
                console.warn('No se encontro .card-title dentro de la tarjeta:', card);
            }
        });
    });

    const cerrarSesionButton = document.querySelector('.cerrar-sesion');
    if (cerrarSesionButton) {
        cerrarSesionButton.addEventListener('click', (event) => {
        window.location.href = 'http://localhost/Bloomware/index.php';
        console.log("Cerrando sesión...");
    });
    } else {
        console.warn('No se encontró el botón .cerrar-sesion');
    }
});