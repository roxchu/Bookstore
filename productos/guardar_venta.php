<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../DAO/VentaDAO.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';
require_once __DIR__ . '/../conexion.php';

// 1. Conexión centralizada — la misma instancia mysqli que usan todas las vistas
$conexion = Conexion::conectar();

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// 2. Traemos el ID del usuario de la sesión
$uid = $_SESSION['usuario_id'] ?? null;

if (!$uid) {
    echo json_encode(['success' => false, 'error' => 'No hay sesión de usuario activa.']);
    exit;
}

$total  = isset($data['total']) ? floatval($data['total']) : 0;
$metodo = $data['metodo'] ?? 'Efectivo';

// 3. Armamos el objeto Venta y delegamos el INSERT al DAO
//    (el id y la fecha los ignora/gestiona la BD, van en 0 / string vacío)
$venta = new Venta(0, (int) $uid, $total, '', $metodo);

$ventaDAO = new VentaDAO($conexion);
$id_factura = $ventaDAO->registrarVenta($venta);

if ($id_factura > 0) {
    // 4. Buscamos los datos del usuario para armar el ticket — ahora vía DAO
    $usuarioDAO = new UsuarioDAO($conexion);
    $usuario = $usuarioDAO->getById((int) $uid);

    $nombreCliente = $usuario ? $usuario->getRealName() : 'Cliente';
    $direccion     = $usuario ? $usuario->getDireccion() : 'No especificada';
    $email         = $usuario ? $usuario->getEmail()     : 'Sin email';

    echo json_encode([
        'success' => true,
        'id_factura' => $id_factura,
        'usuario' => [
            'realname'  => $nombreCliente,
            'direccion' => $direccion,
            'email'     => $email
        ],
        'compra' => [
            'total'  => $total,
            'metodo' => $metodo,
            'fecha'  => date('d/m/Y H:i')
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se pudo registrar la venta.']);
}
?>