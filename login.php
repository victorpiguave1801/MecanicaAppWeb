<?php
session_start();
require 'db.php';

// Si ya está logueado, redirigir al admin
if (isset($_SESSION['admin'])) { header("Location: admin.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $pass = $_POST['password'];

    // Verificación directa (admin/admin)
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = ?");
    $stmt->execute([$usuario, $pass]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso al Sistema - MecánicaPRO</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="login-wrapper">
    
    <div class="login-box">
        <div style="text-align: center; margin-bottom: 30px;">
            <div class="logo" style="justify-content: center; font-size: 1.5rem;">🔧 Mecánica<span>PRO</span></div>
            <p style="color: var(--text-muted); margin-top: 10px;">Acceso exclusivo para personal autorizado.</p>
        </div>

        <?php if(isset($error)): ?>
            <div style="background: #fef2f2; color: #dc2626; padding: 10px; border-radius: var(--radius); font-size: 0.9rem; margin-bottom: 20px; text-align: center;">
                ⚠️ <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label for="usuario">ID de Usuario</label>
                <input type="text" id="usuario" name="usuario" required placeholder="Ej. admin" autofocus>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label for="password">Contraseña de Acceso</label>
                <input type="password" id="password" name="password" required placeholder="••••••">
            </div>
            
            <button type="submit" class="btn btn-primary btn-full" style="font-size: 1rem; padding: 12px;">Iniciar Sesión Segura</button>
        </form>
        
        <div style="text-align: center; margin-top: 25px; font-size: 0.9rem;">
            <a href="index.php" style="color: var(--text-muted);">← Volver al Catálogo Público</a>
        </div>
    </div>

</body>
</html>