<?php
    $host = "localhost:3306";
    $user = "root";
    $clave = "02S93m66n63.";
    $bd = "bloomware";

    // Creación de la conexión a MySQL
    $conn = new mysqli($host, $user, $clave, $bd);
    
    // Verificación de la conexión
    if ($conn->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;}
    
    
    //echo"Conexión exitosa a la base de datos.";
?>