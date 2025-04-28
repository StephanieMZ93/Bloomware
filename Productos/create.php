<?php
// Archivo: create.php
require_once('conexion.php'); // Incluir conexión a la base de datos

// Verificar si la conexión se estableció correctamente
if (!$conn) {
    header('Location: producto.php?error=' . urlencode('Error de conexión a la base de datos'));
    exit; 
}
// Asegurarse de que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_producto = $_POST['Nombre_Producto'] ?? '';
    $cantidad = isset($_POST['Cantidad']) ? (int)$_POST['Cantidad'] : 0; // Convertir a entero
    $precio = isset($_POST['Precio']) ? (float)$_POST['Precio'] : 0.0; // Convertir a float
    $categoria = $_POST['Categoria'] ?? '';
    $lote = $_POST['Lote'] ?? '';
    $fecha_vencimiento = $_POST['Fecha_Vencimiento'] ?? null; // Permitir NULL si no se envía

    if (empty($nombre_producto) || $cantidad <= 0 || $precio <= 0 || empty($categoria) || empty($lote)) {
        header('Location: producto.php?error=' . urlencode('Datos inválidos o incompletos. Por favor, verifique.'));
        exit;
    }

    if ($fecha_vencimiento === '') {
        $fecha_vencimiento = null;
    }

    $sql = "INSERT INTO producto (Nombre_Producto, Cantidad, Precio, Categoria, Lote, Fecha_Vencimiento)
            VALUES (?, ?, ?, ?, ?, ?)";

    // Preparar la consulta
    $stmt = mysqli_prepare($conn, $sql);

    // Verificar si la preparación fue exitosa
    if ($stmt) {
mysqli_stmt_bind_param($stmt, "sidsss", 
            $nombre_producto,
            $cantidad,
            $precio,
            $categoria,
            $lote,
            $fecha_vencimiento
        );

        // Ejecutar la consulta preparada
        if (mysqli_stmt_execute($stmt)) {
            header('Location: producto.php?mensaje=' . urlencode('Producto registrado exitosamente'));
            exit;
        } else {
            header('Location: producto.php?error=' . urlencode('Error al registrar el producto.'));
            exit;
        }
        
        mysqli_stmt_close($stmt);

    } else {
        header('Location: producto.php?error=' . urlencode('Error al preparar la consulta.'));
        exit;
    }
} else {
    header('Location: producto.php');
    exit;
}
// Cerrar la conexión 
mysqli_close($conn);

?>