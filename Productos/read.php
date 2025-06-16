<?php
// Archivo: Productos/read.php
// Página principal que muestra la tabla de productos CON FILTRO DESPLEGABLE POR CATEGORÍA.

session_start();
if (!isset($_SESSION["usuario_id_usuario"])) {
    header("Location: ../index.php");
    exit();
}

require_once(__DIR__ . '/../BD/Conexion.php');

if (!isset($conn) || !$conn || $conn->connect_error) {
    $db_error_message = isset($conn) && $conn->connect_error ? $conn->connect_error : mysqli_connect_error();
    error_log("Error crítico de conexión en read.php: " . $db_error_message);
    die("Error crítico: No se pudo conectar. Contacte al administrador.");
}

// --- OBTENER CATEGORÍAS ÚNICAS PARA EL DESPLEGABLE ---
$categorias_para_filtro = [];
$sql_categorias = "SELECT DISTINCT Categoria FROM producto WHERE Categoria IS NOT NULL AND Categoria != '' ORDER BY Categoria ASC";
$resultado_categorias = mysqli_query($conn, $sql_categorias);
if ($resultado_categorias) {
    while ($fila_cat = mysqli_fetch_assoc($resultado_categorias)) {
        $categorias_para_filtro[] = $fila_cat['Categoria'];
    }
}
// --- FIN OBTENER CATEGORÍAS ---

// --- LÓGICA DE FILTRO POR CATEGORÍA ---
$filtro_categoria_seleccionada = trim($_GET['filtro_categoria'] ?? '');

$where_clauses = [];
$params = [];
$types = "";

if (!empty($filtro_categoria_seleccionada)) {
    $where_clauses[] = "Categoria = ?";
    $params[] = $filtro_categoria_seleccionada;
    $types .= "s";
}

$sql_base = "SELECT * FROM producto";
$sql_where = "";
if (!empty($where_clauses)) {
    $sql_where = " WHERE " . implode(" AND ", $where_clauses);
}
$sql_order = " ORDER BY Fecha_Vencimiento ASC";
$sql_final = $sql_base . $sql_where . $sql_order;
// --- FIN LÓGICA DE FILTRO ---

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Existentes - Bloomware</title>
    <link rel="stylesheet" href="css/Estilos.css">
