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
   $data = json_decode(file_get_contents('php://input'), true); 

// Luego, antes de usarlo, verifica que NO sea un string:
if (!is_array($data)) {
    die(json_encode(['success' => false, 'error' => 'Los datos no llegaron como un array']));
}

    echo json_encode(['success' => true, 'id_factura' => $id_venta]);
}
?>