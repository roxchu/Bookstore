<?php
// Habilitamos errores temporalmente por si falta algún include
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// Ajustamos las rutas subiendo un nivel si este archivo está en la carpeta /productos
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../DAO/ProductoDAO.php';
require_once __DIR__ . '/../models/Productos.php';

// Inicializamos la conexión centralizada y el DAO
$mysqli = Conexion::conectar();
$productoDAO = new ProductoDAO($mysqli);

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'list':
        try {
            $listaObjetos = $productoDAO->getAll();
            $productosJson = [];
            
            foreach ($listaObjetos as $p) {
                $productosJson[] = [
                    'id'        => $p->getId(),
                    'nombre'    => $p->getNombre(),
                    'autor'     => $p->getAutor(),
                    'detalle'   => $p->getDetalle(),
                    'precio'    => $p->getPrecio(),
                    'stock'     => $p->getStock(),
                    'id_genero' => $p->getIdGenero(),
                    'imagen'    => $p->getImagen()
                ];
            }
            echo json_encode(['status' => 'success', 'productos' => $productosJson]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'save':
        try {
            $id        = (int)($_POST['id'] ?? 0);
            $nombre    = trim($_POST['nombre'] ?? '');
            $autor     = trim($_POST['autor'] ?? ''); // <-- Capturamos el autor que faltaba
            $detalle   = trim($_POST['detalle'] ?? '');
            $precio    = (float)($_POST['precio'] ?? 0);
            $stock     = (int)($_POST['stock'] ?? 0);
            $id_genero = (int)($_POST['id_genero'] ?? 0);
            
            $nombre_foto = null;

            // Si estamos editando, recuperamos la imagen actual por si no se sube una nueva
            if ($id > 0) {
                $prodActual = $productoDAO->getById($id);
                if ($prodActual) {
                    $nombre_foto = $prodActual->getImagen();
                }
            }

            // --- Lógica de Imagen ---
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $folder = __DIR__ . "/../img/"; // Ruta absoluta hacia tu carpeta de imágenes
                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre_foto = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $folder . $nombre_foto);
            }

            // Creamos el objeto Producto con los datos del formulario (pasando null en imagen2 e imagen3)
            $producto = new Producto($id, $nombre, $autor, $detalle, $precio, $stock, $id_genero, $nombre_foto, null, null);
            
            // El DAO se encarga de saber si hace INSERT o UPDATE gracias al método save()
            if ($productoDAO->save($producto)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar en la base de datos.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'delete':
        try {
            $id = (int)($_REQUEST['id'] ?? 0);
            if ($id > 0) {
                if ($productoDAO->delete($id)) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar el producto.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID no válido']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}