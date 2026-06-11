<?php
require_once __DIR__ . '/../model/Usuario.php';

class UsuarioDAO {

    private mysqli $conexion;

    // ── Constructor ────────────────────────────────────────────
    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // ── Método privado: convierte una fila en objeto Usuario ───
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

    // ── registrarUsuario: INSERT del nuevo usuario ─────────────
    public function registrarUsuario(Usuario $u): bool {

        // El hash se hace acá antes de insertar
        $passHash = password_hash($u->getPass(), PASSWORD_DEFAULT);
        $rolId    = 3; // rol por defecto: Cliente

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

    // ── loginUsuario: busca un usuario por username ────────────
    // Devuelve el objeto Usuario o null si no existe
    // El password_verify() lo hace la VISTA con $usuario->getPass()
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
        return null; // usuario no encontrado
    }
}
?>