<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: login.php");
require 'db.php';

if (isset($_GET['id'])) {
    // Opcional: Borrar imagen del servidor antes de borrar registro
    $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $img = $stmt->fetchColumn();
    if(file_exists($img)) unlink($img);

    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}
header("Location: admin.php");