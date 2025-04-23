<?php
require 'conexion.php';

$consulta_bloomware = mysqli_query($conn, "SELECT * FROM producto");

if (mysqli_num_rows($consulta_bloomware) > 0){
    while ($row = mysqli_fetch_assoc($consulta_bloomware)) {
        echo "<tr>
                <td>{$row['ID_Producto']}</td>
                <td>{$row['Nombre_Producto']}</td>
                <td>{$row['Cantidad']}</td>
                <td>{$row['Precio']}</td>
                <td>{$row['Categoria']}</td>
                <td>{$row['Lote']}</td>
                <td>{$row['Fecha_vencimiento']}</td>
                <td>
                    <button onclick='eliminarReservacion({$row['ID_Producto']})'>Eliminar</button>
                    <button onclick='mostrarFormularioActualizar({$row['ID_Producto']}, \"{$row['Nombre_Producto']}\", \"{$row['Cantidad']}\", \"{$row['Precio']}\", \"{$row['Categoria']}\", \"{$row['Lote']}\", \"{$row['Fecha_vencimiento']}\")'>Actualizar</button>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='9'>No se encuentran productos registrados.</td></tr>";
}
?>
