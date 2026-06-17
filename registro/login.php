<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once '../../config/conexion.php';
require_once '../../DAO/UsuarioDAO.php';

header('Content-Type: application/json; charset=utf-8');

// ── Validación de campos vacíos (se queda en la vista) ────────
$user = trim($_POST['username'] ?? '');
$pass = $_POST['password'] ?? '';

if ($user === '' || $pass === '') {
    echo json_encode(['status' => 'error', 'message' => 'Completa todos los campos']);
    exit;
}

// ── Llamada al DAO ─────────────────────────────────────────────
$dao    = new UsuarioDAO($mysqli);
$usuario = $dao->loginUsuario($user);

// ── Lógica de verificación (se queda en la vista) ─────────────
if ($usuario === null) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    exit;
}

if (!password_verify($pass, $usuario->getPass())) {
    echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
    exit;
}

// ── Sesión (se queda en la vista) ─────────────────────────────
$_SESSION['usuario_id'] = $usuario->getId();
$_SESSION['user_id']    = $usuario->getId();
$_SESSION['username']   = $usuario->getUserName();
$_SESSION['rol_id']     = $usuario->getIdRol();

// ── Redirect según rol (se queda en la vista) ─────────────────
switch ($usuario->getIdRol()) {
    case 1:
        $redirect = 'panel_admin.html';
        break;
    case 2:
        $redirect = 'panel_empleado.html';
        break;
    case 3:
    default:
        $redirect = 'index.php';
}

echo json_encode(['status' => 'success', 'redirect' => $redirect]);
?>