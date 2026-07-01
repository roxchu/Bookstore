<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';
require_once __DIR__ . '/../models/Usuario.php';

header('Content-Type: application/json; charset=utf-8');

// Conexión centralizada — la misma instancia mysqli que usan todas las vistas
$mysqli = Conexion::conectar();

// ── Validación del email (se queda en la vista) ────────────────
$email = trim($_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Por favor ingresá un email válido.']);
    exit;
}

// ── Llamada al DAO ─────────────────────────────────────────────
$dao     = new UsuarioDAO($mysqli);
$usuario = $dao->getByEmail($email);

if ($usuario === null) {
    echo json_encode(['success' => false, 'message' => 'No se encontró una cuenta con ese email.']);
    exit;
}

// ── Genera contraseña temporal y la actualiza (vista coordina) ─
$passTemp  = bin2hex(random_bytes(5)); // ej: "a3f9c12b4e"
$dao->actualizarPassword($usuario->getId(), $passTemp);

// ── Envío de email (lógica de presentación, se queda en la vista)
$to      = $email;
$subject = 'Recuperación de contraseña - Bookstore';
$message = "Hola {$usuario->getUserName()},\n\n" .
           "Tu contraseña temporal es: {$passTemp}\n\n" .
           "Por favor ingresá con ella y cambiala cuanto antes.\n\n" .
           "Saludos,\nBookstore";
$headers = 'From: no-reply@localhost' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Te enviamos una contraseña temporal al email.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo enviar el correo. Intentá más tarde.']);
}
?>