<?php
require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

   
    case 'list':
        $result = $mysqli->query("SELECT id, nombre, detalle, stock, precio FROM producto ORDER BY id DESC");
        if (!$result) {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
            exit;
        }
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        echo json_encode(['status' => 'success', 'productos' => $productos]);
        break;

   
    case 'create':
        $nombre  = trim($_POST['nombre']  ?? '');
        $detalle = trim($_POST['detalle'] ?? '');
        $precio  = $_POST['precio'] ?? null;
        $stock   = $_POST['stock']  ?? null;

        if ($nombre === '' || $precio === null || $stock === null) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos requeridos']);
            exit;
        }

        $stmt = $mysqli->prepare("INSERT INTO producto (nombre, detalle, stock, precio) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssid", $nombre, $detalle, $stock, $precio);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
        break;

    // ─── ACTUALIZAR ──────────────────────────────────────────────────────────
    case 'update':
        $id      = intval($_POST['id']     ?? 0);
        $nombre  = trim($_POST['nombre']   ?? '');
        $detalle = trim($_POST['detalle']  ?? '');
        $precio  = $_POST['precio'] ?? null;
        $stock   = $_POST['stock']  ?? null;

        if ($id === 0 || $nombre === '' || $precio === null || $stock === null) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos requeridos']);
            exit;
        }

        $stmt = $mysqli->prepare("UPDATE producto SET nombre = ?, detalle = ?, stock = ?, precio = ? WHERE id = ?");
        $stmt->bind_param("ssidi", $nombre, $detalle, $stock, $precio, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
        break;

   
    case 'delete':
        $id = intval($_POST['id'] ?? 0);

        if ($id === 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
            exit;
        }

        $stmt = $mysqli->prepare("DELETE FROM producto WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}

$mysqli->close();
?>