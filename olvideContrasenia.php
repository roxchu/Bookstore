<?php
require_once 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

$email = trim($_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Por favor ingresa un email válido.']);
    exit;
}

$stmt = $mysqli->prepare("SELECT id, username, pass FROM usuarios WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'No se encontró una cuenta con ese email.']);
    $stmt->close();
    $mysqli->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Envío de email con la contraseña (hash) tal cual está en la base de datos
$to = $email;
$subject = 'Recuperación de contraseña - Bookstore';
$message = "Hola {$user['username']},\n\n" .
           "Se solicitó recuperar tu contraseña. En la base de datos está guardada como:\n\n" .
           "{$user['pass']}\n\n" .
           "Ten en cuenta que es una contraseña hasheada y no puede usarse tal cual en login.\n\n" .
           "Saludos.\n";
$headers = 'From: no-reply@localhost' . "\r\n" .
           'Reply-To: no-reply@localhost' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

$mailSent = mail($to, $subject, $message, $headers);

if ($mailSent) {
    echo json_encode(['success' => true, 'message' => 'Te hemos enviado un email con nueva contraseña temporal. Revisá tu bandeja.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo enviar el correo. Volvé a intentar más tarde.']);
}

$mysqli->close();
?>