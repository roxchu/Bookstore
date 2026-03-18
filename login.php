<?php
// 1. Iniciamos sesión al principio (SOLO UNA VEZ)
session_start();

// 2. Apagamos errores visuales para que el JSON salga limpio (evita el error del < )
error_reporting(0);
ini_set('display_errors', 0);

require_once 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

$user = trim($_POST['username'] ?? '');
$pass = $_POST['password'] ?? '';

if ($user === '' || $pass === '') {
    echo json_encode(['status' => 'error', 'message' => 'Completa todos los campos']);
    exit;
}

// 3. Buscamos al usuario (Asegurate que en tu BD las columnas sean id, pass y rol_id)
$stmt = $mysqli->prepare("SELECT id, pass, rol_id FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    
    // Verificamos la contraseña encriptada
    if (password_verify($pass, $row['pass'])) {

       
        $_SESSION['usuario_id'] = $row['id']; 
        $_SESSION['user_id']    = $row['id']; 
        $_SESSION['username']   = $user;      
        $_SESSION['rol_id']     = $row['rol_id'];
       
        $redirect = '';
        switch ($row['rol_id']) {
            case 1: 
                $redirect = 'panel_admin.html';
                break;
            case 2: // Empleado
                $redirect = 'panel_empleado.html';
                break;
            case 3: // Cliente      
                $redirect = 'index.php';
                break;
            default:
                $redirect = 'index.php';
        }

        echo json_encode(['status' => 'success', 'redirect' => $redirect]);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
}

$stmt->close();
$mysqli->close();
?>