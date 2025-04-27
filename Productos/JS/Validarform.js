//Validar el formulario antes de enviar 
function validarFormulario() {
    const ID_Producto = document.querySelector('input[name="ID_Producto"]').value.trim();
    const Nombre_Producto = document.querySelector('input[name="Nombre_Producto"]').value.trim();
    const Cantidad = document.querySelector('input[name="Cantidad"]').value.trim();
    const Precio = document.querySelector('input[name="Precio"]').value.trim();
    const Categoria = document.querySelector('input[name="Categoria"]').value.trim();
    const Lote = document.querySelector('input[name="Lote"]').value.trim();
    const Fecha_Vencimiento = document.querySelector('input[name="FechaVencimiento"]').value.trim();

    if (!nombreproducto || !cantidad || !precio || !categoria || !Lote || !Fecha_Vencimiento) {
        alert("Por favor, completa todos los campos.");
        return false;
    }

    if (isNaN(cantidad) || cantidad < 0) {
        alert("La cantidad del producto se guardo exitosamente");
        return false;
    }

    if (isNaN(precio) || precio <= 0) {
        alert("El precio del producto debe ser positivo");
        return false
    }

    if (new Date(Fecha_Vencimiento)) {
        alert("La fecha de vencimiento se guardo exitosamente");
        return false;
    }
    return true;
}

// Confirmación antes de eliminar un producto
function eliminarProducto(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
        window.location.href = `delete.php?delete_id=${id}`;
    }
}

// Mostrar formulario de actualizacion de productos cargados

function mostrarFormularioActualizar(ID_Producto, NombreProducto, Cantidad, Precio, Categoria, Lote, Fecha_Vencimiento) {
    const FormActualizar = document.createElement('div');
    FormActualizar.innerHTML = `
        <form action="update.php" method="POST">
            <input type="hidden" name="update_id" value="${ID_Producto}">
            <input type="text" name="update_nombre" value="${NombreProducto}" required>
            <input type="text" name="update_apellido" value="${Cantidad}" required>
            <input type="text" name="update_telefono" value="${Precio}" required>
            <input type="text" name="update_habitacion" value="${Categoria}" required>
            <input type="date" name="update_fecha_entrada" value="${Lote}" required>
            <input type="date" name="update_fecha_salida" value="${Fecha_vencimiento}" required>
            <button type="submit" name="update">Actualizar</button>
            <button type="button" onclick="cerrarFormularioActualizar()">Cancelar</button>
        </form>
        `; 

        document.body.appendChild(FormActualizar);
}

// Cerrar el formulario de actualizacion 
function cerrarFormularioActualizar() {
    const formActualizar = document.querySelector('form[action="update.php"]');
    if (formActualizar) {
        formActualizar.remove();
    }
}

function abrirInicio(){
    window.location.href = "productos/producto.php";
}