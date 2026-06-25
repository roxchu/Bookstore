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
    public function registrarUsuario(Usuario $u): bool {
        $passHash  = password_hash($u->getPass(), PASSWORD_DEFAULT);
        $rolId     = 3; // rol por defecto: Cliente

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
             FROM usuarios WHERE username = ?"
        );
        
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $username);
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
}
?>