<?php
session_start();

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';
require_once __DIR__ . '/../models/Usuario.php';

header('Content-Type: application/json; charset=utf-8');

$codigo = trim($_POST['codigo'] ?? '');
$pendiente = $_SESSION['registro_pendiente'] ?? null;

if ($codigo === '') {
    echo json_encode(['type' => 'error', 'ack' => 'Ingrese el codigo de verificacion.', 'field' => 'codigo']);
    exit;
}

if (!is_array($pendiente)) {
    echo json_encode(['type' => 'error', 'ack' => 'No hay un registro pendiente. Complete el formulario nuevamente.']);
    exit;
}

if (($pendiente['expires_at'] ?? 0) < time()) {
    unset($_SESSION['registro_pendiente']);
    echo json_encode(['type' => 'error', 'ack' => 'El codigo vencio. Complete el registro nuevamente.']);
    exit;
}

if (!hash_equals((string)$pendiente['codigo'], $codigo)) {
    echo json_encode(['type' => 'error', 'ack' => 'El codigo ingresado no es correcto.', 'field' => 'codigo']);
    exit;
}

$mysqli = Conexion::conectar();
$dao = new UsuarioDAO($mysqli);

$usuario = new Usuario(
    0,
    $pendiente['realname'],
    $pendiente['user'],
    $pendiente['pass'],
    $pendiente['email'],
    $pendiente['telefono'],
    $pendiente['direccion'],
    3
);

if (!$dao->registrarUsuario($usuario)) {
    echo json_encode(['type' => 'error', 'ack' => 'No se pudo registrar el usuario. El usuario o email ya existen.']);
    exit;
}

$usuarioCreado = $dao->loginUsuario($pendiente['user']);

if ($usuarioCreado !== null) {
    $_SESSION['usuario_id'] = $usuarioCreado->getID();
    $_SESSION['user_id'] = $usuarioCreado->getID();
    $_SESSION['username'] = $usuarioCreado->getUserName();
    $_SESSION['rol_id'] = $usuarioCreado->getIdRol();
}

unset($_SESSION['registro_pendiente']);

echo json_encode(['type' => 'success', 'ack' => 'Usuario registrado con exito.']);
