<?php
// 1. Iniciamos sesión para saber quién es el usuario logueado
session_start();

// 2. Configuramos para que la respuesta sea en formato JSON (lo que espera el fetch de JavaScript)
header('Content-Type: application/json; charset=utf-8');

// 3. Verificación de seguridad: si no hay sesión, no puede comprar
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Error: Debes estar logueado para comprar.']);
    exit;
}

// 4. Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "books_store");

if ($conexion->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

// 5. Capturamos los datos enviados por el carrito (vienen en formato JSON)
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

// 6. Validamos que el carrito no llegue vacío
if (!$datos || empty($datos['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'El carrito está vacío.']);
    exit;
}

// 7. Preparamos la información para guardar
$id_usuario = $_SESSION['usuario_id'];
$total = floatval($datos['total']);

// Juntamos los nombres de los libros en una sola cadena de texto para la columna 'productos'
$nombres_libros = [];
foreach ($datos['items'] as $item) {
    $nombres_libros[] = $conexion->real_escape_string($item['name']);
}
$lista_productos = implode(", ", $nombres_libros);

// 8. Insertamos la venta en la base de datos
// Usamos sentencias preparadas por seguridad contra inyección SQL
$sql = "INSERT INTO ventas (id_usuario, productos, total) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("isd", $id_usuario, $lista_productos, $total);

if ($stmt->execute()) {
    // Si todo sale bien, respondemos éxito
    echo json_encode([
        'status' => 'success', 
        'message' => '¡Compra realizada con éxito! Gracias por elegir Bookstore.'
    ]);
} else {
    // Si falla el insert
    echo json_encode([
        'status' => 'error', 
        'message' => 'No se pudo registrar la venta en la base de datos.'
    ]);
}

// 9. Cerramos conexiones
$stmt->close();
$conexion->close();
?>