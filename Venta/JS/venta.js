// Este archivo JS se llamaría, por ejemplo, venta_cliente.js y se incluiría en venta.php

// Inicializamos el arreglo para los productos que se van añadiendo en el cliente
var productosVenta = []; // Variable global o dentro de un closure/objeto para evitar polución global

// Función para agregar un producto (si se usa un listado dinámico en venta.php)
// Esta función necesitaría que tengas elementos input con IDs como "cantidad_IDDELPRODUCTO"
function agregarProductoDesdeLista(idProducto, nombre, precio) {
    // Supongamos que tienes un input para cantidad específico para este producto en una lista
    // Por ejemplo: <input type="number" id="cantidad_<?php echo $producto['ID_Producto']; ?>" value="1" min="1">
    const cantidadInput = document.getElementById(`cantidad_${idProducto}`); // Usar ` ` para template literals
    if (!cantidadInput) {
        console.error(`Input de cantidad para producto ${idProducto} no encontrado.`);
        return;
    }
    const cantidad = parseInt(cantidadInput.value, 10); // Convertir a número

    if (isNaN(cantidad) || cantidad <= 0) {
        alert("Por favor, ingrese una cantidad válida.");
        return;
    }

    const subtotal = precio * cantidad;

    // Verificar si el producto ya está en productosVenta para actualizar cantidad
    const productoExistente = productosVenta.find(p => p.id_producto === idProducto);

    if (productoExistente) {
        productoExistente.cantidad += cantidad;
        productoExistente.subtotal += subtotal;
    } else {
        productosVenta.push({
            id_producto: idProducto,
            nombre: nombre,
            cantidad: cantidad,
            precio_unitario: precio, // Guardar precio unitario
            subtotal: subtotal
        });
    }

    console.log(productosVenta);
    actualizarVistaCarritoCliente(); // Necesitarías una función para mostrar este carrito en el HTML

    // Usar template literals para el alert y variables JS (sin '$' ni comillas innecesarias)
    alert(`${nombre} agregado con cantidad: ${cantidad}`);
}

// Función para actualizar una sección del HTML que muestre el carrito construido en JS
function actualizarVistaCarritoCliente() {
    const carritoContainer = document.getElementById('carritoClientePreview'); // Necesitas este div en tu HTML
    if (!carritoContainer) return;

    if (productosVenta.length === 0) {
        carritoContainer.innerHTML = "<p>El carrito (cliente) está vacío.</p>";
        return;
    }

    let htmlCarrito = "<ul>";
    let totalGeneralCliente = 0;
    productosVenta.forEach(p => {
        htmlCarrito += `<li>${p.nombre} - Cant: ${p.cantidad} - Subtotal: $${p.subtotal.toFixed(2)}</li>`;
        totalGeneralCliente += p.subtotal;
    });
    htmlCarrito += "</ul>";
    htmlCarrito += `<p><strong>Total (cliente): $${totalGeneralCliente.toFixed(2)}</strong></p>`;
    carritoContainer.innerHTML = htmlCarrito;

    // Llenar el campo oculto si se va a enviar así
    const productosVentaInput = document.getElementById("productos_venta_json_cliente");
    if (productosVentaInput) {
        productosVentaInput.value = JSON.stringify(productosVenta);
    }
}


// Función para registrar la venta cuando se hace clic en el botón
// ESTO ES SI USAS EL CARRITO DEL LADO DEL CLIENTE (productosVenta)
// Y TIENES UN FORMULARIO CON ID "registrarVentaFormCliente" Y UN INPUT OCULTO "productos_venta_json_cliente"

document.addEventListener('DOMContentLoaded', function() {
    const registrarBtnCliente = document.getElementById("registrarVentaBtnCliente");
    if (registrarBtnCliente) {
        registrarBtnCliente.addEventListener("click", function (event) {
            event.preventDefault();

            if (productosVenta.length === 0) {
                alert("Debe agregar al menos un producto al carrito (cliente).");
                return;
            }

            // Asegurarse de que el input oculto esté actualizado
            actualizarVistaCarritoCliente();

            // Enviar el formulario que contiene el input oculto con el JSON
            const formCliente = document.getElementById("registrarVentaFormCliente");
            if (formCliente) {
                formCliente.submit();
            } else {
                console.error("Formulario registrarVentaFormCliente no encontrado.");
            }
        });
    }
});
