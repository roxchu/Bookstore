<?php
session_start();

require_once '../conexion.php';  
require_once '../DAO/UsuarioDAO.php';    
require_once '../models/Usuario.php';   

header('Content-Type: application/json; charset=utf-8');

// Conexión centralizada — la misma instancia mysqli que usan todas las vistas
$mysqli = Conexion::conectar();

// Capturamos las variables usando los nombres de tu formulario
$user = trim($_POST['username'] ?? '');
$pass = $_POST['password'] ?? '';

// Validación de campos vacíos con el JS
if ($user === '' || $pass === '') {
    echo json_encode(["status" => "error", "message" => "Por favor completa todos los campos."]);
    exit;
}

// Instanciamos el DAO pasándole la conexión mysqli
$dao = new UsuarioDAO($mysqli);

// Buscamos al usuario en la base de datos
$usuario = $dao->loginUsuario($user);

// Verificamos si el usuario existe y si la contraseña coincide
if ($usuario !== null && password_verify($pass, $usuario->getPass())) {
    
    // Guardamos los datos clave en la sesión
    $_SESSION['usuario_id'] = $usuario->getID();
    $_SESSION['user_id']    = $usuario->getID();
    $_SESSION['username']   = $usuario->getUserName();
    $_SESSION['rol_id']     = $usuario->getIdRol();

    // Redirección según el rol
    $redirectUrl = "../index.php";
    if ($usuario->getIdRol() == 1) {
        $redirectUrl = "../paneles/panel_admin.html";
    } elseif ($usuario->getIdRol() == 2) {
        $redirectUrl = "../paneles/panel_empleado.html";
    }

    // Retornamos "status" y "message" exactos para el handleLogin(), más la URL de destino
    echo json_encode([
        "status" => "success", 
        "message" => "¡Sesión iniciada correctamente! Redirigiendo...",
        "redirectUrl" => $redirectUrl
    ]);
    exit;
} else {
    // Retornamos el error formateado idéntico para que entre al showError()
    echo json_encode([
        "status" => "error", 
        "message" => "Usuario o contraseña incorrectos."
    ]);
    exit;
}
?>