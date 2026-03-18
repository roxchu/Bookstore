<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');

$conexion = new mysqli("localhost", "root", "", "books_store");

if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Usamos id_usuario como confirmaste que tenés en la BD
$uid = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;

if (!$uid || !$data) {
    echo json_encode(['success' => false, 'error' => 'Sesión o datos no encontrados']);
    exit;
}

$total = $data['total'];
$metodo = $data['metodo'];

// 1. GUARDAR EN LA TABLA PRINCIPAL 'ventas'
$sqlVenta = "INSERT INTO ventas (id_usuario, total, metodo_pago) VALUES (?, ?, ?)";
$stmtVenta = $conexion->prepare($sqlVenta);
$stmtVenta->bind_param("ids", $uid, $total, $metodo);

if ($stmtVenta->execute()) {
    $id_factura = $conexion->insert_id; // Obtenemos el ID de la venta que se acaba de crear

    // 2. GUARDAR CADA LIBRO EN 'detalle_ventas'
    // Asegurate que la tabla detalle_ventas tenga: id_venta, nombre_producto, precio
    $sqlDetalle = "INSERT INTO detalle_ventas (id_venta, nombre_producto, precio) VALUES (?, ?, ?)";
    $stmtDet = $conexion->prepare($sqlDetalle);

    foreach ($data['items'] as $libro) {
        $nombre = $libro['name'];
        $precio = $libro['price'];
        $stmtDet->bind_param("isd", $id_factura, $nombre, $precio);
        $stmtDet->execute();
    }

    echo json_encode(['success' => true, 'id_venta' => $id_factura]);
} else {
    echo json_encode(['success' => false, 'error' => $conexion->error]);
}

$conexion->close();
?>