</head>
<body>
    <div class="page-container table-page-container">
        <header class="table-header">
             <a href="../principal.php"><img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" /></a>
             <h1>Productos Existentes</h1>
             <div class="header-actions">
                 <a href="../principal.php" class="btn-accion btn-regresar">Regresar</a>
                 <a href="producto.php" class="btn-accion btn-nuevo">Registrar Nuevo</a>
             </div>
        </header>

        <div class="message-container">
            <?php if (isset($_GET['mensaje'])) : ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
        </div>

        <!-- ===== FORMULARIO DE FILTRO (CON DESPLEGABLE DE CATEGORÍA) ===== -->
        <div class="filter-form-container card-layout">
            <form action="read.php" method="GET" class="filter-form">
                <!-- El filter-grid-simple ya no es necesario si usamos flex para el form -->
                <div class="filter-group">
                    <label for="filtro_categoria_select">Filtrar por Categoría:</label>
                    <select name="filtro_categoria" id="filtro_categoria_select">
                        <option value="">-- Todas las Categorías --</option>
                        <?php foreach ($categorias_para_filtro as $cat_filtro): ?>
                            <option value="<?php echo htmlspecialchars($cat_filtro); ?>" <?php echo ($filtro_categoria_seleccionada == $cat_filtro) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat_filtro); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-accion btn-filtrar btn-filtro-pequeno">Filtrar</button>
                    <a href="read.php" class="btn-accion btn-limpiar-filtros btn-filtro-pequeno">Limpiar</a>
                </div>
            </form>
        </div>
        <!-- ===== FIN FORMULARIO DE FILTROS ===== -->

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
                    $stmt = mysqli_prepare($conn, $sql_final);
                    if ($stmt) {
                        if (!empty($params)) { mysqli_stmt_bind_param($stmt, $types, ...$params); }
                        if (mysqli_stmt_execute($stmt)) {
                            $resultado_productos = mysqli_stmt_get_result($stmt);
                            if (mysqli_num_rows($resultado_productos) > 0) {
                                $dias_umbral_alerta = 30; $fecha_actual_dt = null;
                                try { $fecha_actual_dt = new DateTime(); $fecha_actual_dt->setTime(0,0,0); } catch (Exception $e) {}
                                while ($row = mysqli_fetch_assoc($resultado_productos)) {
                                    $id_producto_esc = htmlspecialchars($row['ID_Producto'], ENT_QUOTES, 'UTF-8');
                                    $nombre_producto_esc = htmlspecialchars($row['Nombre_Producto'], ENT_QUOTES, 'UTF-8');
                                    $cantidad_esc = htmlspecialchars($row['Cantidad'], ENT_QUOTES, 'UTF-8');
                                    $precio_esc = htmlspecialchars($row['Precio'], ENT_QUOTES, 'UTF-8');
                                    $categoria_esc = htmlspecialchars($row['Categoria'], ENT_QUOTES, 'UTF-8');
                                    $lote_esc = htmlspecialchars($row['Lote'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $fecha_vencimiento = $row['Fecha_Vencimiento'] ?? null;
                                    $fecha_vencimiento_esc = $fecha_vencimiento ? htmlspecialchars($fecha_vencimiento, ENT_QUOTES, 'UTF-8') : 'N/A';
                                    $row_class = ''; $tooltip_text = '';
                                    if ($fecha_actual_dt && $fecha_vencimiento && $fecha_vencimiento !== '0000-00-00') {
                                        try {
                                            $fecha_vencimiento_dt = new DateTime($fecha_vencimiento); $fecha_vencimiento_dt->setTime(0,0,0);
                                            if ($fecha_vencimiento_dt < $fecha_actual_dt) { $row_class = 'expired'; $tooltip_text = 'Vencido el ' . $fecha_vencimiento_dt->format('d/m/Y');
                                            } else { $intervalo = $fecha_actual_dt->diff($fecha_vencimiento_dt); $dias_restantes = (int)$intervalo->format('%r%a');
                                                if ($dias_restantes <= $dias_umbral_alerta) { $row_class = 'expiring-soon';
                                                    if ($dias_restantes == 0) { $tooltip_text = '¡Vence Hoy!'; }
                                                    elseif ($dias_restantes == 1) { $tooltip_text = 'Vence Mañana'; }
                                                    else { $tooltip_text = 'Vence en ' . $dias_restantes . ' días (' . $fecha_vencimiento_dt->format('d/m/Y') . ')'; }
                                                } else { $tooltip_text = 'Vence el ' . $fecha_vencimiento_dt->format('d/m/Y'); }
                                            }
                                        } catch (Exception $e) { $row_class = 'invalid-date'; $tooltip_text = 'Fecha inválida'; }
                                    } elseif ($fecha_vencimiento === '0000-00-00') { $tooltip_text = 'Fecha inválida'; $row_class = 'invalid-date';
                                    } elseif (!$fecha_vencimiento) { $tooltip_text = 'Sin fecha';}

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
                                }
                            } else {
                                if (!empty($filtro_categoria_seleccionada)) { echo "<tr><td colspan='8' style='text-align: center; padding: 20px;'>No se encontraron productos para la categoría '<strong>" . htmlspecialchars($filtro_categoria_seleccionada) . "</strong>'.</td></tr>";
                                } else { echo "<tr><td colspan='8' style='text-align: center; padding: 20px;'>No se encuentran productos registrados.</td></tr>"; }
                            }
                        } else { echo "<tr><td colspan='8' style='color: red;'>Error al ejecutar la consulta: " . htmlspecialchars(mysqli_stmt_error($stmt), ENT_QUOTES, 'UTF-8') . "</td></tr>"; }
                        mysqli_stmt_close($stmt);
                    } else { echo "<tr><td colspan='8' style='color: red;'>Error al preparar la consulta: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($conn)) { mysqli_close($conn); } ?>

    <!-- === JAVASCRIPT PARA BOTONES DE LA TABLA === -->
    <script>
        function eliminarProducto(productoId) {
            if (confirm(`¿Estás seguro de que deseas eliminar el producto con ID ${productoId}? Esta acción no se puede deshacer.`)) {
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
            window.location.href = `producto.php?${params.toString()}`;
        }
    </script>
    <!-- === FIN JAVASCRIPT === -->

</body>
</html>