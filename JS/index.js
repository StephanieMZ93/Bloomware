document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.Formulario');
    const usernameInput = document.getElementById('Username');
    const passwordInput = document.getElementById('password');
    const loginButton = document.querySelector('.iniciar-sesion');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const username = usernameInput.value;
        const password = passwordInput.value;

        if (username.trim() === "" || password.trim() === "") {
            alert("Por favor, complete todos los campos.");
            return;
        }

        if (username === "ejemplo@gmail.com" && password === "123456.") {
            // Redireccionar a la página principal (como en tu botón)
            window.location.href = "http://127.0.0.1:5500/principal.html"; 
            alert("Inicio de sesión exitoso (simulado)");
        } else {
            alert("Credenciales incorrectas. Intente nuevamente.");
        }
    });

    loginButton.addEventListener('click', function(event) {
        event.preventDefault();

        form.dispatchEvent(new Event('submit'));
    });
});