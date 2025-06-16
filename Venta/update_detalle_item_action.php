<?php
session_start();
// Validar acceso de administrador
if (!isset($_SESSION["usuario_id_usuario"])) { // || (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] != 1) ) {
    $_SESSION['mensaje_detalle_error'] = "Acceso no autorizado.";
    header('Location: historial.php'); // O a una página de login/inicio
    exit;
}

require_once(__DIR__ . '/../BD/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_item'])) {
    // --- 1. Recopilar y validar datos del formulario ---
    $id_detalle = filter_input(INPUT_POST, 'id_detalle', FILTER_VALIDATE_INT);
    $id_venta = filter_input(INPUT_POST, 'id_venta', FILTER_VALIDATE_INT);
    // id_producto se refiere al producto de este detalle_venta, que no cambia en este script (solo su cantidad/precio)
    $id_producto = filter_input(INPUT_POST, 'id_producto_original', FILTER_VALIDATE_INT); // Viene del form como id_producto_original
    $cantidad_original = filter_input(INPUT_POST, 'cantidad_original', FILTER_VALIDATE_INT);
    // $precio_original = filter_input(INPUT_POST, 'precio_original', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // No se usa para el cálculo de diferencia de stock directamente, sino para el log.

    $nueva_cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    $nuevo_precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $motivo_modificacion = filter_input(INPUT_POST, 'motivo_modificacion', FILTER_SANITIZE_STRING);
    $accion_stock_devuelto = filter_input(INPUT_POST, 'accion_stock_devuelto', FILTER_SANITIZE_STRING);
    $notas = filter_input(INPUT_POST, 'notas', FILTER_SANITIZE_STRING);

    // Validaciones básicas
    if (!$id_detalle || !$id_venta || !$id_producto || $cantidad_original === false ||
        $nueva_cantidad === false || $nuevo_precio === false ||
        $nueva_cantidad < 0 || $nuevo_precio < 0 || // Permitir cantidad 0 para anular item
        empty($motivo_modificacion)) {

        $_SESSION['mensaje_detalle_error'] = "Datos inválidos para la actualización. Verifique todos los campos requeridos.";
        // Para depurar, podrías pasar los valores para verlos
        // $_SESSION['debug_post_data'] = $_POST;
        header('Location: detalle_venta_vista.php?id_venta=' . ($id_venta ?: 'error'));
        exit;
    }

    // Si el motivo es devolución, la acción de stock es relevante si la cantidad disminuye.
    if (($motivo_modificacion === 'devolucion_cliente_defectuoso' || $motivo_modificacion === 'devolucion_cliente_buen_estado') &&
        ($nueva_cantidad < $cantidad_original) && empty($accion_stock_devuelto)) {
        $_SESSION['mensaje_detalle_error'] = "Si hay una devolución que reduce la cantidad, debe seleccionar una acción para el stock.";
        header('Location: detalle_venta_vista.php?id_venta=' . $id_venta);
        exit;
    }


    $conn->begin_transaction();
    try {
        // --- 2. Obtener valores originales del item y total de la venta ---
        // (Tu consulta original para esto está bien)
        $sql_old_values = "SELECT dv.subtotal AS subtotal_item_original, v.total_venta AS total_venta_original
                           FROM detalle_venta dv
                           JOIN ventas v ON dv.id_venta = v.id_venta
                           WHERE dv.id_detalle = ?";
        $stmt_old = $conn->prepare($sql_old_values);
        if (!$stmt_old) throw new Exception("Error preparando consulta de valores originales: " . $conn->error);
        $stmt_old->bind_param("i", $id_detalle);
        $stmt_old->execute();
        $res_old = $stmt_old->get_result();
        $old_values = $res_old->fetch_assoc();
        $stmt_old->close();

        if (!$old_values) {
            throw new Exception("No se pudo obtener los valores originales del item (ID Detalle: $id_detalle).");
        }
        $subtotal_item_original = $old_values['subtotal_item_original'];
        $total_venta_original = $old_values['total_venta_original'];

        // --- 3. Calcular diferencia de cantidad y ajustar stock ---
        $diferencia_cantidad_vendida = $nueva_cantidad - $cantidad_original; // Positivo si se vende más, negativo si se devuelve/reduce

        if ($diferencia_cantidad_vendida != 0) { // Solo ajustar stock si la cantidad cambió
            if ($diferencia_cantidad_vendida > 0) { // Aumenta la cantidad vendida (se toma más del stock)
                $sql_check_stock = "SELECT Cantidad FROM producto WHERE ID_Producto = ?";
                $stmt_check = $conn->prepare($sql_check_stock);
                if (!$stmt_check) throw new Exception("Error preparando consulta de stock: " . $conn->error);
                $stmt_check->bind_param("i", $id_producto);
                $stmt_check->execute();
                $current_stock_row = $stmt_check->get_result()->fetch_assoc();
                $stmt_check->close();

                if (!$current_stock_row || $current_stock_row['Cantidad'] < $diferencia_cantidad_vendida) {
                    throw new Exception("Stock insuficiente para aumentar la cantidad. Stock disponible para añadir: " . ($current_stock_row['Cantidad'] ?? 0));
                }
                // Restar del stock vendible
                $sql_update_stock = "UPDATE producto SET Cantidad = Cantidad - ? WHERE ID_Producto = ?";
                $stmt_stock = $conn->prepare($sql_update_stock);
                if (!$stmt_stock) throw new Exception("Error preparando actualización de stock: " . $conn->error);
                $stmt_stock->bind_param("ii", $diferencia_cantidad_vendida, $id_producto);
                $stmt_stock->execute();
                if ($stmt_stock->affected_rows === 0) {
                    // Podría ser que el producto ID no exista, aunque la verificación anterior debería haber fallado.
                    // O que la cantidad sea exactamente la misma que ya estaba (aunque diferencia_cantidad_vendida > 0 lo previene)
                    // Considerar si es un error crítico.
                }
                $stmt_stock->close();

            } else { // Disminuye la cantidad vendida (se devuelve al stock o se marca como defectuoso)
                $cantidad_devuelta = abs($diferencia_cantidad_vendida);

                if ($accion_stock_devuelto === 'reingresar_vendible' ||
                    ($motivo_modificacion === 'correccion_error_ingreso' && $nueva_cantidad < $cantidad_original)) { // Si es corrección y se reduce, se asume que vuelve a vendible
                    $sql_update_stock = "UPDATE producto SET Cantidad = Cantidad + ? WHERE ID_Producto = ?";
                    $stmt_stock = $conn->prepare($sql_update_stock);
                    if (!$stmt_stock) throw new Exception("Error preparando actualización de stock (reingreso): " . $conn->error);
                    $stmt_stock->bind_param("ii", $cantidad_devuelta, $id_producto);
                } elseif ($accion_stock_devuelto === 'marcar_defectuoso' && $motivo_modificacion === 'devolucion_cliente_defectuoso') {
                    // ASEGÚRATE DE TENER LA COLUMNA 'stock_defectuoso' EN TU TABLA 'producto'
                    $sql_update_stock = "UPDATE producto SET stock_defectuoso = stock_defectuoso + ? WHERE ID_Producto = ?";
                    $stmt_stock = $conn->prepare($sql_update_stock);
                    if (!$stmt_stock) throw new Exception("Error preparando actualización de stock (defectuoso): " . $conn->error . ". Asegúrate que la columna 'stock_defectuoso' existe.");
                    $stmt_stock->bind_param("ii", $cantidad_devuelta, $id_producto);
                } elseif ($accion_stock_devuelto === 'no_aplicable' || empty($accion_stock_devuelto)) {
                    // No hacer nada con el stock si es 'no_aplicable' o si no se especificó y no es una devolución clara.
                    // Podrías loguear esto como una advertencia si esperabas una acción.
                    $stmt_stock = null; // Para evitar error en ->execute() si no se preparó
                } else {
                     throw new Exception("Acción de stock devuelto desconocida o no aplicable para el motivo.");
                }

                if ($stmt_stock) { // Solo ejecutar si se preparó una consulta de stock
                    $stmt_stock->execute();
                    // Aquí podrías verificar affected_rows si es necesario.
                    $stmt_stock->close();
                }
            }
        }

        // --- 4. Actualizar 'detalle_venta' ---
        $nuevo_subtotal_item = $nueva_cantidad * $nuevo_precio;
        $sql_update_detalle = "UPDATE detalle_venta SET cantidad = ?, precio = ?, subtotal = ? WHERE id_detalle = ?";
        $stmt_detalle = $conn->prepare($sql_update_detalle);
        if (!$stmt_detalle) throw new Exception("Error preparando actualización de detalle_venta: " . $conn->error);
        $stmt_detalle->bind_param("iddi", $nueva_cantidad, $nuevo_precio, $nuevo_subtotal_item, $id_detalle);
        $stmt_detalle->execute();
        // No es un error si affected_rows es 0 si los datos no cambiaron, pero con los cambios de stock, algo debería cambiar.
        // Si la cantidad es 0, podrías considerar 'eliminar' lógicamente el detalle o marcarlo como 'anulado'.
        // Por ahora, se actualiza a cantidad 0.
        $stmt_detalle->close();

        // --- 5. Recalcular y actualizar 'total_venta' ---
        // Es más robusto recalcular el total sumando todos los subtotales actuales de detalle_venta para esa venta.
        // Esto evita errores si hay múltiples modificaciones o si el $total_venta_original no era fiable.
        $sql_recalculate_total = "SELECT SUM(subtotal) as nuevo_total FROM detalle_venta WHERE id_venta = ?";
        $stmt_recalc = $conn->prepare($sql_recalculate_total);
        if (!$stmt_recalc) throw new Exception("Error preparando recálculo de total: " . $conn->error);
        $stmt_recalc->bind_param("i", $id_venta);
        $stmt_recalc->execute();
        $result_recalc = $stmt_recalc->get_result();
        $row_recalc = $result_recalc->fetch_assoc();
        $stmt_recalc->close();
        $nuevo_total_venta_calculado = $row_recalc['nuevo_total'] ?? 0; // Si no hay items, el total es 0.

        $sql_update_total_venta = "UPDATE ventas SET total_venta = ? WHERE id_venta = ?";
        $stmt_total_v = $conn->prepare($sql_update_total_venta);
        if (!$stmt_total_v) throw new Exception("Error preparando actualización de total_venta: " . $conn->error);
        $stmt_total_v->bind_param("di", $nuevo_total_venta_calculado, $id_venta);
        $stmt_total_v->execute();
        $stmt_total_v->close();

        // --- 6. (Opcional pero Recomendado) Registrar Log de Modificación ---
        $id_usuario_modifico = $_SESSION['usuario_id_usuario'];
        $precio_original_item = filter_input(INPUT_POST, 'precio_original', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // Para el log

        /*
        // DESCOMENTA Y ADAPTA SI TIENES UNA TABLA DE LOGS
        // Asume una tabla: log_modificaciones_detalle_venta (id_log, id_detalle_modificado, id_venta, id_producto, 
        //                                                    cantidad_anterior, nueva_cantidad, precio_anterior, nuevo_precio,
        //                                                    motivo_modificacion, accion_stock, notas_modificacion, 
        //                                                    id_usuario_modifico, fecha_modificacion)
        $sql_log = "INSERT INTO log_modificaciones_detalle_venta 
                        (id_detalle_modificado, id_venta, id_producto, cantidad_anterior, nueva_cantidad, 
                         precio_anterior, nuevo_precio, motivo_modificacion, accion_stock, notas_modificacion, 
                         id_usuario_modifico, fecha_modificacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt_log = $conn->prepare($sql_log);
        if ($stmt_log) {
            $stmt_log->bind_param("iiiiiddsssi", 
                $id_detalle, $id_venta, $id_producto, $cantidad_original, $nueva_cantidad,
                $precio_original_item, $nuevo_precio, $motivo_modificacion, $accion_stock_devuelto, $notas,
                $id_usuario_modifico
            );
            $stmt_log->execute();
            $stmt_log->close();
        } else {
            // Loguear el error de preparación del log, pero no detener la transacción principal por esto si no es crítico.
            error_log("Error preparando statement de log: " . $conn->error);
        }
        */

        $conn->commit();
        $_SESSION['mensaje_detalle_exito'] = "Item de la venta actualizado correctamente. Motivo: " . htmlspecialchars($motivo_modificacion) . ".";

    } catch (Exception $e) {
        $conn->rollback();
        // Guardar mensaje de error más detallado para el admin (log) y uno más genérico para el usuario
        error_log("Error en update_detalle_item_action.php: " . $e->getMessage() . " | POST Data: " . json_encode($_POST));
        $_SESSION['mensaje_detalle_error'] = "Error al actualizar el item: " . htmlspecialchars($e->getMessage());
    } finally {
        // No cierres la conexión aquí si vas a redirigir inmediatamente.
        // PHP la cerrará al final del script. Si la cierras y luego rediriges, la sesión podría no guardarse correctamente.
        // if (isset($conn) && $conn->ping()) { $conn->close(); }
    }

    header('Location: detalle_venta_vista.php?id_venta=' . $id_venta);
    exit;

} else {
    $_SESSION['mensaje_detalle_error'] = "Acción no válida o método de solicitud incorrecto.";
    // Redirigir a una página segura, como el historial o la página de inicio.
    // Evita redirigir a detalle_venta_vista.php sin un id_venta válido.
    header('Location: historial.php');
    exit;
}
?>