<?php
require_once 'conexion.php';

// ─── DATOS DEL ADMIN ─────────────────────────────────────────────────────────
$realname  = 'Nicole Admin';
$username  = 'nicole';
$email     = 'nicole@gmail.com';
$password  = 'admin1233';
$telefono  = '000000000';
$direccion = 'Administración';
$rol_id    = 1; // Admin
// ─────────────────────────────────────────────────────────────────────────────

// Verificar si ya existe
$check = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "⚠️ El usuario admin ya existe en la base de datos.";
    $check->close();
    $mysqli->close();
    exit;
}
$check->close();

$passHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO usuarios (realname, username, pass, email, telefono, direccion, rol_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssi", $realname, $username, $passHash, $email, $telefono, $direccion, $rol_id);

if ($stmt->execute()) {
    echo "✅ Admin creado correctamente.<br>";
    echo "📧 Email: <strong>$email</strong><br>";
    echo "🔑 Contraseña: <strong>$password</strong><br>";
    echo "<br>⚠️ <strong>Eliminá este archivo del servidor después de usarlo.</strong>";
} else {
    echo "❌ Error al crear el admin: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>