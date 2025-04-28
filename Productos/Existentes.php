<?php

// Podrías incluir aquí la verificación de sesión si esta página también requiere login
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Existentes - Bloomware</title>
    <link rel="stylesheet" href="css/Estilos.css">
</head>

<body>

    <!-- Contenedor principal para la tabla -->
    <div class="table-page-container">

        <header class="table-header">
            <!-- botón para volver -->
            <a href="../principal.php" class="btn-accion btn-regresar">Regresar</a>
            <img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" /></a>
            <h1>Productos Existentes</h1>
            <a href="producto.php" class="btn-accion btn-nuevo">Registrar Nuevo Producto</a>
        </header>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Lote</th>
                        <th>Fecha Vencimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    include 'read.php';
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>