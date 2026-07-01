<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../DAO/UsuarioDAO.php';
require_once __DIR__ . '/../conexion.php';

// Conexión centralizada — la misma instancia mysqli que usan todas las vistas
$mysqli = Conexion::conectar();

$dao = new UsuarioDAO($mysqli);

// Función para no repetir código — ahora todo el SQL vive en UsuarioDAO
function crearAdmin(UsuarioDAO $dao, string $realname, string $username, string $password, string $email): void {
    // 1. Borramos si ya existe (para evitar errores de duplicado) usando el DAO
    $dao->eliminarPorUsernameOEmail($username, $email);

    // 2. Armamos el objeto Usuario con rol 1 (Admin); el hash lo hace el DAO
    $tel = "000";
    $dir = "Administración";
    $usuario = new Usuario(0, $realname, $username, $password, $email, $tel, $dir, 1);

    // 3. El INSERT vive en registrarUsuario()
    if ($dao->registrarUsuario($usuario)) {
        echo "✅ Admin <b>$username</b> creado con éxito.<br>";
    } else {
        echo "❌ Error creando a $username.<br>";
    }
}

crearAdmin($dao, 'Nicole Admin', 'nicole', 'admin123', 'nicole@gmail.com');
crearAdmin($dao, 'Denise Admin', 'denise', 'admin123', 'denise@gmail.com');
crearAdmin($dao, 'Rocio Admin',  'rocio',  'admin123', 'Rocioe@gmail.com');
?>