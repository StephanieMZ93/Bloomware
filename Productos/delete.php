<?php
require_once('conexion.php'); 

// Verificar si la conexión se estableció correctamente
if (!$conn) {
    header('Location: producto.php?error=' . urlencode('Error de conexión a la base de datos'));
    exit;
}
// Verificar si se recibió el parámetro y no está vacío
if (isset($_GET['delete_ID_Producto']) && !empty($_GET['delete_ID_Producto'])) {
    $delete_id = $_GET['delete_ID_Producto'];

    if (filter_var($delete_id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) === false) {
        header('Location: producto.php?error=' . urlencode('ID de producto inválido.'));
        exit;
    }

    // Consulta SQL PREPARADA para eliminar

    $sql = "DELETE FROM producto WHERE ID_Producto = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: producto.php?mensaje=" . urlencode("¡Producto eliminado exitosamente!"));
            exit; 
        } else {
            header("Location: producto.php?error=" . urlencode("Error al eliminar el producto."));
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: producto.php?error=" . urlencode("Error al preparar la consulta de eliminación."));
        exit;
    }

} else {
    header("Location: producto.php?error=" . urlencode("No se especificó un ID de producto para eliminar."));
    exit;
}

mysqli_close($conn);
?>