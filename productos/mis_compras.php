<?php
session_start();

// Indicamos al navegador que este archivo responde puramente en formato JSON
header('Content-Type: application/json; charset=utf-8');

// 1. Conexión directa a la base de datos
$conexion = new mysqli("localhost", "root", "", "books_store");
if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $conexion->connect_error]);
    exit;
}

// 2. Capturamos el ID del usuario usando la variable de sesión activa
$uid = $_SESSION['usuario_id'] ?? null;

if (!$uid) {
    echo json_encode(['success' => false, 'error' => 'No has iniciado sesión o expiró tu sesión.']);
    exit;
}

// 3. Traemos todas las columnas usando * para evitar que falle si un nombre cambia
$sql = "SELECT * FROM ventas WHERE id_usuario = ? ORDER BY 1 DESC"; // Ordena por la primera columna (ID) de forma descendente
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en la consulta: ' . $conexion->error]);
    exit;
}

$stmt->bind_param("i", $uid);
$stmt->execute();
$resultado = $stmt->get_result();

$compras = [];
while ($fila = $resultado->fetch_assoc()) {
    // Detectamos de forma segura el ID de la venta (sea id, id_venta o id_factura)
    $idVenta = $fila['id'] ?? $fila['id_venta'] ?? $fila['id_factura'] ?? 0;
    
    // Detectamos de forma segura la fecha (sea fecha_venta o fecha)
    $fechaOriginal = $fila['fecha_venta'] ?? $fila['fecha'] ?? date('Y-m-d H:i:s');
    
    $compras[] = [
        'id' => $idVenta,
        'total' => floatval($fila['total']),
        'metodo' => $fila['metodo_pago'] ?? $fila['metodo'] ?? 'Efectivo',
        'fecha' => date('d/m/Y H:i', strtotime($fechaOriginal)) // Formato Día/Mes/Año
    ];
}

// 4. Devolvemos el resultado exitoso
echo json_encode([
    'success' => true, 
    'compras' => $compras
]);

$stmt->close();
$conexion->close();
?>