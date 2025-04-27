<?php
// Archivo: create.php
require 'Productos/conexion.php'; // Incluir conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //seguridad de los datos del formulario
    $Nombre_Producto = mysql_real_escape_string($conn, $_POST['Nombre_Producto']);
    $Cantidad = mysql_real_escape_string($conn, $_POST['Cantidad']);
    $Precio = floatval($conn, $_POST['Precio']);
    $Categoria = mysql_real_escape_string($conn, $_POST['Categoria']);
    $Lote = mysql_real_escape_string($conn, $_POST['Lote']);
    $Fecha_Vencimiento =  $_POST['Fecha_Vencimiento'];
}     

// consulta SQL para insertar los Datos
$sql = "INSERT INTO producto (Nombre_Producto, Cantidad, Precio, Categoria, Lote, Fecha_Vencimiento)
        VALUES ('$Nombre_Producto', '$Cantidad', '$Precio', '$Categoria', '$Lote', '$Fecha_Vencimiento')";


// Ejecutar Consulta
if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success', 'message' => 'Producto registrado exitosamente']);
    header('Location: producto.php?mensaje=Producto registrado exitosamente');
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar: ' . mysqli_error($conn)]);
}

// Cerrar la conexión
mysqli_close($conn);
?>

