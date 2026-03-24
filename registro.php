<?php
// Inicia sesión al principio
session_start();

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
    $validationOutput = array("type" => "error", "ack" => "Por favor, complete todos los campos.");
    echo json_encode($validationOutput);
    exit;
}

// Validación adicional para email (ejemplo básico)
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $validationOutput = array("type" => "error", "ack" => "El email no es válido.");
    echo json_encode($validationOutput);
    exit;
}

// Validación de contraseña
$passwordError = "";
if (!empty($_POST["pass"]) && !empty($_POST["rpass"])) {
    $pass = htmlspecialchars($_POST["pass"]);
    $rpass = htmlspecialchars($_POST["rpass"]);
    if ($pass != $rpass) {
        $passwordError .= "Las contraseñas no coinciden. \n";
    }
    if (strlen($pass) < 8) {  
        $passwordError .= "La contraseña debe tener al menos 8 caracteres. \n";
    }
    if (!preg_match("#[0-9]+#", $pass)) {
        $passwordError .= "La contraseña debe tener al menos un número. (0-9) \n";
    }
    if (!preg_match("#[A-Z]+#", $pass)) {
        $passwordError .= "La contraseña debe tener al menos una mayúscula. (A-Z)\n";
    }
    if (!preg_match("#[a-z]+#", $pass)) {
        $passwordError .= "La contraseña debe tener al menos una minúscula. (a-z) \n";
    }
    if (!preg_match("#[^\w]+#", $pass)) {
        $passwordError .= "La contraseña debe tener al menos un símbolo especial. \n";
    }
} else {
    $passwordError .= "Ingrese la contraseña y confírmela. \n";
}

if (!empty($passwordError)) {
    $validationOutput = array("type" => "error", "ack" => nl2br($passwordError));
    echo json_encode($validationOutput);
    exit;
}

// Si todo es válido, proceder con el registro
require_once 'conexion.php';

// Hashear contraseña usando password_hash()
$passHash = password_hash($pass, PASSWORD_DEFAULT);

// Unir nombre y apellido para la columna existente
$nombre_apellido = $nombre . ' ' . $apellido;

$rol_id = 3;  // Valor por defecto para Cliente
$stmt = $mysqli->prepare("INSERT INTO usuarios (realname, username, pass, email, telefono, direccion, rol_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    // Mostrar el error real de MySQL para ayudar en el debugging.
    $validationOutput = array("type" => "error", "ack" => "Error en la base de datos: " . $mysqli->error);
    echo json_encode($validationOutput);
    exit;
}

$stmt->bind_param("ssssssi", $nombre_apellido, $user, $passHash, $email, $telefono, $direccion, $rol_id);

if ($stmt->execute()) {
    // Obtener el ID del usuario recién insertado
    $usuario_id = $mysqli->insert_id;
    
    // Iniciar sesión automáticamente
    $_SESSION['usuario_id'] = $usuario_id;
    $_SESSION['user_id']    = $usuario_id;
    $_SESSION['username']   = $user;
    $_SESSION['rol_id']     = $rol_id;
    
    $validationOutput = array("type" => "success", "ack" => "Usuario registrado con éxito.");
    echo json_encode($validationOutput);
} else {
    $validationOutput = array("type" => "error", "ack" => "No se pudo registrar el usuario: " . $stmt->error);
    echo json_encode($validationOutput);
}

$stmt->close();
$mysqli->close();
?>