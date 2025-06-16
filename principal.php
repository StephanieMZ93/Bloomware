<?php
session_start();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id_usuario"])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bloomware - Interfaz Principal</title>
    <link rel="stylesheet" href="css/IPrincipal.css" />

</head>

<body>
    <div class="container">
        <header class="header">
            <img src="img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
            <a href="logout.php" class="cerrar-sesion">Cerrar Sesión</a>
        </header>

        <div class="card-container">

            <!-- Tarjeta Usuarios -->
            <div class="card">
                <img src="img/usuario.png" alt="Gestión de Usuarios">
                <div class="card-content">
                    <h2 class="card-title">Usuarios</h2>
                    <div class="card-actions">
                        <a href="Usuarios/usuarios.php" class="action-link">Nuevo Usuario</a>
                        <a href="Usuarios/usuarios.php" class="action-link">Ver Usuarios</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta Productos -->
            <div class="card">
                <img src="img/Productos.png" alt="Gestión de Productos">
                <div class="card-content">
                    <h2 class="card-title">Productos</h2>
                    <div class="card-actions">
                        <a href="Productos/producto.php" class="action-link">Registro Producto</a>
                        <a href="Productos/read.php" class="action-link">Ver Productos</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta Detalle Venta -->
            <div class="card">
                <img src="img/Venta.png" alt="Gestión de Ventas">
                <div class="card-content">
                    <h2 class="card-title">Detalle Venta</h2>
                    <div class="card-actions">
                        <a href="venta/venta.php" class="action-link">Nueva Venta</a>
                        <a href="venta/historial.php" class="action-link">Ver Ventas</a>
                    </div>
                </div>
            </div>


        </div>

    </div>
</body>

</html>