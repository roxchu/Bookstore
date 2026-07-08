<?php
session_start();

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/Mailer.php';

header('Content-Type: application/json; charset=utf-8');

$mysqli = Conexion::conectar();
$dao = new UsuarioDAO($mysqli);

$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$user = trim($_POST['user'] ?? '');
$pass = $_POST['pass'] ?? '';
$rpass = $_POST['rpass'] ?? '';
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');

$requiredFields = [
    'nombre' => 'nombre',
    'apellido' => 'apellido',
    'user' => 'user',
    'pass' => 'pass',
    'rpass' => 'rpass',
    'email' => 'email',
    'telefono' => 'telefono',
    'direccion' => 'direccion'
];

foreach ($requiredFields as $postKey => $fieldId) {
    if (trim($_POST[$postKey] ?? '') === '') {
        echo json_encode(['type' => 'error', 'ack' => 'Por favor, complete el campo requerido.', 'field' => $fieldId]);
        exit;
    }
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['type' => 'error', 'ack' => 'El email no es valido.', 'field' => 'email']);
    exit;
}

$passwordError = '';
if ($pass !== $rpass) {
    $passwordError .= "Las contrasenas no coinciden.\n";
}
if (strlen($pass) < 8) {
    $passwordError .= "La contrasena debe tener al menos 8 caracteres.\n";
}
if (!preg_match('#[0-9]+#', $pass)) {
    $passwordError .= "La contrasena debe tener al menos un numero.\n";
}
if (!preg_match('#[A-Z]+#', $pass)) {
    $passwordError .= "La contrasena debe tener al menos una mayuscula.\n";
}
if (!preg_match('#[a-z]+#', $pass)) {
    $passwordError .= "La contrasena debe tener al menos una minuscula.\n";
}
if (!preg_match('#[^\w]+#', $pass)) {
    $passwordError .= "La contrasena debe tener al menos un simbolo especial.\n";
}

if ($passwordError !== '') {
    echo json_encode(['type' => 'error', 'ack' => trim($passwordError), 'field' => ['pass', 'rpass']]);
    exit;
}

if ($dao->loginUsuario($user) !== null) {
    echo json_encode(['type' => 'error', 'ack' => 'Ese usuario ya existe.', 'field' => 'user']);
    exit;
}

if ($dao->getByEmail($email) !== null) {
    echo json_encode(['type' => 'error', 'ack' => 'Ese email ya esta registrado.', 'field' => 'email']);
    exit;
}

$nombreApellido = $nombre . ' ' . $apellido;
$codigo = (string)random_int(100000, 999999);
$mailError = null;

if (!enviarCodigoRegistro($email, $nombreApellido, $codigo, $mailError)) {
    echo json_encode([
        'type' => 'error',
        'ack' => $mailError ?: 'No se pudo enviar el codigo de verificacion. Intentelo nuevamente.'
    ]);
    exit;
}

$_SESSION['registro_pendiente'] = [
    'realname' => $nombreApellido,
    'user' => $user,
    'pass' => $pass,
    'email' => $email,
    'telefono' => $telefono,
    'direccion' => $direccion,
    'codigo' => $codigo,
    'expires_at' => time() + 900
];

echo json_encode([
    'type' => 'code_sent',
    'ack' => 'Te enviamos un codigo a tu email. Ingresalo para terminar el registro.'
]);
