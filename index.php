<?php
// Activar la visualización de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Si el usuario ya está logueado, redirigirlo a principal.php
if (isset($_SESSION["usuario_id_usuario"])) { // Asegúrate que este nombre de sesión sea consistente
    header("Location: principal.php");
    exit();
}

// Incluir el archivo de conexión a la base de datos
require_once(__DIR__ . '/BD/conexion.php'); // $conn ahora está disponible

// Inicializar variables para mensajes de error
$error_message = "";

// Verificar si la conexión a la BD fue exitosa (Conexion.php debería usar die() si falla)
if (!isset($conn) || $conn === false ) { // $conn->connect_error solo si $conn es objeto
    error_log("Error fatal: La variable de conexión \$conn no es válida o conexion.php falló gravemente.");
    $error_message = "Error crítico del sistema [C1]. Contacte al administrador.";
} else if ($conn->connect_error) {
    error_log("Error de conexión a la base de datos: (" . $conn->connect_errno . ") " . $conn->connect_error);
    $error_message = "No se pudo conectar a la base de datos. Intente más tarde.";
} else {
    // La conexión está bien, proceder si es POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email_ingresado = trim($_POST["email"] ?? ''); // Usar trim y ??
        $password_ingresada = $_POST["password"] ?? ''; // No trim password

        if (empty($email_ingresado) || empty($password_ingresada)) {
            $error_message = "Por favor, ingrese su email y contraseña.";
        } else {
            // Usar los nombres de columna de tu tabla: id_usuario, roles_id
            $sql = "SELECT id_usuario, nombre, pass, roles_id, estado FROM `usuarios` WHERE email = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                error_log("Error al preparar la consulta (login): " . $conn->error);
                $error_message = "Error del sistema [P2]. Por favor, intente más tarde.";
            } else {
                $stmt->bind_param("s", $email_ingresado);

                if (!$stmt->execute()) {
                    error_log("Error al ejecutar la consulta (login): " . $stmt->error);
                    $error_message = "Error del sistema [E2] al procesar su solicitud.";
                } else {
                    $result = $stmt->get_result();

                    if ($result->num_rows === 1) { // Debería ser exactamente 1
                        $row = $result->fetch_assoc();

                        // Verificación de contraseña
                        if (password_verify($password_ingresada, $row["pass"])) {
                            if ($row["estado"] == 'activo') {
                                session_regenerate_id(true); // Por seguridad

                                // Usar los nombres de columna de tu tabla
                                $_SESSION["usuario_id_usuario"] = $row["id_usuario"]; // Correcto
                                $_SESSION["usuario_nombre"] = $row["nombre"];
                                $_SESSION["usuario_roles_id"] = $row["roles_id"];    // Correcto

                                header("Location: principal.php");
                                exit();
                            } else {
                                $error_message = "Su cuenta está suspendida. Contacte al administrador.";
                            }
                        } else {
                            $error_message = "Email o contraseña incorrectos. [debug pass verify failed]"; // Mensaje para depurar
                        }
                    } else {
                        $error_message = "Email o contraseña incorrectos. [debug user not found]"; // Mensaje para depurar
                    }
                }
                $stmt->close();
            }
        }
    }
}

// Cerrar la conexión solo si fue abierta y es válida
if (isset($conn) && is_object($conn) && method_exists($conn, 'close')) {
    // Solo cerrar si no hubo error de conexión inicial grave
    if (isset($conn->connect_error) ? !$conn->connect_error : true) {
         $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloomware - Inicio de sesión</title>
    <link rel="stylesheet" href="css/Estilos.css">
    <link rel="stylesheet" href="css/Font.css">

</head>
<body>
    <div class="container login-container"> <!-- Añadir login-container si necesitas estilos específicos -->
        <header>
            <img src="img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img">
        </header>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="Formulario">
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <label for="email" class="formulario__label-txt">
                <span class="icon-user"></span>Email
            </label>
            <input type="email" name="email" id="email" class="formulario__input-txt" maxlength="150" required autofocus placeholder="ejemplo@correo.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"> <!-- Mantener email si hay error -->

            <label for="password" class="formulario__label-txt">
                <span class="icon-key"></span>Contraseña
            </label>
            <input type="password" name="password" id="password" class="formulario__input-txt" maxlength="255" required placeholder="Tu contraseña">

            <button type="submit" class="iniciar-sesion">
                Iniciar Sesión
            </button>
        </form>
    </div>

    <footer>
        <p><i>© <?php echo date("Y"); ?> Bloomware. Todos los derechos reservados.</i></p>
    </footer>
</body>
</html>