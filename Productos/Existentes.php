<?php
// existentes.php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}
require_once(__DIR__ . '/conexion.php');
if (!isset($conn) || !$conn) {
     die("Error crítico: No se pudo establecer la conexión a la base de datos desde existentes.php.");
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

    <div class="page-container table-page-container">
        <header class="table-header">
             <a href="../principal.php"><img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" /></a>
             <h1>Productos Existentes</h1>
             <div class="header-actions">
                 <a href="../principal.php" class="btn-accion btn-regresar">Regresar</a>
                 <a href="producto.php" class="btn-accion btn-nuevo">Registrar Nuevo</a>
             </div>
        </header>

        <div class="table-wrapper">
            <table>
                <thead>
                     <th>ID Producto</th>
                     <th>Nombre Producto</th>
                     <th>Cantidad</th>
                     <th>Precio</th>
                     <th>Categoría</th>
                     <th>Lote</th>
                     <th>Fecha Vencimiento</th>
                     <th>Acciones</th>
                </thead>
                <tbody>
                    <?php include 'read.php'; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
        if (isset($conn)) { mysqli_close($conn); }
    ?>


    <!-- === JAVASCRIPT PARA BOTONES === -->

    <script>
        /* Función para confirmar y redirigir para eliminar un producto*/
        function eliminarProducto(productoId) {
            if (confirm(`¿Estás seguro de que deseas eliminar el producto con ID ${productoId}? Esta acción no se puede deshacer.`)) {
                window.location.href = `delete.php?delete_ID_Producto=${productoId}`;
            } else {
                // Si el usuario cancela, no hacer nada
                console.log("Eliminación cancelada por el usuario.");
            }
        }

        /**Función para redirigir al formulario de producto para actualizarlo */
        function mostrarFormularioActualizar(id, nombre, cantidad, precio, categoria, lote, fechaVencimiento) {
            console.log("Actualizando producto ID:", id); 

            // Construir la URL con los parámetros GET codificados
            const params = new URLSearchParams({
                update_id: id,
                nombre: nombre,
                cantidad: cantidad,
                precio: precio,
                categoria: categoria,
                lote: lote,
                fecha: fechaVencimiento !== 'N/A' ? fechaVencimiento : '' // Enviar fecha vacía si es N/A
            });

            // Redirigir a producto.php con los datos para edición
            window.location.href = `producto.php?${params.toString()}`;
        }

    </script>

</body>
</html>