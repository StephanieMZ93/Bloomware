<?php
$host = 'localhost';
$user = 'root';
$clave = ''; 
$bd = 'bloomware';

// Crear conexión
$conn = new mysqli($host, $user, $clave, $bd);

// Verificar conexión
if ($conn->connect_error) {
    error_log("Fallo al conectar a MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error);
    // Mensaje genérico para el usuario y detener script
    die("Error crítico: No se pudo establecer conexión con la base de datos. Intente más tarde o contacte al administrador.");
}

// Establecer el charset 
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error cargando el conjunto de caracteres utf8mb4: %s\n", $conn->error);
}

?>