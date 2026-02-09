<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");
require 'db.php';

// --- LÓGICA: ELIMINAR CLIENTE ---
if (isset($_GET['borrar'])) {
    $id = $_GET['borrar'];
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: clientes.php"); exit;
}

// --- LÓGICA: AGREGAR CLIENTE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];

    $stmt = $pdo->prepare("INSERT INTO clientes (nombre, telefono, email, direccion) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $telefono, $email, $direccion]);
    header("Location: clientes.php"); exit;
}

// Obtener lista de clientes
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cartera de Clientes - MecánicaPRO</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="admin.php" class="logo">🔧 Mecánica<span>Admin</span></a>
            <nav class="nav-menu">
                <a href="admin.php">← Volver al Inventario</a>
            </nav>
        </div>
    </header>

    <div class="container admin-layout">
        
        <aside class="sidebar">
            <div style="font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px; padding-left: 10px;">Menú Principal</div>
            <a href="admin.php">📦 Inventario de Piezas</a>
            <a href="ventas.php">📈 Reporte de Ventas</a>
            <a href="clientes.php" class="active">👥 Cartera de Clientes</a>
        </aside>

        <main>
            <div class="card-panel">
                <h2 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 15px;">Registrar Nuevo Cliente</h2>
                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    
                    <div style="grid-column: 1 / -1;">
                        <label>Nombre Completo / Razón Social</label>
                        <input type="text" name="nombre" required placeholder="Ej. Taller Mecánico Veloz S.A.">
                    </div>

                    <div>
                        <label>Teléfono / WhatsApp</label>
                        <input type="text" name="telefono" placeholder="Ej. 0991234567">
                    </div>

                    <div>
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" placeholder="cliente@email.com">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label>Dirección / Ciudad</label>
                        <input type="text" name="direccion" placeholder="Ej. Av. Principal 123, Quito">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                    </div>
                </form>
            </div>

            <h3>Directorio de Clientes (<?php echo count($clientes); ?>)</h3>
            
            <?php if(empty($clientes)): ?>
                <p style="color: var(--text-muted);">No hay clientes registrados.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Ubicación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $c): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; font-size: 1rem; color: var(--primary);"><?php echo $c['nombre']; ?></div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">ID: <?php echo str_pad($c['id'], 4, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td>
                                    <?php if($c['telefono']): ?>
                                        <div style="margin-bottom: 4px;">📞 <?php echo $c['telefono']; ?></div>
                                    <?php endif; ?>
                                    <?php if($c['email']): ?>
                                        <div style="font-size: 0.85rem; color: var(--accent);">✉️ <?php echo $c['email']; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $c['direccion']; ?></td>
                                <td>
                                    <a href="clientes.php?borrar=<?php echo $c['id']; ?>" class="btn-danger" onclick="return confirm('¿Borrar a <?php echo $c['nombre']; ?> de la base de datos?');">Eliminar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>