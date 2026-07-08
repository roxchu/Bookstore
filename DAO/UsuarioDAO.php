<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioDAO {

    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // convierte una fila de la BD en un objeto Usuario
    private function hidratar(array $fila): Usuario {
        return new Usuario(
            (int)   $fila['id'],
            (string)$fila['realname'],
            (string)$fila['username'],
            (string)$fila['pass'],
            (string)$fila['email'],
            (string)$fila['telefono'],
            (string)$fila['direccion'],
            (int)   $fila['rol_id']
        );
    }

    // Método para registrar el usuario 
    // El rol viene del objeto Usuario: así lo puede usar tanto el registro
    // público (rol Cliente) como la creación de admins (rol Admin).
    public function registrarUsuario(Usuario $u): bool {
        $passHash  = password_hash($u->getPass(), PASSWORD_DEFAULT);
        $rolId     = $u->getIdRol();

        $stmt = $this->conexion->prepare(
            "INSERT INTO usuarios (realname, username, pass, email, telefono, direccion, rol_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $realname  = $u->getRealName();
        $username  = $u->getUserName();
        $email     = $u->getEmail();
        $telefono  = $u->getTelefono();
        $direccion = $u->getDireccion();

        $stmt->bind_param("ssssssi", $realname, $username, $passHash, $email, $telefono, $direccion, $rolId);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    // Método para loguear/buscar al usuario tras registrarse
    public function loginUsuario(string $username): ?Usuario {
        $stmt = $this->conexion->prepare(
            "SELECT id, realname, username, pass, email, telefono, direccion, rol_id
             FROM usuarios WHERE username = ? OR email = ?"
        );
        
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $stmt->close();
            return $this->hidratar($fila);
        }

        $stmt->close();
        return null;
    }

    // trae un usuario por id — usado por guardar_venta.php para armar el ticket
    public function getById(int $id): ?Usuario {
        $stmt = $this->conexion->prepare(
            "SELECT id, realname, username, pass, email, telefono, direccion, rol_id
             FROM usuarios WHERE id = ?"
        );

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $stmt->close();
            return $this->hidratar($fila);
        }

        $stmt->close();
        return null;
    }

    // borra un usuario existente por username o email — usado por insertar_admin.php
    // para evitar duplicados antes de crear un admin
    public function eliminarPorUsernameOEmail(string $username, string $email): bool {
        $stmt = $this->conexion->prepare(
            "DELETE FROM usuarios WHERE username = ? OR email = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ss", $username, $email);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    // busca un usuario por email — usado por olvideContrasenia.php
    public function getByEmail(string $email): ?Usuario {
        $stmt = $this->conexion->prepare(
            "SELECT id, realname, username, pass, email, telefono, direccion, rol_id
             FROM usuarios WHERE email = ?"
        );

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $stmt->close();
            return $this->hidratar($fila);
        }

        $stmt->close();
        return null;
    }

    // actualiza la contraseña de un usuario — usado por olvideContrasenia.php
    // recibe la contraseña en texto plano y la hashea acá, igual que registrarUsuario
    public function actualizarPassword(int $id, string $passPlano): bool {
        $passHash = password_hash($passPlano, PASSWORD_DEFAULT);

        $stmt = $this->conexion->prepare(
            "UPDATE usuarios SET pass = ? WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $passHash, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
}
?>
