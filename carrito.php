<?php
session_start();
require 'db.php';


if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];


if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
    $id = $_POST['id'];
   
    $_SESSION['carrito'][$id] = isset($_SESSION['carrito'][$id]) ? $_SESSION['carrito'][$id] + 1 : 1;
    header("Location: carrito.php"); exit;
}
if (isset($_GET['vaciar'])) {
    unset($_SESSION['carrito']);
    header("Location: carrito.php"); exit;
}

$mensaje_orden = "";
if (isset($_POST['procesar_orden'])) {
    $cliente = $_POST['cliente_nombre'];
    $total_orden = $_POST['total_orden_hidden'];
    
    if(!empty($_SESSION['carrito'])) {
        try {
            // ¡IMPORTANTE! Iniciar transacción SQL para asegurar integridad
            $pdo->beginTransaction();
            
            // A. Insertar en tabla VENTAS (Cabecera)
            $stmtVenta = $pdo->prepare("INSERT INTO ventas (cliente, total) VALUES (?, ?)");
            $stmtVenta->execute([$cliente, $total_orden]);
            $id_venta_generada = $pdo->lastInsertId();
            
            // B. Insertar DETALLES y RESTAR STOCK por cada producto
            foreach($_SESSION['carrito'] as $prod_id => $cantidad_comprada) {
                // Obtener precio actual de la base de datos (más seguro)
                $precio_actual = $pdo->query("SELECT precio FROM repuestos WHERE id = $prod_id")->fetchColumn();
                
                // Insertar detalle
                $stmtDetalle = $pdo->prepare("INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
                $stmtDetalle->execute([$id_venta_generada, $prod_id, $cantidad_comprada, $precio_actual]);
                
                // Restar del inventario
                $stmtStock = $pdo->prepare("UPDATE repuestos SET stock = stock - ? WHERE id = ?");
                $stmtStock->execute([$cantidad_comprada, $prod_id]);
            }
            
            // Si todo salió bien, confirmamos los cambios
            $pdo->commit();
            
            // Limpiamos carrito y mostramos éxito
            unset($_SESSION['carrito']);
            $mensaje_orden = "La Orden de Servicio #$id_venta_generada se ha registrado correctamente.";
            
        } catch (Exception $e) {
            // Si algo falló, deshacemos todo
            $pdo->rollBack();
            $error_orden = "Error al procesar: " . $e->getMessage();
        }
    }
}
// ELIMINAR UN SOLO ÍTEM (Nuevo)
if (isset($_GET['eliminar_id'])) {
    $id_borrar = $_GET['eliminar_id'];
    if (isset($_SESSION['carrito'][$id_borrar])) {
        unset($_SESSION['carrito'][$id_borrar]);
    }
    // Redirigir a la página desde donde vino (para que no recargue carrito.php)
    $volver = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header("Location: $volver");
    exit;
}

// --- Preparar vista del carrito ---
$items_carrito = [];
$total_general = 0;
if (!empty($_SESSION['carrito'])) {
    // Obtenemos IDs de la sesión: keys( [ID => Cantidad, ID => Cantidad] )
    $ids_sql = implode(',', array_keys($_SESSION['carrito']));
    $stmt = $pdo->query("SELECT * FROM repuestos WHERE id IN ($ids_sql)");
    $productos_db = $stmt->fetchAll();

    foreach($productos_db as $prod) {
        $prod['cantidad_en_carrito'] = $_SESSION['carrito'][$prod['id']];
        $prod['subtotal'] = $prod['precio'] * $prod['cantidad_en_carrito'];
        $total_general += $prod['subtotal'];
        $items_carrito[] = $prod;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Pedido - MecánicaPRO</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">🔧 Mecánica<span>PRO</span></a>
            <nav class="nav-menu"><a href="index.php">← Seguir Comprando</a></nav>
        </div>
    </header>

    <main class="container">
        <h1 style="margin-bottom: 30px;">Resumen de Pedido</h1>

        <?php if($mensaje_orden): ?>
            <div class="card-panel" style="text-align: center; border-color: #059669;">
                <h2 style="color: #059669; margin-top: 0;">✅ Pedido Completado</h2>
                <p><?php echo $mensaje_orden; ?></p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Volver al Catálogo</a>
            </div>
        <?php elseif(isset($error_orden)): ?>
             <div class="card-panel" style="border-color: #dc2626; color: #dc2626;">❌ <?php echo $error_orden; ?></div>
        <?php elseif(empty($items_carrito)): ?>
            <div class="card-panel" style="text-align: center; padding: 50px;">
                <p style="color: var(--text-muted); font-size: 1.1rem;">Tu carrito de compras está vacío.</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Ir a buscar repuestos</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                
                <div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Producto / SKU</th>
                                    <th>Precio Unit.</th>
                                    <th>Cant.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items_carrito as $item): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo $item['nombre']; ?></div>
                                        <span class="sku-label"><?php echo $item['codigo']; ?></span>
                                    </td>
                                    <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                    <td style="font-weight: 600;"><?php echo $item['cantidad_en_carrito']; ?></td>
                                    <td style="font-weight: 700; color: var(--primary);">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 20px; text-align: right;">
                         <a href="carrito.php?vaciar=true" class="btn-danger" style="text-decoration: none;">Vaciar todo el carrito</a>
                    </div>
                </div>

                <div class="cart-summary">
                    <h3 style="margin-top: 0;">Totales del Pedido</h3>
                    <div class="summary-row">
                        <span>Subtotal Artículos:</span>
                        <span>$<?php echo number_format($total_general, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Impuestos estimados:</span>
                        <span>$0.00</span>
                    </div>
                    <div class="summary-total summary-row">
                        <span>Total a Pagar:</span>
                        <span>$<?php echo number_format($total_general, 2); ?></span>
                    </div>

                    <form method="POST" style="margin-top: 25px;">
                        <label for="cliente">Datos del Cliente / Taller</label>
                        <input type="text" id="cliente" name="cliente_nombre" required placeholder="Nombre completo o razón social" style="border-color: var(--accent);">
                        
                        <input type="hidden" name="total_orden_hidden" value="<?php echo $total_general; ?>">
                        <input type="hidden" name="procesar_orden" value="1">
                        
                        <button type="submit" class="btn btn-primary btn-full" style="font-size: 1.1rem; padding: 15px;">Confirmar Pedido</button>
                    </form>
                    <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; margin-top: 15px;">
                        Al confirmar, se generará la orden y se descontará el stock.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>