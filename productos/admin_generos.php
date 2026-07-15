<?php
header('Content-Type: application/json');

// Cualquier Warning (ej: move_uploaded_file fallando por permisos) se
// convierte en una excepción real, así nunca se filtra HTML antes del
// JSON (mismo fix aplicado en admin_productos.php).
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

require_once __DIR__ . '/../models/genero.php';
require_once __DIR__ . '/../DAO/generoDAO.php';
require_once __DIR__ . '/../conexion.php';

$conexion = Conexion::conectar();
$generoDAO = new GeneroDAO($conexion);

// Sube el archivo de imagen (si vino) y devuelve el nombre guardado o null
function subirImagenGenero(): ?string {
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $folder = __DIR__ . '/../img/';
    if (!is_dir($folder)) {
        if (!mkdir($folder, 0775, true) && !is_dir($folder)) {
            throw new Exception('No se pudo crear la carpeta img/.');
        }
    }

    if (!is_writable($folder)) {
        throw new Exception('La carpeta img/ no tiene permiso de escritura para Apache. Revisá los permisos del servidor.');
    }

    $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($_FILES['imagen']['tmp_name']);
    if (!isset($tiposPermitidos[$mime])) {
        throw new Exception('La imagen debe ser JPG, PNG o WEBP.');
    }

    $nombreArchivo = time() . '_' . bin2hex(random_bytes(4)) . '.' . $tiposPermitidos[$mime];
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $folder . $nombreArchivo)) {
        throw new Exception('No se pudo guardar la imagen. Verificá que la carpeta img/ tenga permisos de escritura.');
    }
    return $nombreArchivo;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_genero') {
        $nombre = $_POST['nombre'] ?? '';
        
        if (empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre es requerido']);
            exit;
        }

        try {
            $imagen = subirImagenGenero();
            $result = $generoDAO->crearGenero($nombre, $imagen);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Género creado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo crear el género']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'set_imagen') {
        $id_genero = (int)($_POST['id'] ?? 0);

        if (!$id_genero) {
            echo json_encode(['status' => 'error', 'message' => 'ID de género requerido']);
            exit;
        }

        try {
            $imagen = subirImagenGenero();
            if ($imagen === null) {
                echo json_encode(['status' => 'error', 'message' => 'No se recibió ninguna imagen']);
                exit;
            }
            $result = $generoDAO->actualizarImagen($id_genero, $imagen);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Imagen actualizada correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la imagen']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'disable_genero') {
        $id_genero = $_POST['id'] ?? 0;
        
        if (!$id_genero) {
            echo json_encode(['status' => 'error', 'message' => 'ID de género requerido']);
            exit;
        }

        try {
            $result = $generoDAO->deshabilitarGenero($id_genero);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Género deshabilitado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo deshabilitar el género']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'update_destacados') {
        $destacados = json_decode($_POST['destacados'] ?? '[]', true);
        
        if (count($destacados) > 3) {
            echo json_encode(['status' => 'error', 'message' => 'Máximo 3 géneros destacados']);
            exit;
        }

        try {
            // Primero, marcar todos como no destacados
            $generoDAO->marcarTodosNoDestacados();
            
            // Luego, marcar los seleccionados como destacados
            foreach ($destacados as $id) {
                $generoDAO->marcarDestacado($id);
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Géneros destacados actualizados']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
} 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'list') {
        try {
            $stmt = $conexion->query("SELECT id_genero, nombre_genero, destacado, imagen FROM genero WHERE activo = 1 ORDER BY nombre_genero ASC");
            $generos = [];
            while ($row = $stmt->fetch_assoc()) {
                $generos[] = $row;
            }
            echo json_encode(['status' => 'success', 'generos' => $generos]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
