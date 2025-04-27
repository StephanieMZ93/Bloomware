<?php 

require_once('conexion.php'); 

if (!$conn) { 
    die("<tr><td colspan='8'>Error de conexión: " . htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8') . "</td></tr>"); // Line 6
} 

// Realiza la consulta 
$consulta_bloomware = mysqli_query($conn, "SELECT * FROM producto");

// Verifica si la consulta falló
if (!$consulta_bloomware) {
    echo "<tr><td colspan='8'>Error en la consulta SQL: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</td></tr>"; // Line 14
} elseif (mysqli_num_rows($consulta_bloomware) > 0) { 
    while ($row = mysqli_fetch_assoc($consulta_bloomware)) { 
        $id_producto_esc = htmlspecialchars($row['ID_Producto'], ENT_QUOTES, 'UTF-8');
        $nombre_producto_esc = htmlspecialchars($row['Nombre_Producto'], ENT_QUOTES, 'UTF-8');
        $cantidad_esc = htmlspecialchars($row['Cantidad'], ENT_QUOTES, 'UTF-8');
        $precio_esc = htmlspecialchars($row['Precio'], ENT_QUOTES, 'UTF-8');
        $categoria_esc = htmlspecialchars($row['Categoria'], ENT_QUOTES, 'UTF-8');
        $lote_esc = htmlspecialchars($row['Lote'], ENT_QUOTES, 'UTF-8');
        $fecha_vencimiento_esc = isset($row['Fecha_Vencimiento']) ? htmlspecialchars($row['Fecha_Vencimiento'], ENT_QUOTES, 'UTF-8') : '';

        // --- Inicio de la fila ---
        echo "<tr>"; 
        echo "<td data-label='ID Producto'>{$id_producto_esc}</td>";
        echo "<td data-label='Nombre Producto'>{$nombre_producto_esc}</td>";
        echo "<td data-label='Cantidad'>{$cantidad_esc}</td>";
        echo "<td data-label='Precio'>{$precio_esc}</td>";
        echo "<td data-label='Categoría'>{$categoria_esc}</td>";
        echo "<td data-label='Lote'>{$lote_esc}</td>";
        echo "<td data-label='Fecha Vencimiento'>{$fecha_vencimiento_esc}</td>";
        echo "<td>";
            // Botón Eliminar
            echo "<button onclick='eliminarProducto({$id_producto_esc})'>Eliminar</button>";
            // Botón Actualizar
            echo "<button onclick='mostrarFormularioActualizar({$id_producto_esc}, \"{$nombre_producto_esc}\", \"{$cantidad_esc}\", \"{$precio_esc}\", \"{$categoria_esc}\", \"{$lote_esc}\", \"{$fecha_vencimiento_esc}\")'>Actualizar</button>";
        echo "</td>";
        echo "</tr>";
    } 
} else { 
    echo "<tr><td colspan='8'>No se encuentran productos registrados.</td></tr>";
} 

// Cierra la conexión 
mysqli_close($conn);

?>