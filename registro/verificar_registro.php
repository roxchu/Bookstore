<?php
session_start();

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';
require_once __DIR__ . '/../models/Usuario.php';

header('Content-Type: application/json; charset=utf-8');

$codigo = trim($_POST['codigo'] ?? '');
$pendiente = $_SESSION['registro_temp'] ?? null;

// El email dice que el código vence en 15 minutos (ver Mailer.php)
const CODIGO_VIGENCIA_SEGUNDOS = 900;

if ($codigo === '') {
    echo json_encode(['type' => 'error', 'ack' => 'Ingrese el codigo de verificacion.', 'field' => 'codigo']);
    exit;
}

if (!is_array($pendiente)) {
    echo json_encode(['type' => 'error', 'ack' => 'No hay un registro pendiente. Complete el formulario nuevamente.']);
    exit;
}

if ((($pendiente['codigo_timestamp'] ?? 0) + CODIGO_VIGENCIA_SEGUNDOS) < time()) {
    unset($_SESSION['registro_temp']);
    echo json_encode(['type' => 'error', 'ack' => 'El codigo vencio. Complete el registro nuevamente.']);
    exit;
}

if (!hash_equals((string)$pendiente['codigo'], $codigo)) {
    echo json_encode(['type' => 'error', 'ack' => 'El codigo ingresado no es correcto.', 'field' => 'codigo']);
    exit;
}

$mysqli = Conexion::conectar();
$dao = new UsuarioDAO($mysqli);

// El formulario no pide username ni dirección, pero la tabla usuarios las
// exige (username además es UNIQUE). Generamos un username a partir del
// email y garantizamos que no choque con uno existente.
$baseUsername = strtolower(preg_replace('/[^a-z0-9]/i', '', strstr($pendiente['email'], '@', true)));
if ($baseUsername === '') {
    $baseUsername = 'usuario';
}
$username = $baseUsername;
$intentos = 0;
while ($dao->usernameExiste($username)) {
    $intentos++;
    $username = $baseUsername . $intentos;
}

$usuario = new Usuario(
    0,
    trim($pendiente['nombre'] . ' ' . $pendiente['apellido']),
    $username,
    $pendiente['password'], // texto plano; registrarUsuario() lo hashea
    $pendiente['email'],
    $pendiente['telefono'],
    '', // dirección: no se pide en el formulario de registro
    3   // rol Cliente
);

if (!$dao->registrarUsuario($usuario)) {
    echo json_encode(['type' => 'error', 'ack' => 'No se pudo registrar el usuario. El usuario o email ya existen.']);
    exit;
}

$usuarioCreado = $dao->loginUsuario($username);

if ($usuarioCreado !== null) {
    $_SESSION['usuario_id'] = $usuarioCreado->getID();
    $_SESSION['user_id'] = $usuarioCreado->getID();
    $_SESSION['username'] = $usuarioCreado->getUserName();
    $_SESSION['rol_id'] = $usuarioCreado->getIdRol();
}

unset($_SESSION['registro_temp']);

echo json_encode(['type' => 'success', 'ack' => 'Usuario registrado con exito.']);