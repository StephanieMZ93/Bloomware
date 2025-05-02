<?php

// Incluir conexión
require_once(__DIR__ . '/conexion.php');

// Verificar conexión
if (!$conn) {
    header('Location: producto.php?error=' . urlencode('Error Crítico: No se pudo conectar a la base de datos.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Obtener datos del formulario
    $nombre_producto = $_POST['Nombre_Producto'] ?? '';
    $cantidad = isset($_POST['Cantidad']) ? (int)$_POST['Cantidad'] : null;
    $precio = isset($_POST['Precio']) ? (float)$_POST['Precio'] : null;
    $categoria = $_POST['Categoria'] ?? '';
    $lote = $_POST['Lote'] ?? '';
    $fecha_vencimiento = $_POST['FechaVencimiento'] ?? null;

    // --- Validación ---
    if (empty($nombre_producto) || $cantidad === null || $cantidad < 0 || $precio === null || $precio < 0 || empty($categoria) || empty($lote)) {
         header('Location: producto.php?error=' . urlencode('Datos inválidos o incompletos. Por favor, verifique.'));
         exit;
    }

    // Tratar fecha vacía como NULL
    if ($fecha_vencimiento === '') {
        $fecha_vencimiento = null;
    }

    // --- Consulta SQL 
    $sql = "INSERT INTO producto (Nombre_Producto, Cantidad, Precio, Categoria, Lote, Fecha_Vencimiento)
            VALUES (?, ?, ?, ?, ?, ?)"; //

    // 1. Preparar la consulta
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // 2. Vincular parámetros 
        //    Tipos: s(nombre), i(cantidad), d(precio), s(cat), s(lote), s(fecha)
        mysqli_stmt_bind_param($stmt, "sidsis",
            $nombre_producto,
            $cantidad,
            $precio,
            $categoria,
            $lote,
            $fecha_vencimiento
        );

        // 3. Ejecutar la consulta
        if (mysqli_stmt_execute($stmt)) {
            // Éxito: Redirigir con mensaje
            header('Location: producto.php?mensaje=' . urlencode('¡Producto registrado exitosamente!'));
            exit;
        } else {
            // Error en la ejecución: Redirigir con error
            header('Location: producto.php?error=' . urlencode('Error al registrar el producto: ' . mysqli_stmt_error($stmt)));
            exit;
        }

        // 4. Cerrar sentencia
        mysqli_stmt_close($stmt);

    } else {
        // Error al preparar: Redirigir con error
        header('Location: producto.php?error=' . urlencode('Error al preparar la consulta: ' . mysqli_error($conn)));
        exit;
    }

} else {
    header('Location: producto.php');
    exit;
}

// Cerrar conexión
mysqli_close($conn);
?>