<?php
// Archivo: Productos/read.php
// Página principal que muestra la tabla de productos CON FILTROS.

session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

require_once(__DIR__ . '/../BD/Conexion.php');

if (!isset($conn) || !$conn || $conn->connect_error) {
    $db_error_message = isset($conn) && $conn->connect_error ? $conn->connect_error : mysqli_connect_error();
    error_log("Error crítico de conexión en read.php: " . $db_error_message);
    die("Error crítico: No se pudo conectar. Contacte al administrador.");
}

// --- LÓGICA DE FILTROS ---
// Inicializar variables de filtro (para mantener valores en los inputs)
$filtro_nombre = trim($_GET['filtro_nombre'] ?? '');
$filtro_cantidad_min = trim($_GET['filtro_cantidad_min'] ?? '');
$filtro_cantidad_max = trim($_GET['filtro_cantidad_max'] ?? '');
$filtro_precio_min = trim($_GET['filtro_precio_min'] ?? '');
$filtro_precio_max = trim($_GET['filtro_precio_max'] ?? '');
$filtro_categoria = trim($_GET['filtro_categoria'] ?? '');
$filtro_lote = trim($_GET['filtro_lote'] ?? '');
$filtro_fecha_venc_desde = trim($_GET['filtro_fecha_venc_desde'] ?? '');
$filtro_fecha_venc_hasta = trim($_GET['filtro_fecha_venc_hasta'] ?? '');

// Construir la cláusula WHERE dinámicamente
$where_clauses = [];
$params = []; // Para sentencias preparadas
$types = "";    // Tipos para sentencias preparadas

if (!empty($filtro_nombre)) {
    $where_clauses[] = "Nombre_Producto LIKE ?";
    $params[] = "%" . $filtro_nombre . "%";
    $types .= "s";
}
if (is_numeric($filtro_cantidad_min)) {
    $where_clauses[] = "Cantidad >= ?";
    $params[] = (int)$filtro_cantidad_min;
    $types .= "i";
}
if (is_numeric($filtro_cantidad_max)) {
    $where_clauses[] = "Cantidad <= ?";
    $params[] = (int)$filtro_cantidad_max;
    $types .= "i";
}
if (is_numeric($filtro_precio_min)) {
    $where_clauses[] = "Precio >= ?";
    $params[] = (float)$filtro_precio_min;
    $types .= "d";
}
if (is_numeric($filtro_precio_max)) {
    $where_clauses[] = "Precio <= ?";
    $params[] = (float)$filtro_precio_max;
    $types .= "d";
}
if (!empty($filtro_categoria)) {
    $where_clauses[] = "Categoria LIKE ?";
    $params[] = "%" . $filtro_categoria . "%";
    $types .= "s";
}
if (!empty($filtro_lote)) {
    $where_clauses[] = "Lote LIKE ?";
    $params[] = "%" . $filtro_lote . "%";
    $types .= "s";
}
if (!empty($filtro_fecha_venc_desde)) {
    $where_clauses[] = "Fecha_Vencimiento >= ?";
    $params[] = $filtro_fecha_venc_desde;
    $types .= "s";
}
if (!empty($filtro_fecha_venc_hasta)) {
    $where_clauses[] = "Fecha_Vencimiento <= ?";
    $params[] = $filtro_fecha_venc_hasta;
    $types .= "s";
}

$sql_base = "SELECT * FROM producto";
$sql_where = "";
if (!empty($where_clauses)) {
    $sql_where = " WHERE " . implode(" AND ", $where_clauses);
}
$sql_order = " ORDER BY Fecha_Vencimiento ASC"; // O el orden que prefieras
$sql_final = $sql_base . $sql_where . $sql_order;

