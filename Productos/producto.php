<?php
// Archivo: Productos/producto.php
session_start();
if (!isset($_SESSION["usuario_id_usuario"])) {
    header("Location: ../index.php");
    exit();
}

// Incluir conexión (un nivel arriba, luego carpeta BD)
require_once(__DIR__ . '/../BD/Conexion.php');

// --- Lógica para modo Actualizar ---
$modo_edicion = false;
$producto_a_editar = [
    'ID_Producto' => '', 'Nombre_Producto' => '', 'Cantidad' => '',
    'Precio' => '', 'Categoria' => '', 'Lote' => '', 'Fecha_Vencimiento' => ''
];
$categoria_seleccionada_texto = "Seleccionar Categoría...";

if (isset($_GET['update_id']) && filter_var($_GET['update_id'], FILTER_VALIDATE_INT)) {
    $modo_edicion = true;
    $producto_a_editar['ID_Producto'] = $_GET['update_id'];
    $producto_a_editar['Nombre_Producto'] = $_GET['nombre'] ?? '';
    $producto_a_editar['Cantidad'] = $_GET['cantidad'] ?? '';
    $producto_a_editar['Precio'] = $_GET['precio'] ?? '';
    $producto_a_editar['Categoria'] = $_GET['categoria'] ?? '';
    $producto_a_editar['Lote'] = $_GET['lote'] ?? '';
    $producto_a_editar['Fecha_Vencimiento'] = $_GET['fecha'] ?? '';

    if (!empty($producto_a_editar['Categoria'])) {
        $categoria_seleccionada_texto = htmlspecialchars($producto_a_editar['Categoria']);
    }
}

// Definir las categorías disponibles
$categorias_disponibles = [
    "Maquillaje Facial", "Maquillaje de Ojos",
    "Maquillaje de Cejas", "Maquillaje de Labios",
    "Cuidado de la Piel", "Accesorios"
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modo_edicion ? 'Actualizar' : 'Registro de'; ?> Producto - Bloomware</title>
    <link rel="stylesheet" href="css/Estilos.css"> <!-- CSS para el módulo Productos -->
</head>
<body>
    <div class="page-container form-page-container">

        <header class="form-header">
            <!-- Enlace de regreso apunta a existentes.php si estamos editando -->
            <a href="<?php echo $modo_edicion ? 'read.php' : '../principal.php'; ?>" class="regresar-link">← Regresar</a>
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
            <form action="<?php echo $modo_edicion ? 'update.php' : 'create.php'; ?>" method="post" id="productForm">
                <fieldset>
                    <legend><?php echo $modo_edicion ? 'Actualizar' : 'Registro de'; ?> Producto</legend>

                    <?php if ($modo_edicion): ?>
                        <input type="hidden" name="update_ID_Producto" value="<?php echo htmlspecialchars($producto_a_editar['ID_Producto']); ?>">
                        <div class="form-group">
                            <label for="id_producto_display">ID Producto</label>
                            <input type="text" id="id_producto_display" value="<?php echo htmlspecialchars($producto_a_editar['ID_Producto']); ?>" disabled style="background-color: #eee; cursor: not-allowed;">
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="nombre_producto">Nombre Producto</label>
                        <input type="text" id="nombre_producto" name="Nombre_Producto" maxlength="45" required value="<?php echo htmlspecialchars($producto_a_editar['Nombre_Producto']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" id="cantidad" name="Cantidad" required min="0" value="<?php echo htmlspecialchars($producto_a_editar['Cantidad']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="number" id="precio" name="Precio" step="1" required min="0" value="<?php echo htmlspecialchars($producto_a_editar['Precio']); ?>"> <!-- step="1" si es decimal(10,0) o 0.01 si es decimal(10,2) -->
                    </div>

                    <!-- Campo Categoría con Desplegable -->
                    <div class="form-group categoria-dropdown-group">
                        <label>Categoría</label>
                        <div class="category-selector-container">
                            <button type="button" id="categoria_btn" class="btn-category-select">
                                <?php echo $categoria_seleccionada_texto; ?>
                            </button>
                            <input type="hidden" id="categoria_hidden" name="Categoria" value="<?php echo htmlspecialchars($producto_a_editar['Categoria']); ?>" required>
                            <div id="category_dropdown_list" class="category-dropdown">
                                <ul>
                                    <?php foreach ($categorias_disponibles as $cat): ?>
                                        <li data-value="<?php echo htmlspecialchars($cat); ?>" class="<?php echo ($producto_a_editar['Categoria'] == $cat && $modo_edicion) ? 'selected' : ''; ?>">
                                            <?php echo htmlspecialchars($cat); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lote">Lote</label>
                        <input type="text" id="lote" name="Lote" maxlength="20" value="<?php echo htmlspecialchars($producto_a_editar['Lote']); ?>"> 
                    </div>
                    <div class="form-group">
                        <label for="fecha_vencimiento">Fecha Vencimiento</label>
                        <input type="date" id="fecha_vencimiento" name="FechaVencimiento" value="<?php echo htmlspecialchars($producto_a_editar['Fecha_Vencimiento']); ?>" required>
                    </div>
                </fieldset>
                <button class="btn-registro" type="submit"><?php echo $modo_edicion ? 'Actualizar Producto' : 'Registrar'; ?></button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoriaBtn = document.getElementById('categoria_btn');
            const categoriaHiddenInput = document.getElementById('categoria_hidden');
            const categoryDropdownList = document.getElementById('category_dropdown_list');
            const categoryItems = categoryDropdownList.querySelectorAll('ul li');

            if (categoriaBtn && categoriaHiddenInput && categoryDropdownList) {
                categoriaBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    categoryDropdownList.classList.toggle('open');
                    categoriaBtn.classList.toggle('active');
                });
                categoryItems.forEach(item => {
                    item.addEventListener('click', function() {
                        const selectedValue = this.getAttribute('data-value');
                        const selectedText = this.textContent.trim();
                        categoriaHiddenInput.value = selectedValue;
                        categoriaBtn.textContent = selectedText;
                        categoryItems.forEach(i => i.classList.remove('selected'));
                        this.classList.add('selected');
                        categoryDropdownList.classList.remove('open');
                        categoriaBtn.classList.remove('active');
                    });
                });
                document.addEventListener('click', function(event) {
                    if (!categoriaBtn.contains(event.target) && !categoryDropdownList.contains(event.target)) {
                        if (categoryDropdownList.classList.contains('open')) {
                            categoryDropdownList.classList.remove('open');
                            categoriaBtn.classList.remove('active');
                        }
                    }
                });
            }
        });

        // Script para limpiar formulario después de registro exitoso
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('mensaje') && !urlParams.has('update_id') && !<?php echo json_encode($modo_edicion); ?>) { // Solo si es creación y hay mensaje
            const form = document.getElementById('productForm');
            if (form) {
                form.reset();
                 // Resetear el texto del botón de categoría
                const categoriaBtn = document.getElementById('categoria_btn');
                if(categoriaBtn) categoriaBtn.textContent = "Seleccionar Categoría...";
            }
            // Opcional: limpiar URL
            // window.history.replaceState({}, document.title, window.location.pathname + window.location.hash); // Mantiene el hash si lo hay
        }
    </script>
    <?php if (isset($conn)) { mysqli_close($conn); } ?>
</body>
</html>