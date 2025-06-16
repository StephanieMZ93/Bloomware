<?php
session_start();
// Asumimos que cualquier usuario logueado puede ver detalles.
// Para acciones de update/delete, el control de rol se hará en esos scripts.
if (!isset($_SESSION["usuario_id_usuario"])) {
    header('Location: ../index.php'); // Ajusta la ruta si es necesario
    exit;
}

require_once(__DIR__ . '/../BD/conexion.php'); // Ruta ajustada

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (!isset($_GET['id_venta'])) {
    $_SESSION['error_detalle_venta'] = "ID de venta no proporcionado.";
    header('Location: historial.php'); // O a donde quieras redirigir
    exit;
}

$id_venta = intval($_GET['id_venta']);

// Obtener detalles de la venta
$sqlDetalle = "SELECT dv.id_detalle, p.Nombre_Producto, dv.cantidad, dv.precio, dv.subtotal
               FROM detalle_venta dv
               INNER JOIN producto p ON dv.id_producto = p.ID_Producto
               WHERE dv.id_venta = ?";
$stmtDetalle = $conn->prepare($sqlDetalle);

if ($stmtDetalle === false) {
    die("Error al preparar la consulta de detalle de venta: " . $conn->error);
}

$stmtDetalle->bind_param("i", $id_venta);
$stmtDetalle->execute();
$resultDetalle = $stmtDetalle->get_result();

// Obtener el nombre del vendedor
$sqlVendedor = "SELECT u.nombre
                FROM ventas v
                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.id_venta = ?";
$stmtVendedor = $conn->prepare($sqlVendedor);

if ($stmtVendedor === false) {
    die("Error al preparar la consulta del vendedor: " . $conn->error);
}

$stmtVendedor->bind_param("i", $id_venta);
$stmtVendedor->execute();
$resultVendedor = $stmtVendedor->get_result();
$rowVendedor = $resultVendedor->fetch_assoc();
$vendedor = $rowVendedor['nombre'] ?? 'No disponible';
$stmtVendedor->close();


// Obtener el total de la venta directamente de la tabla 'ventas'
$sqlTotalVenta = "SELECT total_venta, fecha_venta FROM ventas WHERE id_venta = ?";
$stmtTotalVenta = $conn->prepare($sqlTotalVenta);
$totalVenta = 0;
$fechaVenta = 'No disponible';

