<?php
require_once 'conexion.php';

// Función para no repetir código
function crearAdmin($mysqli, $realname, $username, $password, $email) {
    // 1. Borrar si ya existe (para evitar errores de duplicado)
    $mysqli->query("DELETE FROM usuarios WHERE username = '$username' OR email = '$email'");

    // 2. Encriptar contraseña
    $passHash = password_hash($password, PASSWORD_DEFAULT);
    $rol_id = 1; // Admin
    $tel = "000";
    $dir = "Administración";


    $stmt = $mysqli->prepare("INSERT INTO usuarios (realname, username, pass, email, telefono, direccion, rol_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $realname, $username, $passHash, $email, $tel, $dir, $rol_id);
    
    if ($stmt->execute()) {
        echo "✅ Admin <b>$username</b> creado con éxito.<br>";
    } else {
        echo "❌ Error con $username: " . $mysqli->error . "<br>";
    }
    $stmt->close();
}


crearAdmin($mysqli, 'Nicole Admin', 'nicole', 'admin123', 'nicole@gmail.com');
crearAdmin($mysqli, 'Denise Admin', 'denise', 'admin123', 'denise@gmail.com');
crearAdmin($mysqli, 'Rocio Admin',  'rocio',  'admin123', 'Rocioe@gmail.com');

$mysqli->close();
?>