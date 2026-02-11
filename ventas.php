<?php
session_start();
// Seguridad: Solo admin puede ver esto
if (!isset($_SESSION['admin'])) header("Location: login.php");
require 'db.php';

// 1. CALCULAR TOTAL HISTÓRICO DE VENTAS
$total_ingresos = $pdo->query("SELECT SUM(total) FROM ventas")->fetchColumn();

// 2. OBTENER LISTA DE VENTAS (De la más reciente a la más antigua)
$stmt = $pdo->query("SELECT * FROM ventas ORDER BY fecha DESC");
$ventas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas - MecánicaPRO</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos específicos para impresión de recibos */
        @media print {
            body * { visibility: hidden; }
            .printable-area, .printable-area * { visibility: visible; }
            .printable-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    
    <header class="no-print">
        <div class="nav-container">
            <a href="admin.php" class="logo">🔧 Mecánica<span>Admin</span></a>
            <nav class="nav-menu">
                <a href="admin.php">Volver al Inventario</a>
            </nav>
        </div>
    </header>

    <div class="container admin-layout">
        
        <aside class="sidebar no-print">
            <div style="font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: #9ca3af; margin-bottom: 10px; padding-left: 10px;">Menú Principal</div>
            <a href="admin.php">📦 Inventario</a>
            <a href="ventas.php" class="active">📈 Ventas y Facturación</a>
            <a href="clientes.php">👥 Cartera de Clientes</a>
            <a href="logout.php" style="color: #ef4444; margin-top: 20px;">Cerrar Sesión</a>
        </aside>

        <main>
            
            <div style="background: white; padding: 25px; border-radius: 8px; border-left: 5px solid var(--accent); box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h2 style="margin: 0; color: var(--primary);">Reporte de Ingresos</h2>
                    <span style="color: #6b7280;">Historial completo de transacciones</span>
                </div>
                <div style="text-align: right;">
                    <span style="display: block; font-size: 0.9rem; color: #6b7280;">Ventas Totales</span>
                    <span style="font-size: 2rem; font-weight: 800; color: #059669;">
                        $<?php echo number_format($total_ingresos, 2); ?>
                    </span>
                </div>
            </div>

            <?php if(empty($ventas)): ?>
                <div style="text-align: center; padding: 40px; color: #9ca3af;">
                    <i class="fas fa-cash-register fa-3x" style="margin-bottom: 15px;"></i>
                    <p>Aún no se han registrado ventas.</p>
                </div>
            <?php else: ?>
                
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach($ventas as $v): ?>
                        
                        <div id="recibo-<?php echo $v['id']; ?>" class="printable-area" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                            
                            <div style="background: #f9fafb; padding: 15px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: var(--primary); font-size: 1.1rem;">
                                        ORDEN #<?php echo str_pad($v['id'], 5, '0', STR_PAD_LEFT); ?>
                                    </strong>
                                    <div style="font-size: 0.85rem; color: #6b7280; margin-top: 4px;">
                                        <i class="far fa-calendar-alt"></i> <?php echo date("d/m/Y", strtotime($v['fecha'])); ?> &nbsp;|&nbsp; 
                                        <i class="far fa-clock"></i> <?php echo date("H:i", strtotime($v['fecha'])); ?>
                                    </div>
                                </div>
                                
                                <div style="text-align: right;">
                                    <div style="font-size: 0.9rem; color: #6b7280;">Cliente</div>
                                    <div style="font-weight: 700; color: var(--accent);">
                                        <?php echo htmlspecialchars($v['cliente']); ?>
                                    </div>
                                </div>
                            </div>

                            <div style="padding: 20px;">
                                <?php 
                                    // Consultamos los productos de ESTA venta específica
                                    $stmtDetalles = $pdo->prepare("
                                        SELECT d.*, r.nombre, r.codigo 
                                        FROM detalle_venta d 
                                        JOIN repuestos r ON d.producto_id = r.id 
                                        WHERE d.venta_id = ?
                                    ");
                                    $stmtDetalles->execute([$v['id']]);
                                    $items = $stmtDetalles->fetchAll();
                                ?>
                                
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #f3f4f6;">
                                            <th style="text-align: left; padding: 8px; color: #9ca3af; font-size: 0.8rem;">CANT</th>
                                            <th style="text-align: left; padding: 8px; color: #9ca3af; font-size: 0.8rem;">DESCRIPCIÓN</th>
                                            <th style="text-align: right; padding: 8px; color: #9ca3af; font-size: 0.8rem;">P. UNIT</th>
                                            <th style="text-align: right; padding: 8px; color: #9ca3af; font-size: 0.8rem;">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($items as $item): ?>
                                        <tr style="border-bottom: 1px solid #f9fafb;">
                                            <td style="padding: 10px 8px; font-weight: bold;"><?php echo $item['cantidad']; ?></td>
                                            <td style="padding: 10px 8px;">
                                                <?php echo $item['nombre']; ?>
                                                <br><span style="font-size: 0.75rem; color: #9ca3af; background: #f3f4f6; padding: 2px 4px; border-radius: 3px;">SKU: <?php echo $item['codigo']; ?></span>
                                            </td>
                                            <td style="padding: 10px 8px; text-align: right;">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                            <td style="padding: 10px 8px; text-align: right;">$<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                                    
                                    <button onclick="imprimirRecibo('recibo-<?php echo $v['id']; ?>')" class="btn-outline no-print" style="cursor: pointer; padding: 8px 15px; border-radius: 4px; border: 1px solid #d1d5db; background: white; color: #4b5563;">
                                        <i class="fas fa-print"></i> Imprimir Recibo
                                    </button>

                                    <div style="text-align: right;">
                                        <span style="font-size: 0.9rem; color: #6b7280; margin-right: 10px;">Total Pagado:</span>
                                        <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
                                            $<?php echo number_format($v['total'], 2); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <script>
        function imprimirRecibo(divId) {
            var contenido = document.getElementById(divId).innerHTML;
            var contenidoOriginal = document.body.innerHTML;

            // Creamos una vista simple para imprimir
            document.body.innerHTML = `
                <div style="max-width: 800px; margin: 0 auto; font-family: sans-serif;">
                    <h1 style="text-align:center; color:#333;">MECÁNICA PRO</h1>
                    <p style="text-align:center; color:#666;">Comprobante de Venta</p>
                    <hr>
                    ${contenido}
                    <hr>
                    <p style="text-align:center; font-size:0.8rem; margin-top:20px;">Gracias por su compra.</p>
                </div>
            `;

            window.print();

            // Restauramos la página normal
            document.body.innerHTML = contenidoOriginal;
            // Necesario recargar para reactivar eventos JS si los hubiera
            window.location.reload(); 
        }
    </script>
</body>
</html>