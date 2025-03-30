<?php
session_start();

// Incluir el archivo de conexión a la base de datos
include("conexion.php");

// Inicializar variables para mensajes de error
$error_message = "";

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $usuario = $_POST["email"];
    $password = $_POST["password"];

    // Validar las credenciales (ejemplo básico con sentencias preparadas)
    $sql = "SELECT * FROM `administrador` WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verificar la contraseña (USAR password_verify() en producción!)
        if ($password == $row["Contraseña"]) {
            // Inicio de sesión exitoso
            $_SESSION["usuario_id"] = $row["ID_Administrador"];
            $_SESSION["usuario_nombre"] = $row["Nombre"];

            // Redireccionar al usuario a la página principal
            header("Location: principal.php");
            exit();
        } else {
            // Contraseña incorrecta
            $error_message = "Usuario o contraseña incorrectos. Inténtalo de nuevo.";
        }
    } else {
        // Usuario no encontrado
        $error_message = "Usuario o contraseña incorrectos. Inténtalo de nuevo.";
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloomware-Inicio de sesión</title>
    <link rel="stylesheet" href="css/Estilos.css">
    <link rel="stylesheet" href="css/Font.css">
</head>

<body>
    <center>
        <div class="container">
            <header>
                <img src="img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img">
            </header>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="Formulario">
                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <label for="email" class="formulario__label-txt">Email</label>
                <a href="#" class="icon-user"></a>
                <input type="email" name="email" id="email" class="formulario__input-txt" maxlength="150" required>

                <label for="password" class="formulario__label-txt">Contraseña</label>
                <a href="#" class="icon-key"></a>
                <input type="password" name="password" id="password" class="formulario__input-txt" maxlength="8" required>

                <button type="submit" class="iniciar-sesion">
                    <a>Iniciar Sesión</a>
                </button>
            </form>
        </div>
    </center>
    <script src="/JS/index.js"></script>
    
    <footer>
        <br>
        <br>
        <br>
        <br>
        <p><i>© 2025 Bloomware. Todos los derechos reservados.</i></p>
    </footer>
</body>

</html>