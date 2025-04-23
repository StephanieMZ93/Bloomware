<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    // Si no ha iniciado sesión, redirigir al formulario de inicio de sesión
    header("Location: index.php");
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
    <link rel="stylesheet" href="css/IPrincipal.css" />
</head>
<body>
    <div class="container">
        <header class="header">
            <img src="img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
            <a href="logout.php" class="cerrar-sesion">Cerrar Sesión</a>
        </header>

        <div class="card-container">
            <div class="card">
                <a href="/Usuarios/usuarios.php">
                    <img src="img/usuario.png" alt="Usuarios">
                    <div class="card-content">
                        <h2 class="card-title">Usuarios</h2>
                    </div>
                </a>
            </div>
            <div class="card">
                <a href="/Proveedores/proveedores.php">
                    <img src="img/Proveedores.png" alt="Proveedores">
                    <div class="card-content">
                        <h2 class="card-title">Proveedores</h2>
                    </div>
                </a>
            </div>
            <div class="card">
                <a href="/Productos/producto.php">
                    <img src="img/Productos.png" alt="Productos">
                    <div class="card-content">
                        <h2 class="card-title">Productos</h2>
                    </div>
                </a>
            </div>
            <div class="card">
                <a href="/clientes/cliente.php">
                    <img src="img/Clientes.png" alt="Clientes">
                    <div class="card-content">
                        <h2 class="card-title">Clientes</h2>
                    </div>
                </a>
            </div>
            <div class="card">
                <a href="#">
                    <img src="img/Reportes.png" alt="Reportes">
                    <div class="card-content">
                        <h2 class="card-title">Reportes</h2>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <script src="JS/principal.js"></script>
</body>
</html>