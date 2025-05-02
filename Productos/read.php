<?php
// read.php -

// Verifica si la variable de conexión está disponible
if (!isset($conn) || !$conn) { 
    echo "<tr><td colspan='8' style='color: red; text-align: center;'><strong>Error Crítico:</strong> No hay conexión a la base de datos disponible en read.php.</td></tr>";
} else {
    // Ejecutar la consulta 
    // Ordenar por fecha ayuda a ver los próximos a vencer primero
    $consulta_bloomware = mysqli_query($conn, "SELECT * FROM producto ORDER BY Fecha_Vencimiento ASC");

    // Verifica si la consulta falló
    if (!$consulta_bloomware) {
        echo "<tr><td colspan='8' style='color: red;'>Error en la consulta SQL: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</td></tr>";
    }
    // SI la consulta fue exitosa Y hay filas...
    elseif (mysqli_num_rows($consulta_bloomware) > 0) {

        // --- Configuración para alertas de vencimiento ---
        $dias_umbral_alerta = 30; // Alertar si faltan 30 días o menos

        // Obtener la fecha actual una sola vez fuera del bucle
        $fecha_actual_dt = null; 
        try {
             $fecha_actual_dt = new DateTime();
             $fecha_actual_dt->setTime(0,0,0); 
        } catch (Exception $e) {
             echo "<tr><td colspan='8' style='color: orange;'>Advertencia: No se pudo obtener la fecha actual. Las alertas de vencimiento pueden no funcionar.</td></tr>";
             error_log("Error al crear DateTime para fecha actual: " . $e->getMessage()); 
        }

        // --- Iterar sobre los resultados ---
        while ($row = mysqli_fetch_assoc($consulta_bloomware)) {
            // --- Obtener y Escapar Datos ---
            $id_producto_esc = htmlspecialchars($row['ID_Producto'], ENT_QUOTES, 'UTF-8');
            $nombre_producto_esc = htmlspecialchars($row['Nombre_Producto'], ENT_QUOTES, 'UTF-8');
            $cantidad_esc = htmlspecialchars($row['Cantidad'], ENT_QUOTES, 'UTF-8');
            $precio_esc = htmlspecialchars($row['Precio'], ENT_QUOTES, 'UTF-8');
            $categoria_esc = htmlspecialchars($row['Categoria'], ENT_QUOTES, 'UTF-8');
            $lote_esc = htmlspecialchars($row['Lote'], ENT_QUOTES, 'UTF-8');
            $fecha_vencimiento = $row['Fecha_Vencimiento'] ?? null; 
            $fecha_vencimiento_esc = $fecha_vencimiento ? htmlspecialchars($fecha_vencimiento, ENT_QUOTES, 'UTF-8') : 'N/A';

            $row_class = ''; 
            $tooltip_text = ''; 

            // Solo calcular si tenemos fecha actual válida y fecha de vencimiento del producto
            if ($fecha_actual_dt && $fecha_vencimiento && $fecha_vencimiento !== '0000-00-00') { 
                try {
                    $fecha_vencimiento_dt = new DateTime($fecha_vencimiento);
                    $fecha_vencimiento_dt->setTime(0,0,0); // Comparar solo fecha

                    // Comprobar si ya está vencido
                    if ($fecha_vencimiento_dt < $fecha_actual_dt) {
                        $row_class = 'expired'; // Clase para vencido
                        $tooltip_text = 'Producto Vencido el ' . $fecha_vencimiento_dt->format('d/m/Y');
                    }
                    // Si no está vencido, comprobar si está próximo a vencer
                    else {
                        $intervalo = $fecha_actual_dt->diff($fecha_vencimiento_dt);
                        $dias_restantes = (int)$intervalo->format('%r%a'); 

                        if ($dias_restantes <= $dias_umbral_alerta) {
                            $row_class = 'expiring-soon'; // Clase para próximo a vencer
                            if ($dias_restantes == 0) {
                               $tooltip_text = '¡Vence Hoy!';
                            } elseif ($dias_restantes == 1) {
                               $tooltip_text = 'Vence Mañana';
                            } else {
                               $tooltip_text = 'Vence en ' . $dias_restantes . ' días (' . $fecha_vencimiento_dt->format('d/m/Y') . ')';
                            }
                        } else {
                            // No está próximo a vencer
                            $tooltip_text = 'Vence el ' . $fecha_vencimiento_dt->format('d/m/Y');
                        }
                    }
                } catch (Exception $e) {
                    $row_class = 'invalid-date';
                    $tooltip_text = 'Fecha de vencimiento inválida (' . $fecha_vencimiento_esc . ')';
                    error_log("Error parseando fecha en read.php: " . $fecha_vencimiento . " - " . $e->getMessage());
                }
            } elseif ($fecha_vencimiento === '0000-00-00') {
                 $tooltip_text = 'Fecha de vencimiento inválida (0000-00-00)';
                 $row_class = 'invalid-date';
            } elseif (!$fecha_vencimiento) {
                 $tooltip_text = 'Sin fecha de vencimiento registrada';
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

        } 

    } 

    else {
        // Mensaje si no hay productos
        echo "<tr><td colspan='8' style='text-align: center; padding: 20px;'>No se encuentran productos registrados.</td></tr>";
    } 

} 
?>