<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "books_store");

// Recibimos los datos enviados desde el JavaScript
$data = json_encode(file_get_contents('php://input'), true);

if(isset($_SESSION['usuario_id']) && !empty($data['items'])) {
    $uid = $_SESSION['usuario_id'];
    $total = $data['total'];

    // 1. Insertar en la tabla de ventas
    $stmt = $conexion->prepare("INSERT INTO ventas (id_usuario, total) VALUES (?, ?)");
    $stmt->bind_param("id", $uid, $total);
    $stmt->execute();
    $id_venta = $stmt->insert_id;

    // 2. Insertar cada libro en el detalle
    $stmt_det = $conexion->prepare("INSERT INTO detalle_ventas (id_venta, nombre_producto, precio) VALUES (?, ?, ?)");
    foreach($data['items'] as $item) {
        $stmt_det->bind_param("isd", $id_venta, $item['name'], $item['price']);
        $stmt_det->execute();
    }

    echo json_encode(['success' => true, 'id_factura' => $id_venta]);
}
?>