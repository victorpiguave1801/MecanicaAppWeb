document.addEventListener("DOMContentLoaded", function() {
    // Cargar carrito al iniciar
    actualizarVistaCarrito();

    // INTERCEPTAR FORMULARIOS DE "AGREGAR"
    const formularios = document.querySelectorAll('.form-agregar-ajax');
    formularios.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // ¡ALTO! No recargues la página
            
            const formData = new FormData(this);
            formData.append('accion', 'agregar');

            fetch('ajax_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                actualizarVistaCarrito(); // Refrescar el mini carrito visualmente
                mostrarNotificacion("Producto agregado al carrito");
            });
        });
    });
});

// FUNCIÓN PARA ACTUALIZAR EL HTML DEL CARRITO
function actualizarVistaCarrito() {
    // Llamamos al servidor solo para pedir el HTML actualizado
    const formData = new FormData();
    formData.append('accion', 'obtener'); // Solo queremos leer

    fetch('ajax_cart.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        // 1. Actualizar contador rojo
        document.querySelector('.cart-count').innerText = data.count;
        
        // 2. Actualizar lista desplegable
        document.querySelector('#mini-cart-list').innerHTML = data.html;
        
        // 3. Actualizar total
        document.querySelector('#mini-cart-total').innerText = '$' + data.total;
    });
}

// FUNCIÓN PARA ELIMINAR (Se llama desde el HTML generado en PHP)
function eliminarDelCarrito(id) {
    const formData = new FormData();
    formData.append('accion', 'eliminar');
    formData.append('id', id);

    fetch('ajax_cart.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        actualizarVistaCarrito();
    });
}

// NOTIFICACIÓN FLOTANTE (Toast)
function mostrarNotificacion(mensaje) {
    const div = document.createElement('div');
    div.className = 'toast-notification';
    div.innerText = mensaje;
    document.body.appendChild(div);

    // CSS dinámico para la notificación
    div.style.position = 'fixed';
    div.style.bottom = '20px';
    div.style.left = '50%';
    div.style.transform = 'translateX(-50%)';
    div.style.background = '#333';
    div.style.color = 'white';
    div.style.padding = '10px 20px';
    div.style.borderRadius = '5px';
    div.style.zIndex = '10000';
    div.style.boxShadow = '0 4px 10px rgba(0,0,0,0.3)';
    div.style.animation = 'fadeInOut 3s forwards';

    setTimeout(() => { div.remove(); }, 3000);
}