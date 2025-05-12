<?php
require_once('conexion.php'); // Incluir conexión

// Verificar conexión
if (!$conn) {
    // Redirigir a la PÁGINA DEL FORMULARIO con error
    header('Location: producto.php?error=' . urlencode('Error de conexión a la base de datos'));
    exit;
}

// Verificar si es POST y si se recibió el ID del producto a actualizar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_ID_Producto'])) {

    // --- Obtener y Validar ID (Nombre de POST correcto) ---
    $update_ID_Producto = $_POST['update_ID_Producto'];
    if (filter_var($update_ID_Producto, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) === false) {
        // Redirigir a la PÁGINA DEL FORMULARIO con error
        header('Location: producto.php?error=' . urlencode('ID de producto inválido para actualizar.'));
        exit;
    }

    // --- Obtener otros datos del formulario  ---
    $update_Nombre_Producto = $_POST['Nombre_Producto'] ?? ''; 
    $update_Cantidad = isset($_POST['Cantidad']) ? (int)$_POST['Cantidad'] : 0; 
    $update_Precio = isset($_POST['Precio']) ? (float)$_POST['Precio'] : 0.0; 
    $update_Categoria = $_POST['Categoria'] ?? ''; 
    $update_Lote = $_POST['Lote'] ?? ''; //
    $update_Fecha_Vencimiento = $_POST['FechaVencimiento'] ?? null; 

    // --- Validación ---
    if (empty($update_Nombre_Producto) || $update_Cantidad < 0 || $update_Precio < 0 || empty($update_Categoria) || empty($update_Lote)) {
         $errorParams = http_build_query([
            'error' => 'Datos inválidos o incompletos para actualizar.',
            'update_id' => $update_ID_Producto, // Pasar el ID de vuelta
            'nombre' => $update_Nombre_Producto, // Pasar datos de vuelta
            'cantidad' => $update_Cantidad,
            'precio' => $update_Precio,
            'categoria' => $update_Categoria,
            'lote' => $update_Lote,
            'fecha' => $update_Fecha_Vencimiento ?? ''
         ]);
         header('Location: producto.php?' . $errorParams);
        exit;
    }

    // Tratar fecha vacía como NULL
    if ($update_Fecha_Vencimiento === '') {
        $update_Fecha_Vencimiento = null;
    }

    // --- Consulta SQL PREPARADA ---
    $sql = "UPDATE producto SET
                Nombre_Producto = ?,
                Cantidad = ?,
                Precio = ?,
                Categoria = ?,
                Lote = ?,
                Fecha_Vencimiento = ?
            WHERE ID_Producto = ?"; 

    // Preparar
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Vincular parámetros 
        mysqli_stmt_bind_param($stmt, "sidsisi", // s(nombre), i(cantidad), d(precio), s(cat), s(lote), s(fecha), i(ID WHERE)
            $update_Nombre_Producto,
            $update_Cantidad,
            $update_Precio,
            $update_Categoria,
            $update_Lote,
            $update_Fecha_Vencimiento,
            $update_ID_Producto
        );

        //  Ejecutar
        if (mysqli_stmt_execute($stmt)) {
            header("Location: read.php?mensaje=" . urlencode("¡Producto (ID: " . $update_ID_Producto . ") actualizado exitosamente!"));
            exit;
        } else {
            $errorParams = http_build_query([
                'error' => 'Error al actualizar el producto: ' . mysqli_stmt_error($stmt), 
                'update_id' => $update_ID_Producto, 
            ]);
            header("Location: producto.php?" . $errorParams);
            exit;
        }

        // Cerrar sentencia
        mysqli_stmt_close($stmt);

    } else {
        // Redirigir a la PÁGINA DEL FORMULARIO con error y el ID
         $errorParams = http_build_query([
            'error' => 'Error al preparar la actualización: ' . mysqli_error($conn),
            'update_id' => $update_ID_Producto 
         ]);
        header("Location: producto.php?" . $errorParams);
        exit;
    }

} else {
    // Redirigir a la PÁGINA DEL FORMULARIO con error
    header('Location: producto.php?error=' . urlencode('Solicitud inválida para actualizar.'));
    exit;
}

// Cerrar la conexión
mysqli_close($conn);
?>