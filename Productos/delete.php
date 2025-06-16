<?php
// Archivo: Productos/delete.php

session_start(); // Es buena práctica iniciar sesión también en scripts de acción por si necesitas verificar permisos en el futuro.

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id_usuario"])) {
    // Si no hay sesión, no debería poder eliminar. Podrías redirigir a index.php
    // o simplemente mostrar un error si se accede directamente.
    // Por simplicidad, si no hay sesión, podríamos redirigir a la tabla con un error.
    header("Location: read.php?error=" . urlencode("Acceso no autorizado. Debe iniciar sesión."));
    exit();
}

// Incluir conexión (un nivel arriba, luego carpeta BD)
require_once(__DIR__ . '/../BD/Conexion.php');

// Verificar si la conexión se estableció correctamente
if (!$conn || $conn->connect_error) {
    $db_error_message = isset($conn) && $conn->connect_error ? $conn->connect_error : mysqli_connect_error();
    error_log("Error crítico de conexión en delete.php: " . $db_error_message);
    // Redirigir a la tabla con un mensaje de error genérico para el usuario
    header("Location: read.php?error=" . urlencode("Error de conexión. No se pudo procesar la eliminación."));
    exit; // Detener si no hay conexión
}

// Verificar si se recibió el parámetro 'delete_ID_Producto' y no está vacío
if (isset($_GET['delete_ID_Producto']) && !empty($_GET['delete_ID_Producto'])) {

    $delete_id = $_GET['delete_ID_Producto'];

    // Validar que el ID sea un entero positivo
    if (filter_var($delete_id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) === false) {
        // Redirigir a la tabla de productos existentes con un mensaje de error
        header('Location: read.php?error=' . urlencode('ID de producto inválido para eliminar.'));
        exit;
    }

    // --- Consulta SQL PREPARADA para eliminar ---
    // Asegúrate que 'ID_Producto' sea el nombre correcto de tu columna ID en la tabla 'producto'
    $sql = "DELETE FROM producto WHERE ID_Producto = ?";

    // 1. Preparar la consulta
    $stmt = mysqli_prepare($conn, $sql);

    // Verificar si la preparación fue exitosa
    if ($stmt) {
        // 2. Vincular el parámetro ID (tipo 'i' para integer)
        mysqli_stmt_bind_param($stmt, "i", $delete_id);

        // 3. Ejecutar la consulta preparada
        if (mysqli_stmt_execute($stmt)) {
            // Verificar si se eliminó alguna fila (opcional pero bueno para confirmar)
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                // Éxito: Redirigir a la PÁGINA DE LA TABLA (read.php) con mensaje de éxito
                header("Location: read.php?mensaje=" . urlencode("¡Producto (ID: " . $delete_id . ") eliminado exitosamente!"));
            } else {
                // No se eliminó ninguna fila, el ID probablemente no existía
                header("Location: read.php?error=" . urlencode("No se encontró el producto con ID: " . $delete_id . " para eliminar."));
            }
            exit; // Salir después de la redirección
        } else {
            // Error en la ejecución: Redirigir a la PÁGINA DE LA TABLA con mensaje de error
            error_log("Error al ejecutar delete para ID {$delete_id}: " . mysqli_stmt_error($stmt));
            header("Location: read.php?error=" . urlencode("Error al eliminar el producto. Intente de nuevo."));
            exit;
        }

        // 4. Cerrar la sentencia preparada
        mysqli_stmt_close($stmt);

    } else {
        // Error en la preparación de la consulta: Redirigir a la PÁGINA DE LA TABLA con mensaje de error
        error_log("Error al preparar delete: " . mysqli_error($conn));
        header("Location: read.php?error=" . urlencode("Error al preparar la consulta de eliminación."));
        exit;
    }

} else {
    // Si no se proporcionó un ID válido o el parámetro está ausente, redirigir a la PÁGINA DE LA TABLA con error
    header("Location: read.php?error=" . urlencode("No se especificó un ID de producto para eliminar o solicitud incorrecta."));
    exit;
}

// Cerrar la conexión
if (isset($conn)) {
    mysqli_close($conn);
}
?>