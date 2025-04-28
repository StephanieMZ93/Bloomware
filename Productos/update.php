<?php
require_once('conexion.php'); // Incluir conexión

// Verificar si la conexión se estableció correctamente
if (!$conn) {
    header('Location: producto.php?error=' . urlencode('Error de conexión a la base de datos'));
    exit;
}

// Verificar si la solicitud es POST y si se recibió el ID del producto a actualizar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_ID_Producto'])) {
    $update_ID_Producto = $_POST['update_ID_Producto'];
    if (filter_var($update_ID_Producto, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) === false) {
        header('Location: producto.php?error=' . urlencode('ID de producto inválido para actualizar.'));
        exit;
    }

    $update_Nombre_Producto = $_POST['update_Nombre_Producto'] ?? '';
    $update_Cantidad = isset($_POST['update_Cantidad']) ? (int)$_POST['update_Cantidad'] : 0;
    $update_Precio = isset($_POST['update_Precio']) ? (float)$_POST['update_Precio'] : 0.0;
    $update_Categoria = $_POST['update_Categoria'] ?? '';
    $update_Lote = $_POST['update_Lote'] ?? '';
    $update_Fecha_Vencimiento = $_POST['update_Fecha_Vencimiento'] ?? null;


    if (empty($update_Nombre_Producto) || $update_Cantidad < 0 || $update_Precio < 0 || empty($update_Categoria) || empty($update_Lote)) {
         header('Location: producto.php?error=' . urlencode('Datos inválidos o incompletos para actualizar.') . '&id_edit=' . $update_ID_Producto); // Devolver al formulario con error
        exit;
    }

    if ($update_Fecha_Vencimiento === '') {
        $update_Fecha_Vencimiento = null;
    }

    $sql = "UPDATE producto SET
                Nombre_Producto = ?,
                Cantidad = ?,
                Precio = ?,
                Categoria = ?,
                Lote = ?,
                Fecha_Vencimiento = ?
            WHERE ID_Producto = ?"; 

    // 1. Preparar la consulta
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sidsssi", 
            $update_Nombre_Producto,
            $update_Cantidad,
            $update_Precio,
            $update_Categoria,
            $update_Lote,
            $update_Fecha_Vencimiento,
            $update_ID_Producto      
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: producto.php?mensaje=" . urlencode("¡Producto actualizado exitosamente!"));
            exit;
        } else {
            header("Location: producto.php?error=" . urlencode("Error al actualizar el producto.") . '&id_edit=' . $update_ID_Producto);
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: producto.php?error=" . urlencode("Error al preparar la actualización.") . '&id_edit=' . $update_ID_Producto);
        exit;
    }

} else {
    header('Location: producto.php?error=' . urlencode('Solicitud inválida para actualizar.'));
    exit;
}
// Cerrar la conexión
mysqli_close($conn);
?>