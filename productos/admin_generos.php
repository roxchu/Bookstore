<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/genero.php';
require_once __DIR__ . '/../DAO/generoDAO.php';
require_once __DIR__ . '/../conexion.php';

$conexion = Conexion::conectar();
$generoDAO = new GeneroDAO($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_genero') {
        $nombre = $_POST['nombre'] ?? '';
        
        if (empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre es requerido']);
            exit;
        }

        try {
            $result = $generoDAO->crearGenero($nombre);
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
            $stmt = $conexion->query("SELECT id_genero, nombre_genero, destacado FROM genero WHERE activo = 1 ORDER BY nombre_genero ASC");
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
