<?php
session_start();
require_once 'conexion.php';

$user = trim($_POST['username'] ?? '');
$pass = $_POST['password'] ?? '';

if ($user === '' || $pass === '') {
    echo "Completa todos los campos";
    exit;
}

// Consulta para obtener usuario y rol
$stmt = $mysqli->prepare("SELECT id, pass, rol_id FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($pass, $row['pass'])) {
        // Redirigir según rol
        $redirect = '';
        switch ($row['rol_id']) {
            case 1: // Admin
                $redirect = 'panel_admin.html';
                break;
            case 2: // Empleado
                $redirect = 'panel_empleado.html';
                break;
            case 3: // Cliente      
                $redirect = 'index.php';
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Rol no reconocido']);
                exit;
        }
        echo json_encode(['status' => 'success', 'redirect' => $redirect]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
    }
} else {
    echo "Usuario no encontrado";
}

$stmt->close();
$mysqli->close();
?>