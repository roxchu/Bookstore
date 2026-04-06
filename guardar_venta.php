<?php
session_start();
require_once 'conexion.php'; // Usamos tu archivo de conexión

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// IMPORTANTE: Usamos el nombre de sesión que pusimos en login.php
$uid = $_SESSION['user_id'] ?? $_SESSION['id_usuario'] ?? null;

if (!$uid) {
    echo json_encode(['success' => false, 'error' => 'No hay sesión de usuario']);
    exit;
}

$total = $data['total'];
$metodo = $data['metodo'];

// 1. Insertamos en la tabla ventas (ahora con id_usuario)
$sqlVenta = "INSERT INTO ventas (id_usuario, total, metodo_pago) VALUES (?, ?, ?)";
$stmtVenta = $mysqli->prepare($sqlVenta); // Usamos $mysqli que es como está en tu conexion.php
$stmtVenta->bind_param("ids", $uid, $total, $metodo);

if ($stmtVenta->execute()) {
    $id_factura = $mysqli->insert_id;

    // Buscamos los datos del usuario para el ticket que me pediste antes
    $resUser = $mysqli->query("SELECT realname, direccion, email FROM usuarios WHERE id = $uid");
    $userData = $resUser->fetch_assoc();

    echo json_encode([
        'success' => true,
        'id_factura' => $id_factura,
        'usuario' => $userData,
        'compra' => [
            'total' => $total,
            'metodo' => $metodo,
            'fecha' => date('d/m/Y H:i')
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $mysqli->error]);
}
?>