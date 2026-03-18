<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'conexion.php';

ob_start();
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'list':
        $res = $mysqli->query("SELECT * FROM producto ORDER BY id DESC");
        $productos = $res->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'productos' => $productos]);
        break;

    case 'save':
        $id        = $_POST['id'] ?? '';
        $nombre    = $_POST['nombre'] ?? '';
        $detalle   = $_POST['detalle'] ?? '';
        $precio    = $_POST['precio'] ?? 0;
        $stock     = $_POST['stock'] ?? 0;
        $id_genero = $_POST['id_genero'] ?? null;
        $nombre_foto = null;

        // --- Lógica de Imagen ---
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $folder = "img/";
            if (!is_dir($folder)) mkdir($folder, 0777, true);
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombre_foto = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $folder . $nombre_foto);
        }

        if (!empty($id)) {
            // EDITAR: Corregí los espacios en bind_param ("ssidi i" -> "ssidii")
            if ($nombre_foto) {
                $stmt = $mysqli->prepare("UPDATE producto SET nombre=?, detalle=?, precio=?, stock=?, id_genero=?, imagen=? WHERE id=?");
                $stmt->bind_param("ssidisi", $nombre, $detalle, $precio, $stock, $id_genero, $nombre_foto, $id);
            } else {
                $stmt = $mysqli->prepare("UPDATE producto SET nombre=?, detalle=?, precio=?, stock=?, id_genero=? WHERE id=?");
                $stmt->bind_param("ssidii", $nombre, $detalle, $precio, $stock, $id_genero, $id);
            }
        } else {
            // CREAR
            $stmt = $mysqli->prepare("INSERT INTO producto (nombre, detalle, precio, stock, id_genero, imagen) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidis", $nombre, $detalle, $precio, $stock, $id_genero, $nombre_foto);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        break;

    // --- AGREGAMOS LA ACCIÓN DE ELIMINAR ---
    case 'delete':
        $id = $_REQUEST['id'] ?? 0;
        if ($id > 0) {
            $stmt = $mysqli->prepare("DELETE FROM producto WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID no válido']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}
ob_end_flush();
?>