<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../DAO/VentaDAO.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';
require_once __DIR__ . '/../conexion.php';

try {
    // 1. Validar sesión
    $uid = $_SESSION['usuario_id'] ?? null;
    if (!$uid) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'No hay sesión de usuario activa.']);
        exit;
    }

    // 2. Obtener datos del POST
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'JSON inválido']);
        exit;
    }

    $total     = isset($data['total']) ? floatval($data['total']) : 0;
    $descuento = isset($data['descuento']) ? floatval($data['descuento']) : 0;
    $neto      = isset($data['neto']) ? floatval($data['neto']) : $total;
    $metodo    = $data['metodo'] ?? 'Efectivo';
    $items     = $data['items'] ?? [];
    $cliente   = $data['cliente'] ?? [];

    // 3. Validaciones
    if ($total <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Total debe ser mayor a 0']);
        exit;
    }

    if (empty($items)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Carrito vacío']);
        exit;
    }

    // 4. Conectar a la BD
    $conexion = Conexion::conectar();
    if (!$conexion) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error de conexión a BD']);
        exit;
    }

    // 5. Crear y guardar venta (se cobra el neto, ya con el descuento aplicado)
    $venta = new Venta(0, (int) $uid, $neto, '', $metodo);
    $ventaDAO = new VentaDAO($conexion);
    $id_factura = $ventaDAO->registrarVenta($venta);

    if ($id_factura <= 0) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo registrar la venta']);
        exit;
    }

    // 6. Respuesta exitosa — misma forma que espera mostrarResumenCompra() en libros.php
    echo json_encode([
        'success'    => true,
        'id_factura' => $id_factura,
        'cliente'    => $cliente,
        'metodo'     => $metodo,
        'total'      => $total,
        'descuento'  => $descuento,
        'neto'       => $neto,
        'fecha'      => date('d/m/Y H:i')
    ]);

} catch (Exception $e) {
    http_response_code(500);
    error_log("Error en guardar_venta.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
?>