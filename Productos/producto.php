<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Producto</title>
    <link rel="stylesheet" href="css/Estilos.css"> 
</head>
<body>

    <!--SECCIÓN DEL FORMULARIO  -->
    <div class="container form-container"> 
        <div class="encabezado">
            <header>
                
                <img src="../img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" /> 
            </header>
        </div>

        <div class="formulario">
            <form action="create.php" method="post">
                <fieldset>
                    <legend>Registro de Producto</legend>
                    <label for="id_producto" >ID Producto</label>
                    <input type="text" id="id_producto" name="ID_Producto" maxlength="25" required>
                    <label for="nombre_producto" >Nombre Producto</label>
                    <input type="text" id="nombre_producto" name="Nombre_producto" maxlength="30" required>
                    <label for="cantidad" >Cantidad</label>
                    <input type="number" id="cantidad" name="Cantidad" required>
                    <label for="precio" >Precio</label>
                    <input type="number" id="precio" name="Precio" step="0.01" required>
                    <label for="categoria" >Categoría</label>
                    <input type="text" id="categoria" name="Categoria" maxlength="50" required>
                    <label for="lote" >Lote</label>
                    <input type="text" id="lote" name="Lote" maxlength="8" required>
                    <label for="fecha_vencimiento" >Fecha Vencimiento</label>
                    <input type="date" id="fecha_vencimiento" name="FechaVencimiento"> 
                </fieldset>
                <button class="btn-registro" type="submit">Registrar</button>
            </form>
        </div>
    </div> 


    <!-- SECCIÓN DE LA TABLA -->
    <div class="container table-container"> 
        <h2>Productos Existentes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Nombre Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Lote</th>
                    <th>Fecha Vencimiento</th>
                    <th>Acciones</th> <!-- Añade encabezado para botones -->
                </tr>
            </thead>
            <tbody>
                <?php include 'read.php';  ?>
            </tbody>
        </table>
    </div> 

</body>
</html>