// --- FIN LÓGICA DE FILTROS ---

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

        <!-- ===== FORMULARIO DE FILTROS ===== -->
        <div class="filter-form-container card-layout"> <!-- Usamos card-layout para un estilo similar -->
            <form action="read.php" method="GET" class="filter-form">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="filtro_nombre">Nombre:</label>
                        <input type="text" id="filtro_nombre" name="filtro_nombre" value="<?php echo htmlspecialchars($filtro_nombre); ?>" placeholder="Buscar por nombre...">
                    </div>
                    <div class="filter-group">
                        <label for="filtro_categoria">Categoría:</label>
                        <input type="text" id="filtro_categoria" name="filtro_categoria" value="<?php echo htmlspecialchars($filtro_categoria); ?>" placeholder="Buscar por categoría...">
                    </div>
                    <div class="filter-group">
                        <label for="filtro_lote">Lote:</label>
                        <input type="text" id="filtro_lote" name="filtro_lote" value="<?php echo htmlspecialchars($filtro_lote); ?>" placeholder="Buscar por lote...">
                    </div>
                    <div class="filter-group range-group">
                        <label>Cantidad:</label>
                        <input type="number" name="filtro_cantidad_min" value="<?php echo htmlspecialchars($filtro_cantidad_min); ?>" placeholder="Mín." min="0">
                        <span>-</span>
                        <input type="number" name="filtro_cantidad_max" value="<?php echo htmlspecialchars($filtro_cantidad_max); ?>" placeholder="Máx." min="0">
                    </div>
                    <div class="filter-group range-group">
                        <label>Precio:</label>
                        <input type="number" name="filtro_precio_min" step="0.01" value="<?php echo htmlspecialchars($filtro_precio_min); ?>" placeholder="Mín." min="0">
                        <span>-</span>
                        <input type="number" name="filtro_precio_max" step="0.01" value="<?php echo htmlspecialchars($filtro_precio_max); ?>" placeholder="Máx." min="0">
                    </div>
                    <div class="filter-group range-group">
                        <label>Fecha Vencimiento:</label>
                        <input type="date" name="filtro_fecha_venc_desde" value="<?php echo htmlspecialchars($filtro_fecha_venc_desde); ?>" title="Desde">
                        <span>-</span>
                        <input type="date" name="filtro_fecha_venc_hasta" value="<?php echo htmlspecialchars($filtro_fecha_venc_hasta); ?>" title="Hasta">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-accion btn-filtrar">Filtrar</button>
                    <a href="read.php" class="btn-accion btn-limpiar-filtros">Limpiar Filtros</a>
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
                    // --- Ejecutar la consulta con filtros ---
                    $stmt = mysqli_prepare($conn, $sql_final);

                    if ($stmt) {
                        if (!empty($params)) { // Solo vincular si hay parámetros
                            // El operador '...' (splat) desempaqueta el array $params
                            mysqli_stmt_bind_param($stmt, $types, ...$params);
                        }

                        if (mysqli_stmt_execute($stmt)) {
                            $resultado_productos = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($resultado_productos) > 0) {
                                $dias_umbral_alerta = 30;
                                $fecha_actual_dt = null;
                                try {
                                     $fecha_actual_dt = new DateTime();
                                     $fecha_actual_dt->setTime(0,0,0);
                                } catch (Exception $e) { /* ... manejo error fecha actual ... */ }

                                while ($row = mysqli_fetch_assoc($resultado_productos)) {
                                    // ... (TODA LA LÓGICA PARA PROCESAR Y MOSTRAR CADA FILA, INCLUYENDO ALERTAS DE VENCIMIENTO) ...
                                    // (Este bloque es igual al de tu read.php anterior)
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
                                                $tooltip_text = 'Vencido el ' . $fecha_vencimiento_dt->format('d/m/Y');
                                            } else {
                                                $intervalo = $fecha_actual_dt->diff($fecha_vencimiento_dt);
                                                $dias_restantes = (int)$intervalo->format('%r%a');
                                                if ($dias_restantes <= $dias_umbral_alerta) {
                                                    $row_class = 'expiring-soon';
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
                                echo "<tr><td colspan='8' style='text-align: center; padding: 20px;'>No se encontraron productos que coincidan con los filtros aplicados.</td></tr>";
                            }
                        } else {
                             echo "<tr><td colspan='8' style='color: red;'>Error al ejecutar la consulta de productos: " . htmlspecialchars(mysqli_stmt_error($stmt), ENT_QUOTES, 'UTF-8') . "</td></tr>";
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        echo "<tr><td colspan='8' style='color: red;'>Error al preparar la consulta de productos: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($conn)) { mysqli_close($conn); } ?>
    <script> /* ... (tus funciones JS eliminarProducto y mostrarFormularioActualizar) ... */ </script>
</body>
</html>