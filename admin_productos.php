<?php
require_once 'conexion.php';
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'list':
        $res = $mysqli->query("SELECT * FROM producto ORDER BY id DESC");
        $productos = $res->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'productos' => $productos]);
        break;

    case 'save': // Sirve para crear y editar
        $id      = $_POST['id'] ?? '';
        $nombre  = $_POST['nombre'];
        $detalle = $_POST['detalle'];
        $precio  = $_POST['precio'];
        $stock   = $_POST['stock'];

        if ($id) {
            // Editar
            $stmt = $mysqli->prepare("UPDATE producto SET nombre=?, detalle=?, precio=?, stock=? WHERE id=?");
            $stmt->bind_param("ssidi", $nombre, $detalle, $precio, $stock, $id);
        } else {
            // Crear nuevo
            $stmt = $mysqli->prepare("INSERT INTO producto (nombre, detalle, precio, stock) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssid", $nombre, $detalle, $precio, $stock);
        }
        
        if ($stmt->execute()) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        break;

    case 'delete':
        $id = $_POST['id'];
        $stmt = $mysqli->prepare("DELETE FROM producto WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        break;
}
?>