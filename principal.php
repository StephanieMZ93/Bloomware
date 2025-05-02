<?php
session_start();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
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
                <img src="img/usuario.png" alt="Usuarios">
                <div class="card-content">
                    <h2 class="card-title">Usuarios</h2>
                    <div class="card-actions">
                        <a href="Usuarios/usuarios.php?view=form" class="action-link">Registrar</a>
                        <a href="Usuarios/usuarios.php?view=table" class="action-link">Ver Usuarios</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta Proveedores -->
            <div class="card">
                <img src="img/Proveedores.png" alt="Proveedores">
                <div class="card-content">
                    <h2 class="card-title">Proveedores</h2>
                    <div class="card-actions">
                        <a href="Proveedores/proveedores.php?view=form" class="action-link">Registrar</a>
                        <a href="Proveedores/proveedores.php?view=table" class="action-link">Ver Proveedores</a>
                    </div>
                </div>
            </div>
            <!-- Tarjeta Productos -->
            <div class="card">
                <img src="img/Productos.png" alt="Productos">
                <div class="card-content">
                    <h2 class="card-title">Productos</h2>
                    <!-- Enlaces de acciones -->
                    <div class="card-actions">
                        <a href="Productos/producto.php" class="action-link">Registrar</a> 
                        <a href="Productos/existentes.php" class="action-link">Ver productos</a> 
                    </div>
                </div>
            </div>

            <!-- Tarjeta Clientes -->
            <div class="card">
                <img src="img/Clientes.png" alt="Clientes">
                <div class="card-content">
                    <h2 class="card-title">Clientes</h2>
                    <div class="card-actions">
                        <a href="clientes/cliente.php?view=form" class="action-link">Registrar</a>
                        <a href="clientes/cliente.php?view=table" class="action-link">Ver Clientes</a>
                    </div>
                </div>
            </div>

        </div> 

    </div> 

    <!-- === ESTRUCTURA DEL MODAL  -->
 
    <div id="productTableModal" class="modal-overlay">
        <div class="modal-content"> 

            <!-- Botón para cerrar el modal -->
            <button id="closeProductTableBtn" class="modal-close-btn">×</button> <!-- Usar × HTML entity -->
        </div>
    </div>

    <script src="js/Modal.js"></script>
</body>
</html>