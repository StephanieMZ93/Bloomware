<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id_usuario"])) {
    header("Location: ../index.php"); // Redirigir si no hay sesión
    exit();
}

// Verificar si el carrito existe y no está vacío, y si se presionó el botón de finalizar
if (!isset($_POST['finalizar_compra']) || !isset($_SESSION['carrito_venta']) || empty($_SESSION['carrito_venta'])) {
    $_SESSION['error_venta'] = "No hay productos en el carrito o la acción no es válida.";
    header("Location: venta.php"); // Volver a la página de ventas
    exit();
}

require_once(__DIR__ . '/../BD/conexion.php');

if (!isset($conn) || !$conn || ($conn instanceof mysqli && $conn->connect_error)) {
    $db_error_message = ($conn instanceof mysqli && $conn->connect_error) ? $conn->connect_error : mysqli_connect_error();
    error_log("Error crítico de conexión en procesar_venta.php: " . $db_error_message);
    // Guardar el error en sesión para mostrarlo en venta.php o historial.php
    $_SESSION['error_venta'] = "Error crítico de conexión al procesar la venta.";
    header("Location: venta.php");
    exit();
}

$id_usuario_actual = $_SESSION["usuario_id_usuario"];
$carrito_items = $_SESSION['carrito_venta'];
// $nombre_cliente_temporal = isset($_POST['nombre_cliente_temporal']) ? trim($_POST['nombre_cliente_temporal']) : null; // Opcional

// Calcular el total general de la venta desde el carrito (para asegurar consistencia)
$total_general_venta = 0;
foreach ($carrito_items as $item) {
    $total_general_venta += $item['cantidad'] * $item['precio_unitario'];
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Insertar en la tabla 'ventas'
    // La columna fecha_venta se llenará con la fecha y hora actual del servidor MySQL
    $sql_insert_venta = "INSERT INTO ventas (id_usuario, fecha_venta, total_venta) VALUES (?, NOW(), ?)";
    $stmt_venta = $conn->prepare($sql_insert_venta);
    if (!$stmt_venta) {
        throw new Exception("Error preparando la inserción de venta: " . $conn->error);
    }
    $stmt_venta->bind_param("id", $id_usuario_actual, $total_general_venta);
    $stmt_venta->execute();

    if ($stmt_venta->affected_rows <= 0) {
        throw new Exception("No se pudo registrar la venta principal.");
    }
    $id_nueva_venta = $conn->insert_id; // Obtener el ID de la venta recién creada
    $stmt_venta->close();

    // 2. Insertar en 'detalle_venta' y actualizar stock en 'producto'
    $sql_insert_detalle = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio, subtotal) VALUES (?, ?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_insert_detalle);
    if (!$stmt_detalle) {
        throw new Exception("Error preparando la inserción de detalle de venta: " . $conn->error);
    }

    $sql_update_stock = "UPDATE producto SET Cantidad = Cantidad - ? WHERE ID_Producto = ? AND Cantidad >= ?";
    $stmt_stock = $conn->prepare($sql_update_stock);
    if (!$stmt_stock) {
        throw new Exception("Error preparando la actualización de stock: " . $conn->error);
    }

    foreach ($carrito_items as $item_key => $item_carrito) {
        $id_producto_carrito = $item_carrito['id_producto'];
        $cantidad_vendida = $item_carrito['cantidad'];
        $precio_venta_item = $item_carrito['precio_unitario']; // Precio al momento de agregar al carrito
        $subtotal_item = $cantidad_vendida * $precio_venta_item;

        // Insertar detalle
        $stmt_detalle->bind_param("iiidd", $id_nueva_venta, $id_producto_carrito, $cantidad_vendida, $precio_venta_item, $subtotal_item);
        $stmt_detalle->execute();
        if ($stmt_detalle->affected_rows <= 0) {
            throw new Exception("No se pudo registrar el detalle para el producto ID: " . $id_producto_carrito);
        }

        // Actualizar stock
        // El AND Cantidad >= ? es una doble verificación para evitar stock negativo si algo falló antes
        $stmt_stock->bind_param("iii", $cantidad_vendida, $id_producto_carrito, $cantidad_vendida);
        $stmt_stock->execute();
        if ($stmt_stock->affected_rows <= 0) {
            // Esto puede pasar si el stock cambió entre que se agregó al carrito y se finalizó la compra,
            // o si la cantidad a descontar es mayor que el stock actual.
            throw new Exception("Stock insuficiente o error al actualizar stock para el producto ID: " . $id_producto_carrito . ". La venta no se completará.");
        }
    }

    $stmt_detalle->close();
    $stmt_stock->close();

    // Si todo fue bien, confirmar transacción
    $conn->commit();

    // Limpiar carrito de la sesión
    unset($_SESSION['carrito_venta']);

    $_SESSION['mensaje_venta'] = "¡Venta registrada exitosamente! ID Venta: " . $id_nueva_venta;
    header("Location: historial.php"); // Redirigir al historial
    exit();

} catch (Exception $e) {
    $conn->rollback(); // Revertir cambios si algo falló
    error_log("Error al procesar venta: " . $e->getMessage());
    $_SESSION['error_venta'] = "Error al procesar la venta: " . $e->getMessage();
    // Podrías redirigir a venta.php para que el usuario intente de nuevo o vea el error
    header("Location: venta.php");
    exit();
} finally {
    if (isset($conn) && method_exists($conn, 'close')) {
        $conn->close();
    }
}
?>