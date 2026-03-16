<?php
// Recibe datos enviados desde el formulario
$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$user     = trim($_POST['user'] ?? '');
$pass     = $_POST['pass'] ?? '';
$rpass    = $_POST['rpass'] ?? '';
$email    = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');

// Validación mínima de campos
if ($nombre === '' || $apellido === '' || $user === '' || $pass === '' || $rpass === '' || $email === '' || $telefono === '' || $direccion === '') {
    echo "Por favor, complete todos los campos";
    exit;
}

if ($pass !== $rpass) {
    echo "Las contraseñas no coinciden";
    exit;
}

require_once 'conexion.php';

// Hashear contraseña usando password_hash()
$passHash = password_hash($pass, PASSWORD_DEFAULT);

// Unir nombre y apellido para la columna existente
$nombre_apellido = $nombre . ' ' . $apellido;

// Nota: la base de datos importada tiene la tabla `registros`.
// Ajustamos la consulta para coincidir con esa estructura.
$rol_id = 3;  // Valor por defecto para Cliente
$stmt = $mysqli->prepare("INSERT INTO usuarios (realname, username, pass, email, telefono, direccion, rol_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    // Mostrar el error real de MySQL para ayudar en el debugging.
    http_response_code(500);
    echo "Error en la base de datos: " . $mysqli->error;
    exit;
}

$stmt->bind_param("ssssssi", $nombre_apellido, $user, $passHash, $email, $telefono, $direccion, $rol_id);

if ($stmt->execute()) {
    echo "Usuario registrado con éxito";
} else {
    echo "No se pudo registrar el usuario";
}

$stmt->close();
$mysqli->close();
?>