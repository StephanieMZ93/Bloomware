<?php
session_start();
// Solo admins pueden acceder a actualizar
// Es buena práctica ser explícito con los roles. Si tienes roles, úsalos.
// Asumiendo rol_id 1 es admin. Si no usas roles, la comprobación actual está bien.
if (!isset($_SESSION["usuario_id_usuario"]) || (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] != 1) ) {
    $_SESSION['mensaje_detalle_error'] = "Acceso no autorizado.";
    header('Location: historial.php');
    exit;
}

require_once(__DIR__ . '/../BD/conexion.php'); // Buena práctica usar __DIR__

if (!isset($_GET['id_detalle']) || !filter_var($_GET['id_detalle'], FILTER_VALIDATE_INT)) { // Validar que es un entero
    $_SESSION['mensaje_detalle_error'] = "ID de detalle inválido o no proporcionado.";
    header('Location: historial.php');
    exit;
}

$id_detalle = intval($_GET['id_detalle']);

// Obtener datos actuales del item del detalle
// Se añade p.ID_Producto AS producto_id_original para claridad si se quisiera cambiar el producto (más complejo)
$sql = "SELECT dv.id_detalle, dv.id_venta, dv.id_producto, dv.cantidad, dv.precio, 
               p.Nombre_Producto, p.Cantidad as stock_actual_producto, p.ID_Producto AS producto_id_original
        FROM detalle_venta dv
        INNER JOIN producto p ON dv.id_producto = p.ID_Producto
        WHERE dv.id_detalle = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    // En un entorno de producción, loguear el error en lugar de hacer die() directamente.
    error_log("Error preparando consulta (detalle_venta): " . $conn->error);
    $_SESSION['mensaje_detalle_error'] = "Error al preparar la consulta. Intente más tarde.";
    header('Location: historial.php');
    exit;
}
$stmt->bind_param("i", $id_detalle);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    $_SESSION['mensaje_detalle_error'] = "Item del detalle no encontrado.";
    header('Location: historial.php');
    exit;
}

// Calcular el stock que tenía el producto ANTES de esta venta específica
// Esto es: stock_actual_fisico_en_tienda + cantidad_de_este_item_en_la_venta_original
// Lo que tienes es "stock_actual_producto" (que ya refleja la venta) + item['cantidad'] (para revertir esa línea)
// Lo cual es correcto para mostrar "Stock disponible si se revierte este item"
$stock_disponible_al_revertir_item = $item['stock_actual_producto'] + $item['cantidad'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Item de Venta - Bloomware</title>
    <link rel="stylesheet" href="css/Estilos_detalle.css">
    <link rel="stylesheet" href="css/Estilos.css">
    <style>
        /* Estilos adicionales para mejor legibilidad */
        .form-group small { display: block; margin-top: 5px; font-size: 0.9em; color: #555; }
        .warning { color: orange; font-weight: bold; }
        .info { color: #31708f; }
    </style>
</head>
<body>
    <div class="page-container form-page-container">
        <header class="form-header">
            <a href="detalle_venta_vista.php?id_venta=<?= htmlspecialchars($item['id_venta']) ?>" class="regresar-link">← Volver al Detalle de Venta</a>
            <img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
        </header>

        <div class="form-update-container card-layout">
            <h2>Actualizar Item: <?= htmlspecialchars($item['Nombre_Producto']) ?></h2>
            
            <?php if (isset($_SESSION['mensaje_accion_detalle'])): ?>
                <p class="mensaje-exito"><?= $_SESSION['mensaje_accion_detalle']; unset($_SESSION['mensaje_accion_detalle']); ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_accion_detalle'])): ?>
                <p class="mensaje-error"><?= $_SESSION['error_accion_detalle']; unset($_SESSION['error_accion_detalle']); ?></p>
            <?php endif; ?>

            <form action="update_detalle_item_action.php" method="POST" class="formulario">
                <input type="hidden" name="id_detalle" value="<?= htmlspecialchars($item['id_detalle']) ?>">
                <input type="hidden" name="id_venta" value="<?= htmlspecialchars($item['id_venta']) ?>">
                <input type="hidden" name="id_producto_original" value="<?= htmlspecialchars($item['producto_id_original']) ?>">
                <input type="hidden" name="cantidad_original" value="<?= htmlspecialchars($item['cantidad']) ?>">
                <input type="hidden" name="precio_original" value="<?= htmlspecialchars($item['precio']) ?>">

                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" value="<?= htmlspecialchars($item['cantidad']) ?>" min="0" required>
                    <!-- 
                        Si min="0", permites anular el item (cantidad 0). 
                        Si min="1", el item no se puede anular, solo reducir a 1.
                        Para devoluciones, min="0" tiene sentido.
                    -->
                    <small class="info">Stock actual del producto (general): <?= htmlspecialchars($item['stock_actual_producto']) ?></small>
                    <small class="info">Este item originalmente tomó: <?= htmlspecialchars($item['cantidad']) ?> unidades.</small>
                    <small>Si reduce la cantidad, la diferencia se evaluará para stock. Si aumenta, se tomará del stock.</small>
                </div>

                <div class="form-group">
                    <label for="precio">Precio Unitario:</label>
                    <input type="number" id="precio" name="precio" value="<?= htmlspecialchars($item['precio']) ?>" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="motivo_modificacion">Motivo de la Modificación:</label>
                    <select name="motivo_modificacion" id="motivo_modificacion" required>
                        <option value="">Seleccione un motivo...</option>
                        <option value="correccion_error_ingreso">Corrección (Error de Ingreso)</option>
                        <option value="devolucion_cliente_defectuoso">Devolución Cliente (Producto Defectuoso)</option>
                        <option value="devolucion_cliente_buen_estado">Devolución Cliente (Producto Buen Estado)</option>
                        <option value="cambio_producto">Cambio por otro producto (requiere más pasos)</option>
                        <option value="ajuste_precio">Ajuste de Precio</option>
                        <option value="otro">Otro (especificar en notas)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="accion_stock_devuelto">Acción para el stock (si la cantidad disminuye y es una devolución):</label>
                    <select name="accion_stock_devuelto" id="accion_stock_devuelto">
                        <option value="reingresar_vendible" selected>Reingresar a Stock Vendible</option>
                        <option value="marcar_defectuoso">Marcar como Defectuoso (No reingresar a vendible)</option>
                        <option value="no_aplicable">No Aplicable (ej. corrección, aumento cantidad)</option>
                    </select>
                     <small class="warning">¡Importante! Seleccionar "Marcar como Defectuoso" si el producto está dañado y no debe volver a venderse.</small>
                </div>

                <div class="form-group">
                    <label for="notas">Notas Adicionales:</label>
                    <textarea name="notas" id="notas" rows="3" placeholder="Detalles adicionales sobre la modificación..."></textarea>
                </div>

                <button type="submit" name="actualizar_item" class="btn-accion btn-submit-update">Guardar Cambios</p>
            </form>
        </div>
    </div>
    <?php // $conn->close(); // Es buena práctica cerrarla, pero PHP suele hacerlo al final del script si no es persistente. ?>
</body>
</html>