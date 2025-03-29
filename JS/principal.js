document.addEventListener('DOMContentLoaded', () => {

    const cards = document.querySelectorAll('.card');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            const cardTitle = card.querySelector('.card-title').textContent;

            switch (cardTitle) {
                case 'Usuarios':
                    window.location.href = 'http://127.0.0.1:5500/Usuarios.html';
                case 'Proveedores':
                    window.location.href = 'http://127.0.0.1:5500/proveedores.html';
                    break;
                case 'Productos':
                    window.location.href = 'http://127.0.0.1:5500/producto.html'; 
                    break;
                case 'Clientes':
                    window.location.href = 'http://127.0.0.1:5500/cliente.html';    
                    break;
                default:
                    console.log('Clic en tarjeta sin acción definida.');
            }
        });
    });

    const cerrarSesionButton = document.querySelector('.cerrar-sesion');
    cerrarSesionButton.addEventListener('click', (event) => {
        window.location.href = 'http://127.0.0.1:5500/index.html';
        console.log("Cerrando sesión...");
    });
});