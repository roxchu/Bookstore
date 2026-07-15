<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../conexion.php';

function responder(int $status, array $data): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['success' => false, 'error' => 'Método no permitido.']);
}

if (empty($_SESSION['usuario_id'])) {
    responder(401, ['success' => false, 'error' => 'Debés iniciar sesión para finalizar la compra.']);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data) || empty($data['items']) || !is_array($data['items'])) {
    responder(400, ['success' => false, 'error' => 'El carrito no contiene productos válidos.']);
}

$metodosValidos = ['transferencia', 'tarjeta', 'efectivo'];
$metodo = $data['metodo'] ?? '';
if (!in_array($metodo, $metodosValidos, true)) {
    responder(400, ['success' => false, 'error' => 'El método de pago no es válido.']);
}

$items = [];
$subtotal = 0.0;
foreach ($data['items'] as $item) {
    $nombre = trim((string)($item['name'] ?? ''));
    $precio = filter_var($item['price'] ?? null, FILTER_VALIDATE_FLOAT);
    if ($nombre === '' || $precio === false || $precio <= 0) {
        responder(400, ['success' => false, 'error' => 'Hay un producto inválido en el carrito.']);
    }
    $items[] = ['nombre' => $nombre, 'precio' => (float)$precio];
    $subtotal += (float)$precio;
}

$descuento = $metodo === 'transferencia' ? round($subtotal * 0.10, 2) : 0.0;
$total = round($subtotal - $descuento, 2);
$conexion = Conexion::conectar();

try {
    $conexion->begin_transaction();

    $venta = $conexion->prepare('INSERT INTO ventas (id_usuario, total, metodo_pago) VALUES (?, ?, ?)');
    $usuarioId = (int)$_SESSION['usuario_id'];
    $venta->bind_param('ids', $usuarioId, $total, $metodo);
    if (!$venta->execute()) {
        throw new RuntimeException('No se pudo registrar la venta.');
    }
    $idVenta = $conexion->insert_id;
    $venta->close();

    // La estructura real de detalle_ventas usa nombre_producto y precio.
    $detalle = $conexion->prepare('INSERT INTO detalle_ventas (id_venta, nombre_producto, precio) VALUES (?, ?, ?)');
    foreach ($items as $item) {
        $detalle->bind_param('isd', $idVenta, $item['nombre'], $item['precio']);
        if (!$detalle->execute()) {
            throw new RuntimeException('No se pudo guardar el detalle de la venta.');
        }
    }
    $detalle->close();
    $conexion->commit();

    responder(201, ['success' => true, 'id_factura' => $idVenta, 'total' => $subtotal, 'descuento' => $descuento, 'neto' => $total]);
} catch (Throwable $e) {
    $conexion->rollback();
    error_log('guardar_venta.php: ' . $e->getMessage());
    responder(500, ['success' => false, 'error' => 'No se pudo completar la compra. Intentá nuevamente.']);
}
?>
