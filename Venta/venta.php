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
    error_log("Error crítico de conexión en venta.php: " . $db_error_message);
    die("Error crítico: No se pudo conectar. Contacte al administrador.");
}

// Obtener lista de productos disponibles
$productos_disponibles = [];
// Usar prepared statements para consistencia (aunque aquí es un SELECT simple)
$sql_productos = "SELECT ID_Producto, Nombre_Producto, Precio, Cantidad FROM producto WHERE Cantidad > 0 ORDER BY Nombre_Producto ASC";
$stmt_prods = $conn->prepare($sql_productos);
if ($stmt_prods) {
    $stmt_prods->execute();
    $res_productos = $stmt_prods->get_result();
    if ($res_productos) {
        while ($row_prod = $res_productos->fetch_assoc()) {
            $productos_disponibles[] = $row_prod;
        }
    }
    $stmt_prods->close();
} else {
    error_log("Error preparando consulta de productos en venta.php: " . $conn->error);
    // Podrías mostrar un error o manejarlo de otra forma
}


if (!isset($_SESSION['carrito_venta'])) {
    $_SESSION['carrito_venta'] = [];
}

// --- LÓGICA PARA MANEJAR ACCIONES DEL CARRITO (Agregar, Remover) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_carrito'])) {
    $accion = $_POST['accion_carrito'];

    if ($accion === 'agregar') {
        $id_producto_carrito = filter_input(INPUT_POST, 'id_producto_seleccionado', FILTER_VALIDATE_INT);
        $cantidad_carrito = filter_input(INPUT_POST, 'cantidad_seleccionada', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

        if ($id_producto_carrito && $cantidad_carrito) {
            $producto_info_carrito = null;
            // Buscar el producto en DB para obtener la info más actualizada (especialmente stock)
            $sql_find_prod = "SELECT ID_Producto, Nombre_Producto, Precio, Cantidad FROM producto WHERE ID_Producto = ? AND Cantidad > 0";
            $stmt_find = $conn->prepare($sql_find_prod);
            if ($stmt_find) {
                $stmt_find->bind_param("i", $id_producto_carrito);
                $stmt_find->execute();
                $res_find = $stmt_find->get_result();
                if ($res_find && $res_find->num_rows > 0) {
                    $producto_info_carrito = $res_find->fetch_assoc();
                }
                $stmt_find->close();
            }


            if ($producto_info_carrito) {
                // ¿Cuánto hay ya en el carrito de este producto?
                $cantidad_ya_en_carrito = 0;
                if (isset($_SESSION['carrito_venta'][$id_producto_carrito])) {
                    $cantidad_ya_en_carrito = $_SESSION['carrito_venta'][$id_producto_carrito]['cantidad'];
                }

                // Total que se intenta tener en el carrito (actual + nuevo)
                $cantidad_total_deseada = $cantidad_ya_en_carrito + $cantidad_carrito;


                if ($cantidad_total_deseada <= $producto_info_carrito['Cantidad']) {
                    $item_key_carrito = $id_producto_carrito;

                    if (isset($_SESSION['carrito_venta'][$item_key_carrito])) {
                        $_SESSION['carrito_venta'][$item_key_carrito]['cantidad'] = $cantidad_total_deseada; // Actualiza con el total deseado
                        $_SESSION['mensaje_carrito'] = "Cantidad actualizada en el carrito.";
                    } else {
                        $_SESSION['carrito_venta'][$item_key_carrito] = [
                            'id_producto' => $producto_info_carrito['ID_Producto'],
                            'nombre' => $producto_info_carrito['Nombre_Producto'],
                            'precio_unitario' => $producto_info_carrito['Precio'],
                            'cantidad' => $cantidad_carrito, // La cantidad que se está agregando ahora
                            'stock_disponible_al_agregar' => $producto_info_carrito['Cantidad'] // Info del momento
                        ];
                        $_SESSION['mensaje_carrito'] = htmlspecialchars($producto_info_carrito['Nombre_Producto']) . " agregado al carrito.";
                    }
                } else {
                    $disponible_real = $producto_info_carrito['Cantidad'] - $cantidad_ya_en_carrito;
                    $_SESSION['error_carrito'] = "No hay suficiente stock para " . htmlspecialchars($producto_info_carrito['Nombre_Producto']) . ". Puede agregar hasta " . max(0, $disponible_real) . " unidad(es) más. Stock actual total: " . $producto_info_carrito['Cantidad'];
                }
            } else {
                $_SESSION['error_carrito'] = "Producto no encontrado o sin stock.";
            }
        } else {
            $_SESSION['error_carrito'] = "Seleccione un producto y una cantidad válida.";
        }
    } elseif ($accion === 'remover_item') {
        // ... (tu lógica de remover está bien) ...
        $id_remover = filter_input(INPUT_POST, 'id_producto_remover', FILTER_VALIDATE_INT);
        if ($id_remover && isset($_SESSION['carrito_venta'][$id_remover])) {
            unset($_SESSION['carrito_venta'][$id_remover]);
            $_SESSION['mensaje_carrito'] = "Producto removido del carrito.";
        }
    } elseif ($accion === 'vaciar_carrito') {
        $_SESSION['carrito_venta'] = [];
        $_SESSION['mensaje_carrito'] = "Carrito vaciado.";
    }

    header("Location: venta.php");
    exit;
}