if ($stmtTotalVenta) {
    $stmtTotalVenta->bind_param("i", $id_venta);
    $stmtTotalVenta->execute();
    $resultTotalVenta = $stmtTotalVenta->get_result();
    if ($rowTV = $resultTotalVenta->fetch_assoc()) {
        $totalVenta = $rowTV['total_venta'];
        $fechaVenta = date("d/m/Y H:i", strtotime($rowTV['fecha_venta']));
    }
    $stmtTotalVenta->close();
} else {
    die("Error al preparar la consulta del total de la venta: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta #<?= htmlspecialchars($id_venta) ?> - Bloomware</title>
    <!-- Estilos de Bloomware -->
    <link rel="stylesheet" href="../css/IPrincipal.css"> <!-- Estilo general del proyecto -->
    <link rel="stylesheet" href="css/Estilos.css"> <!-- Estilos del módulo de ventas -->
    <link rel="stylesheet" href="css/Estilos_detalle.css"> <!-- CSS específico para esta página -->

    <!-- Estilos específicos para impresión -->
    <style>
        @media print {
            /* Ocultar elementos que no queremos en la impresión */
            .no-imprimir {
                display: none !important;
            }

            /* Estilos generales para la impresión */
            body {
                font-family: Arial, sans-serif; /* Fuente genérica para impresión */
                font-size: 11pt; /* Tamaño de fuente base para impresión */
                color: #000000; /* Asegurar texto negro */
                background-color: #ffffff; /* Fondo blanco */
                margin: 0;
                padding: 0;
            }

            /* Ajustes del contenedor principal para impresión */
            .page-container {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 15mm; /* Margen de impresión */
                box-shadow: none;
                border: none;
            }

            .detalle-venta-container {
                box-shadow: none;
                border: 1px solid #ccc; /* Borde sutil para la factura en sí */
                padding: 10mm;
                margin-top: 0;
            }

            /* Logo para impresión */
            .logo-impresion {
                display: block !important; /* Se mostrará solo en impresión */
                max-height: 70px; /* Ajusta según tu logo */
                width: auto;
                margin: 0 auto 15px auto; /* Centrar logo */
            }
            /* Ocultar el logo normal de la cabecera en pantalla (si ya se oculta el header no es necesario) */
            /* header.form-header .logo--img { display: none; } */


            /* Título y datos de la venta */
            .detalle-venta-container h2 {
                font-size: 18pt;
                text-align: center;
                border-bottom: 1px solid #999999;
                padding-bottom: 5mm;
                margin-bottom: 7mm;
                color: #000000;
            }

            .info-venta {
                font-size: 10pt;
                margin-bottom: 7mm;
                padding-bottom: 5mm;
                border-bottom: 1px dashed #aaaaaa;
                display: flex;
                justify-content: space-between;
            }
             .info-venta p {
                margin: 2px 0;
            }

            /* Tabla de productos */
            .detalle-venta-container table {
                width: 100%;
                border-collapse: collapse; /* Para que los bordes se unan */
                font-size: 9.5pt;
                margin-top: 5mm;
            }

            .detalle-venta-container table th,
            .detalle-venta-container table td {
                padding: 3mm 2mm;
                border: 1px solid #bbbbbb; /* Bordes para celdas de la tabla */
                text-align: left;
            }
            .detalle-venta-container table th {
                background-color: #eeeeee; /* Fondo claro para encabezados de tabla */
                font-weight: bold;
            }
            /* Alineación específica para columnas numéricas */
            .detalle-venta-container table td:nth-child(2), /* Cantidad */
            .detalle-venta-container table td:nth-child(3), /* Precio Unitario */
            .detalle-venta-container table td:nth-child(4) { /* Subtotal */
                text-align: right;
            }
             .detalle-venta-container table th:nth-child(2),
            .detalle-venta-container table th:nth-child(3),
            .detalle-venta-container table th:nth-child(4) {
                text-align: right;
            }


            /* Total de la venta */
            .total-venta {
                font-size: 12pt;
                text-align: right;
                font-weight: bold;
                margin-top: 7mm;
                padding-top: 3mm;
                border-top: 2px solid #000000;
            }
        }
    </style>
</head>
<body>
    <!-- CABECERA PRINCIPAL - Se ocultará al imprimir -->
    <header class="form-header no-imprimir">
        <a href="historial.php" class="regresar-link">← Volver atras</a>
        <img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
    </header>

    <!-- CONTENEDOR DE MENSAJES - Se ocultará al imprimir -->
    <div class="message-container no-imprimir">
        <?php
        if (isset($_SESSION['mensaje_detalle_exito'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensaje_detalle_exito']) . '</div>';
            unset($_SESSION['mensaje_detalle_exito']);
        }
        if (isset($_SESSION['mensaje_detalle_error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['mensaje_detalle_error']) . '</div>';
            unset($_SESSION['mensaje_detalle_error']);
        }
        ?>
    </div>

    <!-- CONTENEDOR DE PÁGINA -->
    <div class="page-container form-page-container"> <!-- Mantener clases para vista normal -->
        <div class="detalle-venta-container card-layout"> <!-- Mantener clases para vista normal -->

            <!-- Logo específico para impresión (oculto en pantalla por defecto) -->
            <img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo-impresion" style="display:none;" />

            <h2>Detalle de Venta: #<?= htmlspecialchars($id_venta) ?></h2>
            <div class="info-venta">
                <p><strong>Fecha:</strong> <?= htmlspecialchars($fechaVenta) ?></p>
                <p><strong>Vendedor:</strong> <?= htmlspecialchars($vendedor) ?></p>
            </div>

            <?php if ($resultDetalle->num_rows > 0): ?>
            <div class="table-wrapper"> <!-- Útil para scroll horizontal en pantalla si la tabla es ancha -->
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                            <th class="no-imprimir">Acciones</th> <!-- Columna de Acciones se oculta al imprimir -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($detalle = $resultDetalle->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($detalle['Nombre_Producto']) ?></td>
                                <td><?= htmlspecialchars($detalle['cantidad']) ?></td>
                                <td>$<?= number_format($detalle['precio'], 2) ?></td>
                                <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                                <td class="acciones-detalle no-imprimir"> <!-- Celda de Acciones se oculta al imprimir -->
                                    <!-- Formulario para Actualizar -->
                                    <form action="update_detalle_item_form.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="id_detalle" value="<?= $detalle['id_detalle'] ?>">
                                        <button type="submit" class="btn-accion btn-update" title="Editar este item">✎</button>
                                    </form>
                                    <!-- Formulario para Eliminar -->
                                    <form action="delete_detalle_item.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de que desea eliminar este producto de la venta? Esta acción no se puede deshacer y afectará el stock.');">
                                        <input type="hidden" name="id_detalle" value="<?= $detalle['id_detalle'] ?>">
                                        <input type="hidden" name="id_venta" value="<?= $id_venta ?>"> <!-- Para redirigir -->
                                        <button type="submit" class="btn-accion btn-delete" title="Eliminar este item">🗑</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <h3 class="total-venta">Total Venta: $<?= number_format($totalVenta, 2) ?></h3>
            <?php else: ?>
                <p class="text-center">No se encontraron detalles para esta venta o la venta está vacía.</p>
            <?php endif; ?>

            <!-- ACCIONES AL PIE - Se ocultarán al imprimir -->
            <div class="acciones-footer no-imprimir">
                <button class="btn-accion btn-imprimir" onclick="window.print()">Imprimir</button>
                <a href="historial.php" class="btn-accion btn-regresar">Regresar al Historial</a>
            </div>
        </div>
    </div>
    <?php
        $stmtDetalle->close();
        if (isset($conn) && method_exists($conn, 'close')) { // Cerrar conexión si existe
            $conn->close();
        }
    ?>
</body>
</html>