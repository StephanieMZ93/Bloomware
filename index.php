<?php
// Activar la visualización de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Incluir el archivo de conexión a la base de datos
include("BD/conexion.php"); // Asegúrate que $conn se crea aquí

// Inicializar variables para mensajes de error
$error_message = "";

// Verificar si la conexión a la BD fue exitosa (hacerlo después del include)
if (!$conn) {
    error_log("Error fatal: La variable de conexión \$conn no es válida o conexion.php falló.");
    $error_message = "Error crítico del sistema. Contacte al administrador.";
    // No podemos continuar sin conexión
} else if ($conn->connect_error) {
    error_log("Error de conexión a la base de datos: " . $conn->connect_error);
    $error_message = "No se pudo conectar a la base de datos. Intente más tarde.";
} else {
    // La conexión está bien, proceder si es POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email_ingresado = $_POST["email"];
        $password_ingresada = $_POST["password"];

        if (empty($email_ingresado) || empty($password_ingresada)) {
            $error_message = "Por favor, ingrese su email y contraseña.";
        } else {
            $sql = "SELECT id, nombre, pass, roles_id, estado FROM `usuarios` WHERE email = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                error_log("Error al preparar la consulta: " . $conn->error);
                $error_message = "Error del sistema. Por favor, intente más tarde.";
            } else {
                $stmt->bind_param("s", $email_ingresado);

                if (!$stmt->execute()) {
                    error_log("Error al ejecutar la consulta: " . $stmt->error);
                    $error_message = "Error del sistema al procesar su solicitud.";
                } else {
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        if (password_verify($password_ingresada, $row["pass"])) {
                            if ($row["estado"] == 'activo') {
                                $_SESSION["usuario_id"] = $row["id"];
                                $_SESSION["usuario_nombre"] = $row["nombre"];
                                $_SESSION["usuario_rol_id"] = $row["roles_id"];

                                header("Location: principal.php");
                                exit();
                            } else {
                                $error_message = "Su cuenta está suspendida. Contacte al administrador.";
                            }
                        } else {
                            $error_message = "Email o contraseña incorrectos.";
                        }
                    } else {
                        $error_message = "Email o contraseña incorrectos.";
                    }
                }
                $stmt->close();
            }
        }
        // Cerrar la conexión solo si fue abierta y es válida
        if ($conn && !$conn->connect_error) {
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloomware - Inicio de sesión</title>
    <link rel="stylesheet" href="css/Estilos.css"> <!-- Asegúrate que esta ruta es correcta -->
    <link rel="stylesheet" href="css/Font.css"> <!-- Asegúrate que esta ruta es correcta -->
</head>

<body>
    <!-- <center> ya no es necesario con los estilos flexbox del body -->
    <div class="container">
        <header>
            <img src="img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img"> <!-- Asegúrate que esta ruta es correcta -->
        </header>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="Formulario">
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <label for="email" class="formulario__label-txt">
                <span class="icon-user"></span>Email <!-- Icono antes del texto -->
            </label>
            <input type="email" name="email" id="email" class="formulario__input-txt" maxlength="150" required autofocus placeholder="ejemplo@correo.com">

            <label for="password" class="formulario__label-txt">
                <span class="icon-key"></span>Contraseña <!-- Icono antes del texto -->
            </label>
            <input type="password" name="password" id="password" class="formulario__input-txt" maxlength="255" required placeholder="Tu contraseña">

            <button type="submit" class="iniciar-sesion">
                Iniciar Sesión
            </button>
        </form>
    </div>
    <!-- </center> -->

    <!-- <script src="JS/index.js"></script> --> <!-- Comentado por ahora para evitar interferencias JS -->

    <footer>
        <p><i>© <?php echo date("Y"); ?> Bloomware. Todos los derechos reservados.</i></p>
    </footer>
</body>

</html>