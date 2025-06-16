<?php
session_start();
if (!isset($_SESSION["usuario_id_usuario"])) { // || $_SESSION['rol_id'] != 1) {
    $_SESSION['mensaje_detalle_error'] = "Acceso no autorizado.";
    header('Location: historial.php');
    exit;
}

require_once(__DIR__ . '/../BD/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_detalle'])) {
    $id_detalle = filter_input(INPUT_POST, 'id_detalle', FILTER_VALIDATE_INT);
    $id_venta = filter_input(INPUT_POST, 'id_venta', FILTER_VALIDATE_INT); // Para redirigir y actualizar total

    if (!$id_detalle || !$id_venta) {
        $_SESSION['mensaje_detalle_error'] = "Datos incompletos para eliminar el item.";
        header('Location: ' . ( $id_venta ? 'detalle_venta_vista.php?id_venta='.$id_venta : 'historial.php' ) );
        exit;
    }

    $conn->begin_transaction();
    try {
        // 1. Obtener datos del item a eliminar (cantidad, id_producto, subtotal)
        $sql_item_info = "SELECT id_producto, cantidad, subtotal FROM detalle_venta WHERE id_detalle = ?";
        $stmt_info = $conn->prepare($sql_item_info);
        $stmt_info->bind_param("i", $id_detalle);
        $stmt_info->execute();
        $result_info = $stmt_info->get_result();
        $item_info = $result_info->fetch_assoc();
        $stmt_info->close();

        if (!$item_info) {
            throw new Exception("Item del detalle no encontrado para eliminar.");
        }

        $id_producto_afectado = $item_info['id_producto'];
        $cantidad_a_devolver = $item_info['cantidad'];
        $subtotal_a_restar = $item_info['subtotal'];

        // 2. Eliminar el item de 'detalle_venta'
        $sql_delete = "DELETE FROM detalle_venta WHERE id_detalle = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id_detalle);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows === 0) {
            throw new Exception("No se pudo eliminar el item del detalle.");
        }
        $stmt_delete->close();

        // 3. Devolver la cantidad al stock del producto en 'producto'
        $sql_update_stock = "UPDATE producto SET Cantidad = Cantidad + ? WHERE ID_Producto = ?";
        $stmt_stock = $conn->prepare($sql_update_stock);
        $stmt_stock->bind_param("ii", $cantidad_a_devolver, $id_producto_afectado);
        $stmt_stock->execute();
        // No es crítico si affected_rows es 0 aquí, el producto podría haber sido eliminado, aunque no debería.
        $stmt_stock->close();

        // 4. Actualizar 'total_venta' en la tabla 'ventas'
        $sql_update_total_venta = "UPDATE ventas SET total_venta = total_venta - ? WHERE id_venta = ?";
        $stmt_total_v = $conn->prepare($sql_update_total_venta);
        $stmt_total_v->bind_param("di", $subtotal_a_restar, $id_venta);
        $stmt_total_v->execute();
        $stmt_total_v->close();


        $conn->commit();
        $_SESSION['mensaje_detalle_exito'] = "Item eliminado de la venta correctamente. Stock y total actualizados.";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['mensaje_detalle_error'] = "Error al eliminar el item: " . $e->getMessage();
    } finally {
        if (isset($conn)) { $conn->close(); }
    }
    header('Location: detalle_venta_vista.php?id_venta=' . $id_venta);
    exit;
} else {
    $_SESSION['mensaje_detalle_error'] = "Acción no válida.";
    header('Location: historial.php');
    exit;
}
?>