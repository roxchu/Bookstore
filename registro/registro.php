<?php
session_start();


require_once '../conexion.php';  
require_once '../DAO/UsuarioDAO.php';    
require_once '../models/Usuario.php';   

header('Content-Type: application/json; charset=utf-8');

// Forzamos la conexión mysqli usando las credenciales idénticas a tu conexion.php
if (!isset($mysqli)) {
    $mysqli = new mysqli("localhost", "root", "", "books_store");
}

// ── Recibe datos del formulario ────────────────────────────────
$nombre    = trim($_POST['nombre']    ?? '');
$apellido  = trim($_POST['apellido']  ?? '');
$user      = trim($_POST['user']      ?? '');
$pass      = $_POST['pass']           ?? '';
$rpass     = $_POST['rpass']          ?? '';
$email     = trim($_POST['email']     ?? '');
$telefono  = trim($_POST['telefono']  ?? '');
$direccion = trim($_POST['direccion'] ?? '');

// ── Validación de campos vacíos ────────────────────────
$requiredFields = [
    'nombre'   => 'nombre',
    'apellido' => 'apellido',
    'user'     => 'username',
    'pass'     => 'password',
    'rpass'    => 'rpassword',
    'email'    => 'email',
    'telefono' => 'telefono',
    'direccion'=> 'direccion'
];

foreach ($requiredFields as $postKey => $fieldId) {
    if (trim($_POST[$postKey] ?? '') === '') {
        echo json_encode(["type" => "error", "ack" => "Por favor, complete el campo requerido.", "field" => $fieldId]);
        exit;
    }
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["type" => "error", "ack" => "El email no es válido.", "field" => "email"]);
    exit;
}

// ── Validación de contraseña ───────────────────────────
$passwordError = "";
if ($pass !== $rpass)                        $passwordError .= "Las contraseñas no coinciden.\n";
if (strlen($pass) < 8)                       $passwordError .= "La contraseña debe tener al menos 8 caracteres.\n";
if (!preg_match("#[0-9]+#", $pass))          $passwordError .= "La contraseña debe tener al menos un número.\n";
if (!preg_match("#[A-Z]+#", $pass))          $passwordError .= "La contraseña debe tener al menos una mayúscula.\n";
if (!preg_match("#[a-z]+#", $pass))          $passwordError .= "La contraseña debe tener al menos una minúscula.\n";
if (!preg_match("#[^\w]+#", $pass))          $passwordError .= "La contraseña debe tener al menos un símbolo especial.\n";

if (!empty($passwordError)) {
    echo json_encode(["type" => "error", "ack" => trim($passwordError), "field" => ["password", "rpassword"]]);
    exit;
}

// ── Preparación de los datos ───────────────────────────
$nombre_apellido = $nombre . ' ' . $apellido;

// Creamos el objeto Usuario usando el constructor original
$usuario = new Usuario(0, $nombre_apellido, $user, $pass, $email, $telefono, $direccion, 3);

// Instanciamos el DAO pasándole el objeto $mysqli asegurado
$dao = new UsuarioDAO($mysqli);

// Llamamos a registrarUsuario que esta ubicado en UsuarioDAO
if ($dao->registrarUsuario($usuario)) {
    
    // Llamamos a loginUsuario donde se ubica en UsuarioDAO
    $usuarioCreado = $dao->loginUsuario($user);
    
    if ($usuarioCreado !== null) {
        $_SESSION['usuario_id'] = $usuarioCreado->getID();
        $_SESSION['user_id']    = $usuarioCreado->getID();
        $_SESSION['username']   = $usuarioCreado->getUserName();
        $_SESSION['rol_id']     = $usuarioCreado->getIdRol();
    }

    echo json_encode(["type" => "success", "ack" => "Usuario registrado con éxito."]);
} else {
    echo json_encode(["type" => "error", "ack" => "No se pudo registrar el usuario. El usuario o email ya existen."]);
}
?>