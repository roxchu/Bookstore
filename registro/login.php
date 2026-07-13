<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';

$conexion = Conexion::conectar();
$usuarioDAO = new UsuarioDAO($conexion);

$response = [
    'status' => 'error',
    'message' => 'Datos inválidos'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email) && !empty($password)) {
        // Buscar usuario por email
        $usuario = $usuarioDAO->buscarPorEmail($email);

        if ($usuario && password_verify($password, $usuario->getPassword())) {
            $_SESSION['usuario_id'] = $usuario->getIdUsuario();
            $_SESSION['username'] = $usuario->getNombreUsuario();
            $_SESSION['rol_id'] = $usuario->getIdRol();
            $_SESSION['email'] = $usuario->getEmail();
            
            $response = [
                'status' => 'success',
                'message' => 'Sesión iniciada',
                'redirectUrl' => '../index.php'
            ];
        } else {
            $response['message'] = 'Email o contraseña incorrectos.';
        }
    } else {
        $response['message'] = 'Email y contraseña son requeridos.';
    }
}

echo json_encode($response);
?>