<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../DAO/VentaDAO.php';
require_once __DIR__ . '/../conexion.php';

// 1. Conexión centralizada — la misma instancia mysqli que usan todas las vistas
$conexion = Conexion::conectar();

// 2. Capturamos el ID del usuario usando la variable de sesión activa
$uid = $_SESSION['usuario_id'] ?? null;

if (!$uid) {
    echo json_encode(['success' => false, 'error' => 'No has iniciado sesión o expiró tu sesión.']);
    exit;
}

// 3. Todo el SQL vive en VentaDAO — acá solo pedimos los objetos
$ventaDAO = new VentaDAO($conexion);
$ventas = $ventaDAO->listarPorUsuario((int) $uid);

// 4. Convertimos los objetos Venta al formato que espera el frontend
$compras = array_map(function (Venta $v) {
    return [
        'id'     => $v->getIdVenta(),
        'total'  => $v->getTotal(),
        'metodo' => $v->getMetodoPago() ?: 'Efectivo',
        'fecha'  => date('d/m/Y H:i', strtotime($v->getFecha())),
    ];
}, $ventas);

echo json_encode([
    'success' => true,
    'compras' => $compras
]);
?>