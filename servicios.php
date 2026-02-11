<?php 
session_start();
require 'db.php'; 
// Consultamos los servicios creados en el admin
$servicios = $pdo->query("SELECT * FROM servicios ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuestros Servicios - MecánicaPRO</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos de las tarjetas */
        .service-card {
            background: white; border: 1px solid var(--border);
            border-radius: 8px; padding: 30px; text-align: center;
            transition: 0.3s; position: relative; overflow: hidden;
            display: flex; flex-direction: column; justify-content: space-between;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1); border-color: var(--accent);
        }
        .icon-box {
            width: 80px; height: 80px; background: #f3f4f6; color: var(--primary);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 2rem; transition: 0.3s;
        }
        .service-card:hover .icon-box { background: var(--accent); color: white; }
        .service-title { font-size: 1.25rem; font-weight: 800; margin-bottom: 10px; }
        .service-desc { color: #666; font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px; flex-grow: 1; }
    </style>
</head>
<body>
    
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">🔧 Mecánica<span>PRO</span></a>
            <nav class="nav-menu">
                <a href="index.php">Catálogo</a>
                <a href="servicios.php" style="color:var(--accent); font-weight:bold;">Servicios</a>
                <a href="contacto.php">Contacto</a>
            </nav>
        </div>
    </header>

    <div style="background: var(--primary); color: white; text-align: center; padding: 60px 20px;">
        <h1 style="margin: 0; font-size: 2.5rem;">Servicios Profesionales</h1>
        <p style="opacity: 0.8; margin-top: 10px;">Soluciones integrales para el cuidado de tu vehículo.</p>
    </div>

    <div class="container" style="margin-top: 50px; margin-bottom: 50px;">
        
        <?php if(empty($servicios)): ?>
            <div style="text-align: center; padding: 50px; color: #999;">
                <h2>Próximamente</h2>
                <p>Estamos actualizando nuestra lista de servicios.</p>
            </div>
        <?php else: ?>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                
                <?php foreach($servicios as $s): ?>
                <div class="service-card">
                    <div>
                        <div class="icon-box"><i class="<?php echo $s['icono']; ?>"></i></div>
                        <div class="service-title"><?php echo $s['titulo']; ?></div>
                        <p class="service-desc"><?php echo $s['descripcion']; ?></p>
                    </div>
                    
                    <a href="https://wa.me/593999999999?text=Hola,%20me%20interesa%20el%20servicio%20de:%20<?php echo urlencode($s['titulo']); ?>" class="btn btn-outline" target="_blank">
                        Agendar Cita
                    </a>
                </div>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>

        <div class="card" style="background: var(--primary); color: white; margin-top: 50px; padding: 40px; text-align: center; border: none;">
            <h2>¿No encuentras lo que buscas?</h2>
            <p style="opacity: 0.8; margin-bottom: 20px;">Escríbenos, realizamos todo tipo de trabajos mecánicos.</p>
            <a href="contacto.php" class="btn btn-primary" style="display: inline-block; width: auto; font-size: 1.2rem; padding: 15px 30px;">
                Contáctanos
            </a>
        </div>

    </div>

</body>
</html>