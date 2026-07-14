<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';
require_once __DIR__ . '/Mailer.php';

$conexion = Conexion::conectar();
$usuarioDAO = new UsuarioDAO($conexion);

$response = [
    'type' => 'error',
    'ack' => 'Error en el registro'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $password = isset($_POST['pass']) ? $_POST['pass'] : '';
    $rpassword = isset($_POST['rpass']) ? $_POST['rpass'] : '';

    // Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($email) || empty($telefono) || empty($password)) {
        $response['ack'] = 'Todos los campos son obligatorios.';
        echo json_encode($response);
        exit;
    }

    if ($password !== $rpassword) {
        $response['ack'] = 'Las contraseñas no coinciden.';
        echo json_encode($response);
        exit;
    }

    // Verificar que el email no exista
    if ($usuarioDAO->emailExiste($email)) {
        $response['ack'] = 'Este email ya está registrado.';
        echo json_encode($response);
        exit;
    }

    // Crear código de verificación
    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Guardar datos temporales en sesión
    $_SESSION['registro_temp'] = [
        'nombre' => $nombre,
        'apellido' => $apellido,
        'email' => $email,
        'telefono' => $telefono,
        'password' => $password, // se hashea recién en UsuarioDAO::registrarUsuario
        'codigo' => $codigo,
        'codigo_timestamp' => time()
    ];

    // Enviar email con código
    $mailError = null;
    if (enviarCodigoRegistro($email, $nombre, $codigo, $mailError)) {
        $response = [
            'type' => 'code_sent',
            'ack' => 'Se envió un código de verificación a tu email.'
        ];
    } else {
        $response['ack'] = $mailError ?: 'No se pudo enviar el email. Intenta más tarde.';
    }
}

echo json_encode($response);
?>