$carrito_items = $_SESSION['carrito_venta'];
$total_general_carrito = 0;
// Recalcular total del carrito para la vista
foreach ($carrito_items as $item_carrito_vista) {
    $total_general_carrito += $item_carrito_vista['cantidad'] * $item_carrito_vista['precio_unitario'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nueva Venta - Bloomware</title>
    <link rel="stylesheet" href="css/Estilos.css">
    <link rel="stylesheet" href="../css/IPrincipal.css">
    <style>
        /* Estilos básicos para mensajes de alerta */
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
        .btn-remover-item { background-color: #ff6b6b; color:white; border:none; padding: 2px 6px; cursor:pointer; border-radius:3px; }
        .btn-remover-item:hover { background-color: #e05252; }
        .btn-vaciar-carrito { background-color: #f0ad4e; color:white; border:none; padding: 8px 12px; cursor:pointer; border-radius:4px; text-decoration: none;}
        .btn-vaciar-carrito:hover { background-color: #ec971f; }

    </style>
</head>
<body>
    <div class="page-container form-page-container">
        <header class="form-header">
            <a href="../principal.php" class="regresar-link">← Volver a Principal</a>
            <img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
        </header>

        <div class="message-container">
            <?php
            // Mensajes del carrito
            if (isset($_SESSION['mensaje_carrito'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensaje_carrito']) . '</div>';
                unset($_SESSION['mensaje_carrito']);
            }
            if (isset($_SESSION['error_carrito'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_carrito']) . '</div>';
                unset($_SESSION['error_carrito']);
            }
            // Mensajes de procesar_venta.php (si redirige aquí con error)
            if (isset($_SESSION['mensaje_venta'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensaje_venta']) . '</div>';
                unset($_SESSION['mensaje_venta']);
            }
            if (isset($_SESSION['error_venta'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_venta']) . '</div>';
                unset($_SESSION['error_venta']);
            }
            // Mensajes antiguos vía GET (considera unificar a sesión)
            if (isset($_GET['error_procesar'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error_procesar']) . '</div>';
            }
            if (isset($_GET['venta_exitosa'])) {
                echo '<div class="alert alert-success">¡Venta registrada exitosamente! ID Venta: ' . htmlspecialchars($_GET['venta_exitosa']) . '</div>';
            }
            ?>
        </div>

        <div class="venta-layout">
            <div class="producto-selector-section card-layout">
                <h3>Agregar Productos al Carrito</h3>
                <form action="venta.php" method="POST" class="formulario-inline">
                    <input type="hidden" name="accion_carrito" value="agregar">
                    <div class="form-group">
                        <label for="id_producto_seleccionado">Producto:</label>
                        <select name="id_producto_seleccionado" id="id_producto_seleccionado" required>
                            <option value="">-- Seleccione un producto --</option>
                            <?php foreach ($productos_disponibles as $producto): ?>
                                <option value="<?php echo $producto['ID_Producto']; ?>"
                                        data-precio="<?php echo $producto['Precio']; ?>"
                                        data-stock="<?php echo $producto['Cantidad']; ?>">
                                    <?php echo htmlspecialchars($producto['Nombre_Producto']) . " (Stock: " . $producto['Cantidad'] . " | Precio: $" . number_format($producto['Precio'], 2) . ")"; // Usar 2 decimales para precio ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cantidad_seleccionada">Cantidad:</label>
                        <input type="number" name="cantidad_seleccionada" id="cantidad_seleccionada" value="1" min="1" required style="width: 80px;">
                    </div>
                    <button type="submit" class="btn-accion btn-agregar-carrito">Agregar</button>
                </form>
            </div>

            <div class="carrito-section card-layout">
                <!-- ... (tu HTML para el título del carrito está bien) ... -->
                 <div class="carrito-titulo-con-imagen">
                    <h3>Carrito de Compras</h3>
                    <img src="../img/Carrito.png" alt="Icono de Carrito" class="carrito-icono">
                </div>

                <?php if (empty($carrito_items)): ?>
                    <div class="carrito-ilustracion-contenedor">
                        <img src="../img/Carrito.png" alt="Ilustración del Carrito de Compras" class="carrito-ilustracion-principal">
                    </div>
                    <div class="carrito-vacio-placeholder">
                        <p>El carrito está vacío. ¡Agrega productos!</p>
                    </div>
                <?php else: ?>
                    <div class="carrito-items table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cant.</th>
                                    <th>P. Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($carrito_items as $item_key => $item_carrito):
                                    $subtotal_item = $item_carrito['cantidad'] * $item_carrito['precio_unitario'];
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item_carrito['nombre']); ?></td>
                                        <td><?php echo $item_carrito['cantidad']; ?></td>
                                        <td>$<?php echo number_format($item_carrito['precio_unitario'], 2); // Usar 2 decimales ?></td>
                                        <td>$<?php echo number_format($subtotal_item, 2); // Usar 2 decimales ?></td>
                                        <td>
                                            <form action="venta.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="accion_carrito" value="remover_item">
                                                <input type="hidden" name="id_producto_remover" value="<?php echo $item_key; // $item_key es el ID_Producto ?>">
                                                <button type="submit" class="btn-remover-item" title="Quitar del carrito">×</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="carrito-total">
                        <strong>Total Carrito: $<?php echo number_format($total_general_carrito, 2); // Usar 2 decimales ?></strong>
                    </div>
                    <?php if (!empty($carrito_items)): ?>
                    <form action="venta.php" method="POST" style="margin-top:10px; text-align:right;">
                        <input type="hidden" name="accion_carrito" value="vaciar_carrito">
                        <button type="submit" class="btn-accion btn-vaciar-carrito">Vaciar Carrito</button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($carrito_items)): ?>
        <div class="checkout-section card-layout" style="margin-top: 20px;">
            <h3>Finalizar Venta</h3>
            <!-- ***** CAMBIO IMPORTANTE AQUÍ: action="procesar_venta.php" ***** -->
            <form action="procesar_venta.php" method="POST" class="formulario">
                <div class="form-group">
                    <label for="nombre_cliente_temporal">Nombre del Cliente (Opcional):</label>
                    <input type="text" id="nombre_cliente_temporal" name="nombre_cliente_temporal" maxlength="100">
                </div>
                <p style="text-align:right; font-size: 1.1em; margin-top:15px;">
                    <strong>Total a Pagar: $<?php echo number_format($total_general_carrito, 2); // Usar 2 decimales ?></strong>
                </p>
                <!-- El href en el botón submit no tiene efecto, se quita. El action del form es lo que importa. -->
                <button type="submit" name="finalizar_compra" class="btn-registro btn-finalizar-compra" style="width:auto; margin: 20px auto 0; display:block;">
                    Registrar Venta
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <?php
        if (isset($conn) && method_exists($conn, 'close')) {
            $conn->close();
        }
    ?>
</body>
</html>