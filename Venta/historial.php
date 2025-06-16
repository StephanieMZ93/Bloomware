<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id_usuario"])) {
    header("Location: ../index.php");
    exit();
}

require_once(__DIR__ . '/../BD/conexion.php');

if (!isset($conn) || !$conn || ($conn instanceof mysqli && $conn->connect_error)) {
    $db_error_message = ($conn instanceof mysqli && $conn->connect_error) ? $conn->connect_error : mysqli_connect_error();
    error_log("Error crítico de conexión en historial.php: " . $db_error_message); // Corregido para que diga historial.php
    die("Error crítico: No se pudo conectar. Contacte al administrador.");
}

// Consulta para obtener el historial de ventas con la cantidad total de artículos
$sql_query_ventas = "
    SELECT
        v.id_venta,
        v.fecha_venta,
        v.total_venta,
        (SELECT SUM(dv.cantidad) FROM detalle_venta dv WHERE dv.id_venta = v.id_venta) AS cantidad_total_articulos,
        u.nombre as nombre_usuario
    FROM
        ventas v
    LEFT JOIN
        usuarios u ON v.id_usuario = u.id_usuario
    ORDER BY
        v.id_venta DESC;
";

$stmt_historial_ventas = $conn->prepare($sql_query_ventas);

if (!$stmt_historial_ventas) {
    error_log("Error en la preparación de la consulta de historial de ventas: " . $conn->error);
    die("Error al preparar consulta de historial. Intente más tarde.");
}

$stmt_historial_ventas->execute();
$historial_ventas_resultado = $stmt_historial_ventas->get_result();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas - Bloomware</title>
    <link rel="stylesheet" href="../css/IPrincipal.css"> <!-- Estilos generales del proyecto -->
    <!-- OPCIÓN A: Si los estilos están en Venta/css/Estilos.css -->
    <link rel="stylesheet" href="css/Estilos.css">
    <!-- OPCIÓN B: Si creaste Venta/css/EstilosHistorial.css -->
    <!-- <link rel="stylesheet" href="css/EstilosHistorial.css"> -->
</head>
<body>
    <div class="page-container form-page-container"> <!-- Clase de IPrincipal.css -->
        <header class="form-header"> <!-- Clase de IPrincipal.css o Estilos.css del módulo de ventas -->
            <a href="../principal.php" class="regresar-link">← Inicio</a>
            <img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
        </header>

        <div class="message-container"> <!-- Clase de Estilos.css del módulo de ventas -->
            <?php
            // ... (código de mensajes de sesión sin cambios) ...
            if (isset($_SESSION['mensaje_venta'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensaje_venta']) . '</div>';
                unset($_SESSION['mensaje_venta']);
            }
            if (isset($_SESSION['error_venta'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_venta']) . '</div>';
                unset($_SESSION['error_venta']);
            }
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

        <!-- ESTAS SON LAS CLASES IMPORTANTES PARA LOS NUEVOS ESTILOS -->
        <div class="historial-container">
            <h2>Historial de Ventas</h2>
            <div class="table-wrapper">
                <table class="historial-table">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Total Venta</th>
                            <th>Artículos Vendidos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($historial_ventas_resultado && $historial_ventas_resultado->num_rows > 0): ?>
                            <?php while ($venta_actual = $historial_ventas_resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($venta_actual['id_venta']) ?></td>
                                    <td>
                                        <?= htmlspecialchars(date("d/m/Y", strtotime($venta_actual['fecha_venta']))) ?>
                                        <?php if (strpos($venta_actual['fecha_venta'], ':') !== false): ?>
                                            <small style="color: #606770;"><?= htmlspecialchars(date(" H:i", strtotime($venta_actual['fecha_venta']))) ?>hs</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?= htmlspecialchars(number_format($venta_actual['total_venta'], 2)) ?></td>
                                    <td><?= htmlspecialchars($venta_actual['cantidad_total_articulos'] ?? '0') ?></td>
                                    <td class="acciones-cell">
                                        <a href="detalle_venta_vista.php?id_venta=<?= htmlspecialchars($venta_actual['id_venta']) ?>">Ver Detalle</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-ventas-message">No hay ventas registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
        if (isset($stmt_historial_ventas)) {
            $stmt_historial_ventas->close();
        }
        if (isset($conn) && method_exists($conn, 'close')) {
             $conn->close();
        }
    ?>
</body>
</html>