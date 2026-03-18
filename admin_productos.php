<?php
// Evitamos que cualquier error de PHP se muestre como texto y rompa el JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once 'conexion.php';

// Limpiamos cualquier espacio en blanco o salida previa
ob_start();
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'list':
        $res = $mysqli->query("SELECT * FROM producto ORDER BY id DESC");
        if (!$res) {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
            break;
        }
        $productos = $res->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'productos' => $productos]);
        break;

    case 'save':
        try {
            $id        = $_POST['id'] ?? '';
            $nombre    = $_POST['nombre'] ?? '';
            $detalle   = $_POST['detalle'] ?? '';
            $precio    = $_POST['precio'] ?? 0;
            $stock     = $_POST['stock'] ?? 0;
            $id_genero = $_POST['id_genero'] ?? null;
            $nombre_foto = null;

            // Validación básica
            if (empty($nombre) || !$id_genero) {
                echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios (Nombre o Género)']);
                exit;
            }

            // --- Lógica de Imagen ---
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $folder = "img/";
                if (!is_dir($folder)) mkdir($folder, 0777, true);
               
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre_foto = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $folder . $nombre_foto);
            }

            if (!empty($id)) {
                // EDITAR producto
                if ($nombre_foto) {
                    $stmt = $mysqli->prepare("UPDATE producto SET nombre=?, detalle=?, precio=?, stock=?, id_genero=?, imagen=? WHERE id=?");
                    $stmt->bind_param("ssidi si", $nombre, $detalle, $precio, $stock, $id_genero, $nombre_foto, $id);
                } else {
                    $stmt = $mysqli->prepare("UPDATE producto SET nombre=?, detalle=?, precio=?, stock=?, id_genero=? WHERE id=?");
                    $stmt->bind_param("ssidi i", $nombre, $detalle, $precio, $stock, $id_genero, $id);
                }
            } else {
                // CREAR producto nuevo
                $stmt = $mysqli->prepare("INSERT INTO producto (nombre, detalle, precio, stock, id_genero, imagen) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssidis", $nombre, $detalle, $precio, $stock, $id_genero, $nombre_foto);
            }
           
            if ($stmt->execute()) {
                ob_clean(); // Borramos cualquier basura del buffer antes de enviar el éxito
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}

// Cerramos el buffer y enviamos
ob_end_flush();
?>