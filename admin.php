<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");
require 'db.php';

// --- Lógica: Eliminar ---
if (isset($_GET['borrar'])) {
    $id = $_GET['borrar'];
    // Intentamos borrar la imagen física
    $stmt = $pdo->prepare("SELECT imagen FROM repuestos WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if($img && file_exists($img)) unlink($img);
    // Borramos el registro
    $pdo->prepare("DELETE FROM repuestos WHERE id = ?")->execute([$id]);
    header("Location: admin.php"); exit;
}

// --- Lógica: Crear Producto (SKU Automático) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock  = $_POST['stock'];
    // Generar SKU: Prefijo "MEC-" + timestamp corto + 2 random
    $codigo = 'MEC-' . substr(time(), -4) . rand(10,99); 
    
    $ruta_img = '';
    if (!empty($_FILES['imagen']['name'])) {
        // Asegurar que el nombre de archivo sea seguro
        $nombre_archivo = time() . "_" . basename($_FILES['imagen']['name']);
        $ruta_img = "img/" . $nombre_archivo;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_img);
    }

    $stmt = $pdo->prepare("INSERT INTO repuestos (codigo, nombre, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$codigo, $nombre, $precio, $stock, $ruta_img]);
}

$repuestos = $pdo->query("SELECT * FROM repuestos ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - MecánicaPRO</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="admin.php" class="logo">🔧 Mecánica<span>Admin</span></a>
            <nav class="nav-menu">
                <a href="index.php" target="_blank">Ver Tienda ↗</a>
                <span style="color: var(--border);">|</span>
                <a href="logout.php" style="color: var(--accent);">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <div class="container admin-layout">
        
        <aside class="sidebar">
            <div style="font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px; padding-left: 10px;">Menú Principal</div>
            <a href="admin.php" class="active">📦 Inventario de Piezas</a>
            <a href="ventas.php">📈 Reporte de Ventas</a>
            <a href="clientes.php">👥 Cartera de Clientes</a>
            <a href="ventas.php">📈 Ventas</a>
    <a href="servicios_admin.php">🛠️ Servicios</a>
        </aside>

        <main>
            <div class="card-panel">
                <h2 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 15px;">Alta de Nuevo Repuesto</h2>
                <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div style="grid-column: 1 / -1;">
                        <label>Descripción de la Pieza</label>
                        <input type="text" name="nombre" required placeholder="Ej. Kit de Embrague LUK">
                    </div>
                    
                    <div>
                        <label>Precio Unitario ($)</label>
                        <input type="number" step="0.01" name="precio" required>
                    </div>
                    <div>
                        <label>Stock Inicial</label>
                        <input type="number" name="stock" required>
                    </div>
                     <div style="display: flex; align-items: flex-end;">
                         <div style="font-size: 0.8rem; color: var(--text-muted); background: var(--bg-light); padding: 10px; border-radius: var(--radius); width: 100%; text-align: center;">
                             🤖 SKU se generará automáticamente.
                         </div>
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label>Fotografía del Producto (Opcional)</label>
                        <input type="file" name="imagen" accept="image/*" style="padding: 8px;">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <button type="submit" class="btn btn-primary">Guardar en Inventario</button>
                    </div>
                </form>
            </div>

            <h3>Inventario Actual (<?php echo count($repuestos); ?> ítems)</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">Img</th>
                            <th>SKU / Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repuestos as $r): ?>
                        <tr>
                            <td>
                                <?php if($r['imagen']): ?>
                                    <img src="<?php echo $r['imagen']; ?>" width="50" height="50" style="object-fit: contain; border: 1px solid var(--border); border-radius: 4px;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600;"><?php echo $r['nombre']; ?></div>
                                <span class="sku-label" style="font-size: 0.7rem;"><?php echo $r['codigo']; ?></span>
                            </td>
                            <td style="font-weight: 600;">$<?php echo number_format($r['precio'], 2); ?></td>
                            <td>
                                <span style="font-weight: 500; color: <?php echo $r['stock'] < 5 ? '#dc2626' : 'inherit'; ?>">
                                    <?php echo $r['stock']; ?> unid.
                                </span>
                            </td>
                            <td>
                                <a href="admin.php?borrar=<?php echo $r['id']; ?>" class="btn-danger" onclick="return confirm('¿Confirmar eliminación de este repuesto?');">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>