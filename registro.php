<?php
// Recibe datos enviados desde el formulario
$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$user     = trim($_POST['user'] ?? '');
$pass     = $_POST['pass'] ?? '';
$rpass    = $_POST['rpass'] ?? '';

// Validación mínima de campos
if ($nombre === '' || $apellido === '' || $user === '' || $pass === '' || $rpass === '') {
    echo "Por favor, complete todos los campos";
    exit;
}

if ($pass !== $rpass) {
    echo "Las contraseñas no coinciden";
    exit;
}

require_once 'conexion.php';

// Hashear contraseña (para producción usar password_hash())
$passHash = md5($pass);

// Unir nombre y apellido para la columna existente
$nombre_apellido = $nombre . ' ' . $apellido;

// Nota: la base de datos importada tiene la tabla `registros`.
// Ajustamos la consulta para coincidir con esa estructura.
$stmt = $mysqli->prepare("INSERT INTO registros (realname, username, pass) VALUES (?, ?, ?)");
if (!$stmt) {
    // Mostrar el error real de MySQL para ayudar en el debugging.
    http_response_code(500);
    echo "Error en la base de datos: " . $mysqli->error;
    exit;
}

$stmt->bind_param("sss", $nombre_apellido, $user, $passHash);

if ($stmt->execute()) {
    echo "Usuario registrado con éxito";
} else {
    echo "No se pudo registrar el usuario";
}

$stmt->close();
$mysqli->close();
?>