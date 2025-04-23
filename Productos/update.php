<?php
require 'conexion.php';

if (isset($_POST['update'])) {
    $update_ID_Producto = $_POST['update_ID_Producto'];
    $update_Nombre_producto = $_POST ['update_Nombre_Producto'];
    $update_Cantidad = $_POST ['update_Cantidad'];
    $update_Precio = $_POST ['update_Precio'];
    $update_Categoria = $_POST ['update_Categoria'];
    $update_Lote = $_POST ['update_Lote'];
    $update_Fecha_Vencimiento = $_POST ['update_Fecha_Vencimiento'];

    $update_query = "UPDATE producto SET
                    Nombre_Producto = '$update_Nombre_producto',
                    Cantidad = '$update_Cantidad',
                    Precio = '$update_Precio',
                    Categoria = '$update_Categoria',
                    Lote = '$update_Lote',
                    Fecha_Vencimiento = '$update_Fecha_Vencimiento',
                    WHERE ID_Producto";
    if (mysqli_query($conn, $update_query)) {
        header("Location: producto.php?mensaje=¡Producto actualizado exitosamente!");
    } else {
        echo "Error al actualizar el producto: " .mysqli_error($conn);
}
}
?>