<?php
session_start();
require 'db.php'; // Asegúrate que tu archivo de conexión se llame así

$response = ['count' => 0, 'total' => 0, 'html' => ''];

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

// AGREGAR
if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
    $id = $_POST['id'];
    $_SESSION['carrito'][$id] = isset($_SESSION['carrito'][$id]) ? $_SESSION['carrito'][$id] + 1 : 1;
}

// ELIMINAR
if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['id'];
    if (isset($_SESSION['carrito'][$id])) unset($_SESSION['carrito'][$id]);
}

// GENERAR HTML PARA EL MINI CARRITO
$total = 0;
$count = 0;

if (!empty($_SESSION['carrito'])) {
    $ids = implode(',', array_keys($_SESSION['carrito']));
    $stmt = $pdo->query("SELECT id, nombre, precio, imagen FROM repuestos WHERE id IN ($ids)");
    
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qty = $_SESSION['carrito'][$r['id']];
        $sub = $r['precio'] * $qty;
        $total += $sub;
        $count += $qty;
        
        // Imagen por defecto si no hay
        $img = $r['imagen'] ? $r['imagen'] : 'imagenes/sin-foto.png';
        
        $response['html'] .= '
        <div class="mini-item">
            <img src="'.$img.'" class="mini-img">
            <div class="mini-info">
                <span class="mini-title">'.$r['nombre'].'</span>
                <span class="mini-price">'.$qty.' x $'.number_format($r['precio'], 2).'</span>
            </div>
            <button onclick="eliminarItem('.$r['id'].')" class="btn-trash" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>';
    }
} else {
    $response['html'] = '<div class="empty-cart">Tu carrito está vacío</div>';
}

$response['count'] = $count;
$response['total'] = number_format($total, 2);

echo json_encode($response);
?>