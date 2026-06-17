<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioDAO {

    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // convierte una fila en objeto Usuario
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

    // INSERT del nuevo usuario
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

        $stmt->bind_param(
            "ssssssi",
            $realname, $username, $passHash, $email, $telefono, $direccion, $rolId
        );

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    // busca usuario por username y devuelve el objeto, o null si no existe
    // el password_verify() lo hace la VISTA: if (password_verify($input, $usuario->getPass()))
    public function loginUsuario(string $username): ?Usuario {
        $stmt = $this->conexion->prepare(
            "SELECT id, realname, username, pass, email, telefono, direccion, rol_id
             FROM usuarios WHERE username = ?"
        );
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

    // busca usuario por email — usado en olvideContrasenia.php
    public function getByEmail(string $email): ?Usuario {
        $stmt = $this->conexion->prepare(
            "SELECT id, realname, username, pass, email, telefono, direccion, rol_id
             FROM usuarios WHERE email = ?"
        );
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

    // actualiza la contraseña — usado en olvideContrasenia.php
    // hashea la contraseña nueva antes de guardarla
    public function actualizarPassword(int $id, string $passNueva): bool {
        $passHash = password_hash($passNueva, PASSWORD_DEFAULT);

        $stmt = $this->conexion->prepare(
            "UPDATE usuarios SET pass = ? WHERE id = ?"
        );
        $stmt->bind_param("si", $passHash, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
}