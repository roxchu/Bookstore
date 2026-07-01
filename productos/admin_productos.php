<?php
require_once __DIR__ . '/../models/Productos.php';
require_once __DIR__ . '/../DAO/ProductoDAO.php';
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json');

// Conexión centralizada — la misma instancia mysqli que usan todas las vistas
$mysqli = Conexion::conectar();

$productoDAO = new ProductoDAO($mysqli);
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'list':
        // Todo el SELECT vive en ProductoDAO::getAll()
        $productos = $productoDAO->getAll();
        $lista = array_map(fn(Producto $p) => [
            'id'        => $p->getId(),
            'nombre'    => $p->getNombre(),
            'autor'     => $p->getAutor(),
            'detalle'   => $p->getDetalle(),
            'precio'    => $p->getPrecio(),
            'stock'     => $p->getStock(),
            'id_genero' => $p->getIdGenero(),
            'imagen'    => $p->getImagen(),
        ], $productos);
        echo json_encode(['status' => 'success', 'productos' => $lista]);
        break;

    case 'save':
        $id        = (int) ($_POST['id'] ?? 0);
        $nombre    = $_POST['nombre'] ?? '';
        $autor     = $_POST['autor'] ?? '';
        $detalle   = $_POST['detalle'] ?? '';
        $precio    = (float) ($_POST['precio'] ?? 0);
        $stock     = (int) ($_POST['stock'] ?? 0);
        $id_genero = (int) ($_POST['id_genero'] ?? 0);

        // Si estamos editando y no llega una imagen nueva, conservamos la actual
        $imagen = null;
        if ($id > 0) {
            $existente = $productoDAO->getById($id);
            $imagen = $existente?->getImagen();
        }

        // --- Lógica de Imagen (se queda acá, es manejo de archivos, no SQL) ---
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $folder = __DIR__ . '/../img/';
            if (!is_dir($folder)) mkdir($folder, 0777, true);
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $imagen = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $folder . $imagen);
        }

        // Armamos el objeto Producto; save() decide INSERT o UPDATE según el id
        $producto = new Producto($id, $nombre, $autor, $detalle, $precio, $stock, $id_genero, $imagen);

        if ($productoDAO->save($producto)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar el producto']);
            // Log el error en el servidor (no en JSON)
            error_log("Error al guardar producto: " . $mysqli->error); 
        }
        break;

    case 'delete':
        $id = (int) ($_REQUEST['id'] ?? 0);
        if ($id > 0 && $productoDAO->delete($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID no válido o no se pudo eliminar']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}
?>