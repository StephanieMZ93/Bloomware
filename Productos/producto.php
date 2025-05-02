<?php
session_start(); // Iniciar sesión si necesitas verificar login aquí también
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php"); // Redirige si no hay sesión
    exit();
}

// --- Lógica para modo Actualizar ---
$modo_edicion = false; // Variable para saber si estamos editando o creando
$producto_a_editar = [ // Array para almacenar datos del producto a editar 
    'ID_Producto' => '',
    'Nombre_Producto' => '',
    'Cantidad' => '',
    'Precio' => '',
    'Categoria' => '',
    'Lote' => '',
    'Fecha_Vencimiento' => ''
];

// DETECTAR SI ESTAMOS EN MODO EDICIÓN: Verificar si recibimos 'update_id' por GET
if (isset($_GET['update_id']) && filter_var($_GET['update_id'], FILTER_VALIDATE_INT)) {
    $modo_edicion = true; // Establecer que estamos editando
    $producto_a_editar['ID_Producto'] = $_GET['update_id']; // Guardar el ID

    // OBTENER LOS DEMÁS DATOS
    $producto_a_editar['Nombre_Producto'] = $_GET['nombre'] ?? '';
    $producto_a_editar['Cantidad'] = $_GET['cantidad'] ?? '';
    $producto_a_editar['Precio'] = $_GET['precio'] ?? '';
    $producto_a_editar['Categoria'] = $_GET['categoria'] ?? '';
    $producto_a_editar['Lote'] = $_GET['lote'] ?? '';
    $producto_a_editar['Fecha_Vencimiento'] = $_GET['fecha'] ?? ''; // Recordar que usamos 'fecha' en el JS
} 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Cambia el título dinámicamente -->
    <title><?php echo $modo_edicion ? 'Actualizar' : 'Registro de'; ?> Producto - Bloomware</title>
    <link rel="stylesheet" href="css/Estilos.css">
</head>
<body>
    <div class="page-container form-page-container">

        <header class="form-header">
            <!-- El enlace "Regresar" apunta a existentes.php si estamos editando -->
            <a href="<?php echo $modo_edicion ? 'existentes.php' : '../principal.php'; ?>" class="regresar-link">← Regresar</a>
            <img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
            <p class="header-subtitle">Gestiona tu inventario</p>
        </header>

        <div class="message-container">
            <?php if (isset($_GET['mensaje'])) : ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
        </div>

        <div class="formulario">
            <!-- La acción del formulario apunta a update.php si estamos editando -->
            <form action="<?php echo $modo_edicion ? 'update.php' : 'create.php'; ?>" method="post" id="productForm">
                <fieldset>
                    <!-- La leyenda cambia dinámicamente -->
                    <legend><?php echo $modo_edicion ? 'Actualizar' : 'Registro de'; ?> Producto</legend>

                    <?php if ($modo_edicion): ?>
                        <!-- Campo oculto que envía el ID a update.php -->
                        <input type="hidden" name="update_ID_Producto" value="<?php echo htmlspecialchars($producto_a_editar['ID_Producto']); ?>">
                        <!-- Mostrar el ID al usuario, pero deshabilitado -->
                        <div class="form-group">
                            <label for="id_producto_display">ID Producto</label>
                            <input type="text" id="id_producto_display" value="<?php echo htmlspecialchars($producto_a_editar['ID_Producto']); ?>" disabled style="background-color: #eee; cursor: not-allowed;"> <!-- Estilo para indicar deshabilitado -->
                        </div>
                    <?php endif; ?>

                    <!-- PRE-RELLENAR CAMPOS: Usar el array $producto_a_editar en el atributo 'value' -->
                    <div class="form-group">
                        <label for="nombre_producto">Nombre Producto</label>
                        <input type="text" id="nombre_producto" name="Nombre_Producto" maxlength="30" required value="<?php echo htmlspecialchars($producto_a_editar['Nombre_Producto']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" id="cantidad" name="Cantidad" required min="0" value="<?php echo htmlspecialchars($producto_a_editar['Cantidad']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="number" id="precio" name="Precio" step="0.01" required min="0" value="<?php echo htmlspecialchars($producto_a_editar['Precio']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <input type="text" id="categoria" name="Categoria" maxlength="50" required value="<?php echo htmlspecialchars($producto_a_editar['Categoria']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="lote">Lote</label>
                        <input type="text" id="lote" name="Lote" maxlength="8" required value="<?php echo htmlspecialchars($producto_a_editar['Lote']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="fecha_vencimiento">Fecha Vencimiento</label>
                        <input type="date" id="fecha_vencimiento" name="FechaVencimiento" value="<?php echo htmlspecialchars($producto_a_editar['Fecha_Vencimiento']); ?>">
                    </div>

                </fieldset>
                <!-- CAMBIAR TEXTO DEL BOTÓN -->
                <button class="btn-registro" type="submit"><?php echo $modo_edicion ? 'Actualizar Producto' : 'Registrar'; ?></button>
            </form>
        </div>

    </div> 

</body>
</html>