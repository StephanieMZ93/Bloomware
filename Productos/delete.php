<?php
require 'Productos/conexion.php';

if (isset($_GET['delete_ID_Producto'])) {
    $delete_id = $_GET['delete_ID_Producto'];
    $delete_query = "DELETE FROM producto WHERE ID = $delete_ID_Producto";

    if (mysqli_query($conn, $delete_query)) {
        header("Location: producto.php?mensaje=¡Producto eliminado exitosamente!"); 
    } else {
        echo "Error al eliminar la reservación: " .mysqli_error($conn);
    }
}