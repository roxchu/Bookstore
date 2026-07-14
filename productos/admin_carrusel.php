<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../DAO/CarruselDAO.php';
require_once __DIR__ . '/../conexion.php';

$conexion = Conexion::conectar();
$carruselDAO = new CarruselDAO($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    if ($action === 'save') {
        $carrusel = $data['carrusel'] ?? [];
        
        try {
            $result = $carruselDAO->guardarCarrusel($carrusel);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Carrusel guardado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar el carrusel']);
            }
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
            $libros = $carruselDAO->obtenerLibrosCarrusel();
            echo json_encode(['status' => 'success', 'carrusel' => $libros]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
