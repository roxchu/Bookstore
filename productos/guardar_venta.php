<?php
session_start();

header('Content-Type: application/json');

// 1. Conexión directa a la base de datos (igual que en Libros.php)
$conexion = new mysqli("localhost", "root", "", "books_store");
if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $conexion->connect_error]);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// 2. Traemos el ID del usuario usando la misma variable de sesión que Libros.php
$uid = $_SESSION['usuario_id'] ?? null;

if (!$uid) {
    echo json_encode(['success' => false, 'error' => 'No hay sesión de usuario activa.']);
    exit;
}

$total = isset($data['total']) ? floatval($data['total']) : 0;
$metodo = isset($data['metodo']) ? $data['metodo'] : 'Efectivo';

// 3. Insertamos la venta de forma limpia
$sqlVenta = "INSERT INTO ventas (id_usuario, total, metodo_pago) VALUES (?, ?, ?)";
$stmtVenta = $conexion->prepare($sqlVenta);

if (!$stmtVenta) {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación: ' . $conexion->error]);
    exit;
}

$stmtVenta->bind_param("ids", $uid, $total, $metodo);

if ($stmtVenta->execute()) {
    $id_factura = $conexion->insert_id;

    // 4. Buscamos los datos del usuario para armar el ticket final
    $resUser = $conexion->query("SELECT realname, direccion, email FROM usuarios WHERE id = $uid");
    $userData = $resUser ? $resUser->fetch_assoc() : null;

    // Si tu tabla de usuarios usa 'nombre' en vez de 'realname', lo controlamos:
    $nombreCliente = $userData['realname'] ?? $userData['nombre'] ?? 'Cliente';

    echo json_encode([
        'success' => true,
        'id_factura' => $id_factura,
        'usuario' => [
            'realname' => $nombreCliente,
            'direccion' => $userData['direccion'] ?? 'No especificada',
            'email' => $userData['email'] ?? 'Sin email'
        ],
        'compra' => [
            'total' => $total,
            'metodo' => $metodo,
            'fecha' => date('d/m/Y H:i')
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $stmtVenta->error]);
}

$stmtVenta->close();
$conexion->close();
?>