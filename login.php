<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
    echo json_encode(['status' => 'error', 'message' => 'Completá todos los campos']);
    exit;
}

$stmt = $mysqli->prepare("SELECT id, username, pass, rol_id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($pass, $row['pass'])) {
        // Guardar sesión
        $_SESSION['usuario_id']  = $row['id'];
        $_SESSION['username']    = $row['username'];
        $_SESSION['rol_id']      = $row['rol_id'];

        // Redirigir según rol
        switch ($row['rol_id']) {
            case 1:
                $redirect = 'panel_admin.html';
                break;
            case 2:
                $redirect = 'panel_empleado.html';
                break;
            case 3:
                $redirect = 'index.html';
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
    echo json_encode(['status' => 'error', 'message' => 'Email no registrado']);
}

$stmt->close();
$mysqli->close();
?>