//Validar el formulario antes de enviar 
function validarFormulario() {
    const nombreproducto = document.querySelector('input[name="NombreProducto"]').value.trim();
    const cantidad = document.querySelector('input[name="Cantidad"]').value.trim();
    const precio = document.querySelector('input[name="Precio"]').value.trim();
    const categoria = document.querySelector('input[name="Categoria"]').value.trim();
    const Lote = document.querySelector('input[name="Lote"]').value.trim();
    const Fecha_Vencimiento = document.querySelector('input[name="FechaVencimiento"]').value.trim();

    if (!nombreproducto || !cantidad || !precio || !categoria || !Lote || !Fecha_Vencimiento){
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


