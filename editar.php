<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: login.php");
require 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    
    // Si suben nueva imagen, la actualizamos
    if (!empty($_FILES['imagen']['name'])) {
        $ruta = "imagenes/" . time() . "_" . $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
        $sql = "UPDATE productos SET nombre=?, precio=?, imagen=? WHERE id=?";
        $pdo->prepare($sql)->execute([$nombre, $precio, $ruta, $id]);
    } else {
        $sql = "UPDATE productos SET nombre=?, precio=? WHERE id=?";
        $pdo->prepare($sql)->execute([$nombre, $precio, $id]);
    }
    header("Location: admin.php");
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="css/styles.css"></head>
<body>
    <div class="container">
        <h2>Editar Producto</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
            <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required>
            <p>Imagen actual: <img src="<?php echo $producto['imagen']; ?>" width="50"></p>
            <input type="file" name="imagen">
            <button type="submit">Actualizar</button>
        </form>
    </div>
</body>
</html>