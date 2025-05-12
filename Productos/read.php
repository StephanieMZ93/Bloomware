<?php
// Archivo: read.php
session_start();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php"); 
    exit();
}

// Es crucial que este archivo establezca la variable $conn
require_once(__DIR__ . '/../BD/Conexion.php');

// Verificar si la conexión se estableció correctamente desde Conexion.php
if (!isset($conn) || !$conn || $conn->connect_error) {
    $db_error_message = isset($conn) && $conn->connect_error ? $conn->connect_error : mysqli_connect_error();
    error_log("Error crítico de conexión en read.php (página principal tabla): " . $db_error_message);
    die("Error crítico: No se pudo establecer la conexión a la base de datos. Por favor, contacte al administrador.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Existentes - Bloomware</title>
    <link rel="stylesheet" href="css/Estilos.css"> <!-- CSS para el módulo Productos -->
</head>
<body>

    <div class="page-container table-page-container">

        <header class="table-header">
             <a href="../principal.php"><img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" /></a>
             <h1>Productos Existentes</h1>
             <div class="header-actions">
                 <a href="../principal.php" class="btn-accion btn-regresar">Regresar</a>
                 <a href="producto.php" class="btn-accion btn-nuevo">Registrar Nuevo</a> <!-- Enlace al formulario -->
             </div>
        </header>

        <div class="message-container"> <!-- Para mostrar mensajes de éxito/error -->
            <?php if (isset($_GET['mensaje'])) : ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['mensaje']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Lote</th>
                        <th>Fecha Vencimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // --- LÓGICA PARA LEER Y MOSTRAR PRODUCTOS (antes en read.php separado) ---

                    $consulta_bloomware = mysqli_query($conn, "SELECT * FROM producto ORDER BY Fecha_Vencimiento ASC");

                    if (!$consulta_bloomware) {
                        echo "<tr><td colspan='8' style='color: red;'>Error en la consulta SQL: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</td></tr>";
                    } elseif (mysqli_num_rows($consulta_bloomware) > 0) {
                        $dias_umbral_alerta = 30;
                        $fecha_actual_dt = null;
                        try {
                             $fecha_actual_dt = new DateTime();
                             $fecha_actual_dt->setTime(0,0,0);
                        } catch (Exception $e) {
                             echo "<tr><td colspan='8' style='color: orange;'>Advertencia: No se pudo obtener la fecha actual.</td></tr>";
                             error_log("Error al crear DateTime para fecha actual en read.php: " . $e->getMessage());
                        }

                        while ($row = mysqli_fetch_assoc($consulta_bloomware)) {
                            $id_producto_esc = htmlspecialchars($row['ID_Producto'], ENT_QUOTES, 'UTF-8');
                            $nombre_producto_esc = htmlspecialchars($row['Nombre_Producto'], ENT_QUOTES, 'UTF-8');
                            $cantidad_esc = htmlspecialchars($row['Cantidad'], ENT_QUOTES, 'UTF-8');
                            $precio_esc = htmlspecialchars($row['Precio'], ENT_QUOTES, 'UTF-8');
                            $categoria_esc = htmlspecialchars($row['Categoria'], ENT_QUOTES, 'UTF-8');
                            $lote_esc = htmlspecialchars($row['Lote'] ?? '', ENT_QUOTES, 'UTF-8');
                            $fecha_vencimiento = $row['Fecha_Vencimiento'] ?? null;
                            $fecha_vencimiento_esc = $fecha_vencimiento ? htmlspecialchars($fecha_vencimiento, ENT_QUOTES, 'UTF-8') : 'N/A';

                            $row_class = '';
                            $tooltip_text = '';

                            if ($fecha_actual_dt && $fecha_vencimiento && $fecha_vencimiento !== '0000-00-00') {
                                try {
                                    $fecha_vencimiento_dt = new DateTime($fecha_vencimiento);
                                    $fecha_vencimiento_dt->setTime(0,0,0);

                                    if ($fecha_vencimiento_dt < $fecha_actual_dt) {
                                        $row_class = 'expired';
                                        $tooltip_text = 'Producto Vencido el ' . $fecha_vencimiento_dt->format('d/m/Y');
                                    } else {
                                        $intervalo = $fecha_actual_dt->diff($fecha_vencimiento_dt);
                                        $dias_restantes = (int)$intervalo->format('%r%a');

                                        if ($dias_restantes <= $dias_umbral_alerta) {
                                            $row_class = 'expiring-soon';
                                            if ($dias_restantes == 0) { $tooltip_text = '¡Vence Hoy!'; }
                                            elseif ($dias_restantes == 1) { $tooltip_text = 'Vence Mañana'; }
                                            else { $tooltip_text = 'Vence en ' . $dias_restantes . ' días (' . $fecha_vencimiento_dt->format('d/m/Y') . ')'; }
                                        } else {
                                            $tooltip_text = 'Vence el ' . $fecha_vencimiento_dt->format('d/m/Y');
                                        }
                                    }
                                } catch (Exception $e) {
                                    $row_class = 'invalid-date';
                                    $tooltip_text = 'Fecha de vencimiento inválida (' . $fecha_vencimiento_esc . ')';
                                    error_log("Error parseando fecha en read.php (producto ID: {$id_producto_esc}): " . $fecha_vencimiento . " - " . $e->getMessage());
                                }
                            } elseif ($fecha_vencimiento === '0000-00-00') {
                                 $tooltip_text = 'Fecha de vencimiento inválida (0000-00-00)'; $row_class = 'invalid-date';
                            } elseif (!$fecha_vencimiento) {
                                 $tooltip_text = 'Sin fecha de vencimiento';
                            }

                            echo "<tr class='{$row_class}' title='{$tooltip_text}'>";
                            echo "<td data-label='ID Producto'>{$id_producto_esc}</td>";
                            echo "<td data-label='Nombre Producto'>{$nombre_producto_esc}</td>";
                            echo "<td data-label='Cantidad'>{$cantidad_esc}</td>";
                            echo "<td data-label='Precio'>{$precio_esc}</td>";
                            echo "<td data-label='Categoría'>{$categoria_esc}</td>";
                            echo "<td data-label='Lote'>{$lote_esc}</td>";
                            echo "<td data-label='Fecha Vencimiento'>{$fecha_vencimiento_esc}</td>";
                            echo "<td data-label='Acciones'>";
                                echo "<button onclick='eliminarProducto({$id_producto_esc})'>Eliminar</button>";
                                echo "<button onclick='mostrarFormularioActualizar({$id_producto_esc}, \"{$nombre_producto_esc}\", \"{$cantidad_esc}\", \"{$precio_esc}\", \"{$categoria_esc}\", \"{$lote_esc}\", \"{$fecha_vencimiento_esc}\")'>Actualizar</button>";
                            echo "</td>";
                            echo "</tr>";
                        } // Fin while
                    }
                    // SI la consulta fue exitosa PERO no hubo filas...
                    else {
                        echo "<tr><td colspan='8' style='text-align: center; padding: 20px;'>No se encuentran productos registrados.</td></tr>";
                    }
                    // --- FIN DE LA LÓGICA INTEGRADA ---
                    ?>
                </tbody>
            </table>
        </div> <!-- Fin .table-wrapper -->

    </div> <!-- Fin .table-page-container -->

    <?php
        // Cerrar la conexión establecida al principio de este script
        if (isset($conn)) {
            mysqli_close($conn);
        }
    ?>

    <!-- === JAVASCRIPT PARA BOTONES (Igual que en el anterior existentes.php) === -->
    <script>
        function eliminarProducto(productoId) {
            if (confirm(`¿Estás seguro de que deseas eliminar el producto con ID ${productoId}? Esta acción no se puede deshacer.`)) {
                // delete.php sigue siendo un archivo separado
                window.location.href = `delete.php?delete_ID_Producto=${productoId}`;
            } else {
                console.log("Eliminación cancelada por el usuario.");
            }
        }

        function mostrarFormularioActualizar(id, nombre, cantidad, precio, categoria, lote, fechaVencimiento) {
            console.log("Actualizando producto ID:", id);
            const params = new URLSearchParams({
                update_id: id, nombre: nombre, cantidad: cantidad, precio: precio,
                categoria: categoria, lote: lote,
                fecha: fechaVencimiento !== 'N/A' ? fechaVencimiento : ''
            });
            // producto.php sigue siendo el archivo del formulario
            window.location.href = `producto.php?${params.toString()}`;
        }
    </script>

</body>
</html>