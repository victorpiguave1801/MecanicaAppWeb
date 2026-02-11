<?php
session_start();
// Seguridad: Solo admin puede ver esto
if (!isset($_SESSION['admin'])) header("Location: login.php");
require 'conexion.php';

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
    <link rel="stylesheet" href="css/estilos.css">
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
            <a href="mensajes.php">✉️ Mensajes</a>
            <a href="logout.php" style="color: #ef4444; margin-top: 20px;">Cerrar Sesión</a>
        </aside>

        <main