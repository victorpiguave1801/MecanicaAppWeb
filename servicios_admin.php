<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");
require 'db.php';

// --- ELIMINAR SERVICIO ---
if (isset($_GET['borrar'])) {
    $pdo->prepare("DELETE FROM servicios WHERE id = ?")->execute([$_GET['borrar']]);
    header("Location: servicios_admin.php"); exit;
}

// --- AGREGAR SERVICIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $desc = $_POST['descripcion'];
    $icono = $_POST['icono'];
    
    $stmt = $pdo->prepare("INSERT INTO servicios (titulo, descripcion, icono) VALUES (?, ?, ?)");
    $stmt->execute([$titulo, $desc, $icono]);
    header("Location: servicios_admin.php"); exit;
}

$servicios = $pdo->query("SELECT * FROM servicios ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Servicios</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="admin.php" class="logo">🔧 Mecánica<span>Admin</span></a>
            <nav class="nav-menu"><a href="admin.php">Volver al Panel</a></nav>
        </div>
    </header>

    <div class="container admin-layout">
        
        <aside class="sidebar">
            <div style="font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: #999; margin-bottom: 10px;">Menú</div>
            <a href="admin.php">📦 Inventario</a>
            <a href="ventas.php">📈 Ventas</a>
            <a href="servicios_admin.php" class="active">🛠️ Servicios</a>
            <a href="clientes.php">👥 Clientes</a>
        </aside>

        <main>
            <div class="card" style="padding: 25px; border-left: 5px solid var(--accent); margin-bottom: 30px;">
                <h2 style="margin-top: 0;">Publicar Nuevo Servicio</h2>
                
                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <div style="grid-column: 1 / -1;">
                        <label>Título del Servicio</label>
                        <input type="text" name="titulo" required placeholder="Ej. Cambio de Aceite">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label>Descripción (Corta)</label>
                        <textarea name="descripcion" rows="3" required placeholder="Ej. Incluye filtro y revisión de niveles..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                    </div>

                    <div>
                        <label>Selecciona un Icono</label>
                        <select name="icono" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="fas fa-oil-can">🛢️ Aceite / Fluidos</option>
                            <option value="fas fa-car-crash">🛑 Frenos / Suspensión</option>
                            <option value="fas fa-cogs">⚙️ Motor / Mecánica</option>
                            <option value="fas fa-bolt">⚡ Eléctrico / Batería</option>
                            <option value="fas fa-laptop-medical">💻 Scanner / Diagnóstico</option>
                            <option value="fas fa-spray-can">🎨 Latonería / Pintura</option>
                            <option value="fas fa-snowflake">❄️ Aire Acondicionado</option>
                            <option value="fas fa-wrench">🔧 Mantenimiento General</option>
                            <option value="fas fa-tachometer-alt">🏎️ Tuning / Performance</option>
                        </select>
                    </div>

                    <div style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary">Guardar Servicio</button>
                    </div>
                </form>
            </div>

            <h3>Servicios Activos en la Web</h3>
            
            <?php if(empty($servicios)): ?>
                <p>No hay servicios registrados.</p>
            <?php else: ?>
                <div class="grid">
                    <?php foreach($servicios as $s): ?>
                    <div class="card" style="text-align: center; padding: 20px;">
                        <div style="font-size: 2rem; color: var(--accent); margin-bottom: 10px;">
                            <i class="<?php echo $s['icono']; ?>"></i>
                        </div>
                        <h3 style="margin: 10px 0;"><?php echo $s['titulo']; ?></h3>
                        <p style="color: #666; font-size: 0.9rem;"><?php echo $s['descripcion']; ?></p>
                        <a href="servicios_admin.php?borrar=<?php echo $s['id']; ?>" class="btn-trash" onclick="return confirm('¿Eliminar servicio?');">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>