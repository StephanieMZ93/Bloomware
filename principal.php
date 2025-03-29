<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    // Si no ha iniciado sesión, redirigir al formulario de inicio de sesión
    header("Location: index.php"); // Redirige al archivo index.php (o al que contenga el formulario de login)
    exit();
}

// Si el usuario ha iniciado sesión, se muestra la página principal
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bloomware-interfaz principal</title>
    <link rel="stylesheet" href="/css/IPrincipal.css" />
</head>
<body>
    <div class="container">
        <header class="header">
            <img src="img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
            <button class="cerrar-sesion">
                <a href="logout.php" style="color:white; text-decoration:none;">Cerrar Sesión</a>
            </button>
        </header>

        <div class="card-container">
            <div class="card">
                <img src="/img/usuario.png" alt="Usuarios">
                <div class="card-content">
                    <a href="#" class="card-title">Usuarios</a>
                </div>
            </div>
            <div class="card">
                <img src="/img/Proveedores.png" alt="Proveedores">
                <div class="card-content">
                    <a href="#" class="card-title">Proveedores</a>
                </div>
            </div>
            <div class="card">
                <img src="/img/Productos.png" alt="Productos">
                <div class="card-content">
                    <a href="#" class="card-title">Productos</a>
                </div>
            </div>
            <div class="card">
                <img src="/img/Clientes.png" alt="Clientes">
                <div class="card-content">
                    <a href="#" class="card-title">Clientes</a>
                </div>
            </div>
            </div>
        </div>
    </div>
    <script src="/JS/principal.js"></script>
</body>
</html>