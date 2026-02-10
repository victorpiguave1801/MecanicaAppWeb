<?php
session_start();
require 'db.php';

$alerta = "";

// Lógica de Guardado (Nombre, Teléfono, Email, Dirección)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];

    if (!empty($nombre) && !empty($telefono)) {
        $sql = "INSERT INTO clientes (nombre, telefono, email, direccion) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nombre, $telefono, $email, $direccion])) {
            $alerta = '<div class="card-panel" style="border-color: green; color: green; text-align: center; font-weight:bold;">✅ Datos enviados correctamente.</div>';
        } else {
            $alerta = '<div class="card-panel" style="border-color: red; color: red;">❌ Error al guardar datos.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto - MecánicaPRO</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Estilo del Botón Flotante de WhatsApp */
        .btn-whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25d366;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 1000;
            transition: transform 0.3s;
            text-decoration: none;
        }
        .btn-whatsapp-float:hover {
            transform: scale(1.1);
            background-color: #128c7e;
        }
    </style>
</head>
<body>
    
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">🔧 Mecánica<span>PRO</span></a>
            <nav class="nav-menu">
                <a href="index.php">← Volver al Catálogo</a>
            </nav>
        </div>
    </header>

    <a href="https://wa.me/593999511682?text=Hola,%20quisiera%20más%20información." class="btn-whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <div class="container" style="max-width: 1000px; margin-top: 40px;">
        
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 style="color: var(--primary);">Déjanos tus Datos</h1>
            <p style="color: var(--text-muted);">Llena el formulario para que te contactemos o escríbenos directo al WhatsApp.</p>
        </div>

        <?php echo $alerta; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            
            <div class="card-panel">
                <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px;">Formulario de Contacto</h3>
                <form method="POST">
                    
                    <label>Nombre Completo</label>
                    <input type="text" name="nombre" required placeholder="Ej. Juan Pérez">

                    <label>Teléfono / Celular</label>
                    <input type="text" name="telefono" required placeholder="Ej. 099 123 4567">

                    <label>Correo Electrónico</label>
                    <input type="email" name="email" required placeholder="juan@email.com">

                    <label>Dirección / Ciudad</label>
                    <input type="text" name="direccion" required placeholder="Ej. Quito, Av. Amazonas">

                    <button type="submit" class="btn btn-primary btn-full" style="margin-top: 15px;">
                        Enviar mis datos
                    </button>
                </form>
            </div>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <div class="card-panel">
                    <h3 style="margin-top:0; color: var(--primary);">Nuestras Oficinas</h3>
                    
                    <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <i class="fas fa-map-marker-alt fa-2x" style="color: var(--accent);"></i>
                        <div>
                            <strong>Ubicación</strong><br>
                            <span style="color: #666;">Av. de los Motores 123</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <i class="fas fa-phone-alt fa-2x" style="color: var(--accent);"></i>
                        <div>
                            <strong>Teléfonos</strong><br>
                            <span style="color: #666;">+593 99 951 1682</span>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <i class="fas fa-envelope fa-2x" style="color: var(--accent);"></i>
                        <div>
                            <strong>Email</strong><br>
                            <span style="color: #666;">ventas@mecanicapro.com</span>
                        </div>
                    </div>
                </div>

                <a href="https://wa.me/593999511682" target="_blank" class="card-panel" style="display: flex; align-items: center; justify-content: center; gap: 10px; background: #25d366; color: white; text-decoration: none; transition: 0.3s;">
                    <i class="fab fa-whatsapp fa-2x"></i>
                    <span style="font-weight: bold; font-size: 1.1rem;">Chatear ahora</span>
                </a>

            </div>

        </div>
    </div>
</body>
</html>