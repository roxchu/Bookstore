<?php
require_once __DIR__ . '/../models/Rol.php';

class RolDAO {

    private mysqli $conexion;

    // Constructor
    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    private function hidratar(array $fila): Rol {
        return new Rol(
            (int)   $fila['id_rol'],
            (string)$fila['nombre_rol'],
            (string)$fila['rol_descripcion']
        );
    }

    public function getAll(): array {
        $resultado = $this->conexion->query("SELECT * FROM rol ORDER BY id_rol ASC");

        $roles = [];
        while ($fila = $resultado->fetch_assoc()) {
            $roles[] = $this->hidratar($fila);
        }
        return $roles;
    }

    // Busca un rol específico por su ID
    public function getById(int $idRol): ?Rol {
        $stmt = $this->conexion->prepare("SELECT * FROM rol WHERE id_rol = ?");
        $stmt->bind_param("i", $idRol);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $stmt->close();
            return $this->hidratar($fila);
        }

        $stmt->close();
        return null; // Si no existe el rol
    }
}
?>