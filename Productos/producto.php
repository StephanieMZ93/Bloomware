<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Producto</title>
    <link rel="stylesheet" href="css/Estilos.css">
</head>

<body>
    <div class="container">
        <center>
            <div class="encabezado">
                <header>
                    <img src="img/logo.jpeg.JPG" alt="Bloomware Logo" class="logo--img" />
                </header>
            </div>
        </center>
        <div class="formulario">
            <form action="create.php" method="post" onsubmit="return ValidarFormulario()">
                <fieldset>
                    <legend>Registro de Producto</legend>
                    <label for="" class="formulario__label-txt">ID Producto</label>
                    <br> <input type="Text" class="formulario__input-txt" name:"Nombreproducto" maxlength="25" required> <br>
                    <label for="Nombreproducto" class="formulario__label-txt">Nombre Producto</label>
                    <br> <input type="text" class="formulario__input-txt" name:"Cantidad" maxlength="30" required> <br>
                    <label for="Cantidad" class="formulario__label-txt">Cantidad</label>
                    <br><input type="number" class="formulario__input-txt" name:"Precio" maxlength="50" required> <br>
                    <label for="Precio" class="formulario__label-txt">Precio</label>
                    <br> <input type="number" class="formulario__input-txt" name:"Categoria" maxlength="10" required> <br>
                    <label for="Categoria" class="formulario__label-txt">Categoria</label>
                    <br> <input type="text" class="formulario__input-txt" name:"Lote" maxlength="8" required> <br>
                    <label for="Lote" class="formulario__label-txt">Lote</label>
                    <br><br> <input type="text" class="formulario__input-txt" name:"FechaVencimiento" maxlength="8">
                    <label for="FechaVencimiento" class="formulario__label-txt">Fecha Vencimiento</label>
                    <br><br> <input type="date" class="formulario__input-txt" maxlength="8">
                </fieldset>
                <button class="btn-registro" type="button">Registrar</button>
        </div>
        </form>

        <h2> Productos Existentes</h2>
        <table>
            <tr>
                <th>ID Producto</th>
                <th>Nombre Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Categoria</th>
                <th>Lote</th>
                <th>Fecha Vencimiento</th>
            </tr>
            <?php include 'read.php'; ?>
        </table>
    </div>
</body>

</html>