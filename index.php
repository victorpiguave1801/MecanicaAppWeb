<?php
session_start();
require 'db.php'; // Asegúrate que sea conexion.php o db.php
$stmt = $pdo->query("SELECT * FROM repuestos WHERE stock > 0 ORDER BY id DESC");
$repuestos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MecánicaPRO - Repuestos</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos del Botón WhatsApp */
        .btn-whatsapp {
            position: fixed; bottom: 20px; right: 20px;
            background-color: #25d366; color: white;
            width: 60px; height: 60px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 1000; transition: transform 0.3s;
            text-decoration: none;
        }
        .btn-whatsapp:hover { transform: scale(1.1); background-color: #128c7e; }

        /* --- ESTILOS DEL MINI CARRITO FLOTANTE --- */
        .cart-wrapper { position: relative; display: inline-block; padding-bottom: 15px; }
        
        .cart-dropdown {
            display: none; /* Oculto por defecto */
            position: absolute;
            top: 100%; right: 0;
            width: 320px;
            background: white;
            border: 1px solid #e5e7eb;
            border-top: 3px solid #dc2626; /* Rojo automotriz */
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-radius: 0 0 8px 8px;
            z-index: 9999;
        }

        /* Mostrar al pasar el mouse */
        .cart-wrapper:hover .cart-dropdown { display: block; }

        .mini-list { max-height: 300px; overflow-y: auto; }
        
        .mini-item {
            display: flex; align-items: center; gap: 10px;
            padding: 12px; border-bottom: 1px solid #f3f4f6;
        }
        .mini-img { width: 40px; height: 40px; object-fit: contain; border: 1px solid #eee; border-radius: 4px; }
        .mini-info { flex-grow: 1; }
        .mini-title { font-size: 0.85rem; font-weight: bold; color: #1f2937; display: block; }
        .mini-price { font-size: 0.8rem; color: #6b7280; }
        
        .btn-trash { color: #ef4444; background: none; border: none; cursor: pointer; font-size: 1rem; }
        .btn-trash:hover { color: #b91c1c; transform: scale(1.1); }

        .mini-footer { padding: 15px; background: #f9fafb; text-align: center; }
        .mini-total-txt { display: block; font-weight: 800; margin-bottom: 10px; font-size: 1.1rem; }
        .empty-cart { padding: 20px; text-align: center; color: #999; }
    </style>
</head>
<body>
    
    <header>
    <div class="nav-container">
        
        <a href="index.php" class="logo">
            <i class="fas fa-wrench" style="font-size: 1.2rem;"></i> 
            MECÁNICA<span>PRO</span>
        </a>
        
        <nav class="main-nav">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                Catálogo
            </a>
    <a href="servicios.php">Servicios</a>  <a href="contacto.php">Contacto</a>
            </nav>

        <div class="user-actions">
            
            <?php if(isset($_SESSION['admin'])): ?>
                <a href="admin.php" class="btn btn-outline" style="color: var(--accent); border-color:var(--accent);">
                    <i class="fas fa-user-shield"></i> Panel Admin
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-staff">
                    <i class="fas fa-user-lock"></i> Staff
                </a>
            <?php endif; ?>

            <div class="cart-wrapper">
                <a href="carrito.php" class="btn btn-primary" style="padding: 8px 15px; display:flex; align-items:center; gap: 8px;">
                    <i class="fas fa-shopping-cart"></i> 
                    <span class="cart-count">0</span>
                </a>

                <div class="cart-dropdown">
                    <div id="mini-cart-list" class="mini-list"></div>
                    <div class="mini-footer">
                        <span class="mini-total-txt">Total: $<span id="mini-total">0.00</span></span>
                        <a href="carrito.php" class="btn btn-primary btn-full">Pagar</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
<div style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.6)), url('imagenes/hero.jpg'); color:white;  text-align: center; padding: 60px 20px;">
        <h1 style="margin: 0; font-size: 2.5rem;">Servicios Profesionales</h1>
        <p style="opacity: 0.8; margin-top: 10px;">Soluciones integrales para el cuidado de tu vehículo.</p>
                <div class="hero-buttons">
                <a href="#catalogo-repuestos" class="btn btn-primary btn-hero icon-btn">
                    <i class="fas fa-shopping-bag"></i> Ver Catálogo
                </a>
                <a href="servicios.php" class="btn btn-outline-white btn-hero icon-btn">
                    <i class="fas fa-calendar-check"></i> Agendar Servicio
                </a>
            </div>
    </div>

    <a href="https://wa.me/593999511682?text=Hola, Me gustaria cotizar el precios de algunas herrmientas y materiales" class="btn-whatsapp" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <main class="container">
        <div style="padding: 30px 0; text-align: center;">
            <h1 style="color: var(--primary);">Catálogo de Piezas</h1>
        </div>

        <div class="grid-products">
            <?php foreach ($repuestos as $r): ?>
            <div class="product-card">
                <div class="product-img-wrap">
                    <?php if($r['imagen']): ?>
                        <img src="<?php echo $r['imagen']; ?>" alt="<?php echo $r['nombre']; ?>">
                    <?php else: ?>
                        <div style="color: #ccc;">Sin Foto</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <span class="sku-label">SKU: <?php echo $r['codigo']; ?></span>
                    <h3 class="product-title"><?php echo $r['nombre']; ?></h3>
                    <div class="product-meta">
                        <span class="price">$<?php echo number_format($r['precio'], 2); ?></span>
                        <span class="stock-indicator"><?php echo $r['stock']; ?> disp.</span>
                    </div>
                    
                    <form class="form-agregar-ajax">
                        <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                        <button type="submit" class="btn btn-secondary btn-full">Agregar</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            actualizarVistaCarrito(); // Cargar al iniciar

            // Escuchar todos los botones de "Agregar"
            document.querySelectorAll('.form-agregar-ajax').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    formData.append('accion', 'agregar');

                    fetch('ajax.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            renderCart(data);
                            // Efecto visual opcional en el botón
                            const btn = this.querySelector('button');
                            const textoOriginal = btn.innerText;
                            btn.innerText = "¡Agregado!";
                            btn.style.background = "#059669";
                            setTimeout(() => { 
                                btn.innerText = textoOriginal; 
                                btn.style.background = ""; 
                            }, 1500);
                        });
                });
            });
        });

        // Eliminar ítem desde el desplegable
        function eliminarItem(id) {
            const formData = new FormData();
            formData.append('accion', 'eliminar');
            formData.append('id', id);
            fetch('ajax.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => renderCart(data));
        }

        // Pedir datos al servidor
        function actualizarVistaCarrito() {
            const formData = new FormData();
            formData.append('accion', 'obtener'); // Acción dummy para leer
            fetch('ajax.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => renderCart(data));
        }

        // Pintar el HTML
        function renderCart(data) {
            document.querySelector('.cart-count').innerText = data.count;
            document.querySelector('#mini-cart-list').innerHTML = data.html;
            document.querySelector('#mini-total').innerText = data.total;
        }
    </script>

</body>
</html>