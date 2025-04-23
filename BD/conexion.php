<?php
    $host = 'localhost';
    $user = 'root';
    $clave = '';
    $bd = 'bloomware';

    // Creación de la conexión a MySQL
    $conn = new mysqli($host, $user, $clave, $bd);
    
    // Verificación de la conexión
    if ($conn->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;}
    
    
    //echo"Conexión exitosa a la base de datos.";
?>