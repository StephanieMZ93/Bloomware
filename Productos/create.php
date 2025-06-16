<?php
// Archivo: Productos/create.php

session_start(); // Iniciar sesión si es necesario para alguna lógica o auditoría

// Incluir conexión
require_once(__DIR__ . '/../BD/Conexion.php');

// Verificar si la conexión se estableció correctamente
if (!$conn || $conn->connect_error) {
    $db_error_message = isset($conn) && $conn->connect_error ? $conn->connect_error : mysqli_connect_error();
    error_log("Error crítico de conexión en create.php: " . $db_error_message);
    // Redirigir al formulario con un error genérico si la conexión falla al inicio
    header('Location: producto.php?error=' . urlencode('Error Crítico: No se pudo conectar a la base de datos.'));
    exit;
}

// Asegurarse de que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Obtener datos del formulario (ID_Producto ya no se envía desde el form para creación)
    $nombre_producto = trim($_POST['Nombre_Producto'] ?? '');
    $cantidad_nueva = isset($_POST['Cantidad']) ? (int)$_POST['Cantidad'] : null;
    $precio = isset($_POST['Precio']) ? (float)$_POST['Precio'] : null;
    $categoria = trim($_POST['Categoria'] ?? '');
    $lote = trim($_POST['Lote'] ?? '');
    $fecha_vencimiento = $_POST['FechaVencimiento'] ?? ''; // Obtener como string

    // --- Validación básica de los datos recibidos ---
    // ID_Producto no se valida aquí porque es para NUEVOS registros (asumimos AUTO_INCREMENT)
    if (empty($nombre_producto) || $cantidad_nueva === null || $cantidad_nueva <= 0 || $precio === null || $precio < 0 || empty($categoria) || empty($lote) || empty($fecha_vencimiento)) {
         header('Location: producto.php?error=' . urlencode('Datos inválidos o incompletos. Nombre, Cantidad (>0), Precio (>=0), Categoría, Lote y Fecha de Vencimiento son obligatorios.'));
         exit;
    }
    // Validar formato de fecha (YYYY-MM-DD)
    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fecha_vencimiento)) {
        header('Location: producto.php?error=' . urlencode('Formato de Fecha de Vencimiento inválido. Use YYYY-MM-DD.'));
        exit;
    }
    // --- Fin Validación ---


    // --- Lógica para buscar producto existente y decidir si actualizar o insertar ---

    // 1. Buscar un producto con el mismo Nombre, Lote Y Fecha de Vencimiento
    $sql_buscar = "SELECT ID_Producto, Cantidad FROM producto 
                   WHERE Nombre_Producto = ? AND Lote = ? AND Fecha_Vencimiento = ?";
    $stmt_buscar = mysqli_prepare($conn, $sql_buscar);

    if (!$stmt_buscar) {
        error_log("Error al preparar la consulta de búsqueda en create.php: " . mysqli_error($conn));
        header('Location: producto.php?error=' . urlencode('Error del sistema al buscar producto.'));
        exit;
    }

    mysqli_stmt_bind_param($stmt_buscar, "sss", $nombre_producto, $lote, $fecha_vencimiento);

    if (!mysqli_stmt_execute($stmt_buscar)) {
        error_log("Error al ejecutar la consulta de búsqueda en create.php: " . mysqli_stmt_error($stmt_buscar));
        header('Location: producto.php?error=' . urlencode('Error del sistema al verificar producto.'));
        mysqli_stmt_close($stmt_buscar);
        exit;
    }

    $resultado_busqueda = mysqli_stmt_get_result($stmt_buscar);
    $producto_existente = mysqli_fetch_assoc($resultado_busqueda);
    mysqli_stmt_close($stmt_buscar);


    // 2. Decidir acción: UPDATE (cantidad) o INSERT (nuevo registro)
    if ($producto_existente) {
        // --- PRODUCTO EXISTENTE CON MISMO NOMBRE, LOTE Y FECHA: ACTUALIZAR CANTIDAD ---
        $id_producto_existente = $producto_existente['ID_Producto'];
        $cantidad_actual = (int)$producto_existente['Cantidad'];
        $nueva_cantidad_total = $cantidad_actual + $cantidad_nueva;

        // (Opcional) Actualizar también precio o categoría si cambiaron, aunque la lógica es solo sumar cantidad
        // Si el precio o categoría son diferentes, podrías decidir crear un nuevo registro o actualizar el existente.
        // Para este caso, asumimos que si Nombre, Lote y Fecha coinciden, actualizamos cantidad.
        // El precio y categoría del *nuevo* input se usarían para actualizar el registro existente.

        $sql_actualizar = "UPDATE producto SET Cantidad = ?, Precio = ?, Categoria = ? 
                           WHERE ID_Producto = ?";
        $stmt_actualizar = mysqli_prepare($conn, $sql_actualizar);

        if (!$stmt_actualizar) {
            error_log("Error al preparar la consulta de actualización en create.php: " . mysqli_error($conn));
            header('Location: producto.php?error=' . urlencode('Error del sistema al actualizar producto.'));
            exit;
        }

        mysqli_stmt_bind_param($stmt_actualizar, "idsi", $nueva_cantidad_total, $precio, $categoria, $id_producto_existente);

        if (mysqli_stmt_execute($stmt_actualizar)) {
            mysqli_stmt_close($stmt_actualizar);
            header('Location: read.php?mensaje=' . urlencode('Cantidad del producto (ID: ' . $id_producto_existente . ') actualizada exitosamente a ' . $nueva_cantidad_total . '!'));
            exit;
        } else {
            $error_actualizacion = mysqli_stmt_error($stmt_actualizar);
            mysqli_stmt_close($stmt_actualizar);
            error_log("Error al ejecutar la actualización en create.php: " . $error_actualizacion);
            header('Location: producto.php?error=' . urlencode('Error al actualizar la cantidad del producto.'));
            exit;
        }

    } else {
        // --- PRODUCTO NO EXISTE (con ese Nombre+Lote+Fecha) O LOTE/FECHA SON DIFERENTES: INSERTAR NUEVO ---
        // ID_Producto es AUTO_INCREMENT
        $sql_insertar = "INSERT INTO producto (Nombre_Producto, Cantidad, Precio, Categoria, Lote, Fecha_Vencimiento)
                         VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insertar = mysqli_prepare($conn, $sql_insertar);

        if (!$stmt_insertar) {
            error_log("Error al preparar la consulta de inserción en create.php: " . mysqli_error($conn));
            header('Location: producto.php?error=' . urlencode('Error del sistema al registrar producto.'));
            exit;
        }

        mysqli_stmt_bind_param($stmt_insertar, "sidsis",
            $nombre_producto,
            $cantidad_nueva, // Usar la cantidad del formulario
            $precio,
            $categoria,
            $lote,
            $fecha_vencimiento
        );

        if (mysqli_stmt_execute($stmt_insertar)) {
            $nuevo_id_producto = mysqli_insert_id($conn); // Obtener el ID del nuevo producto
            mysqli_stmt_close($stmt_insertar);
            header('Location: read.php?mensaje=' . urlencode('¡Nuevo producto (ID: ' . $nuevo_id_producto . ') registrado exitosamente!'));
            exit;
        } else {
            $error_insercion = mysqli_stmt_error($stmt_insertar);
            mysqli_stmt_close($stmt_insertar);
            error_log("Error al ejecutar la inserción en create.php: " . $error_insercion);
            // Verificar si el error es por ID_Producto duplicado (si no fuera auto_increment)
            // O algún otro constraint de la BD
            header('Location: producto.php?error=' . urlencode('Error al registrar el nuevo producto.'));
            exit;
        }
    }

} else {
    // Si no es POST, redirigir al formulario
    header('Location: producto.php');
    exit;
}

// Cerrar la conexión
if (isset($conn)) {
    mysqli_close($conn);
}
?>