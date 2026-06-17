<?php
require_once __DIR__ . '/../models/genero.php';

class GeneroDAO {

    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // convierte una fila en objeto Genero
    private function hidratar(array $fila): Genero {
        return new Genero(
            (int)   $fila['id_genero'],
            (string)$fila['nombre_genero']
        );
    }

    // trae todos los géneros — usado en el menú lateral de Libros.php
    public function getAll(): array {
        $resultado = $this->conexion->query(
            "SELECT * FROM genero ORDER BY nombre_genero ASC"
        );

        $generos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $generos[] = $this->hidratar($fila);
        }
        return $generos;
    }

    // trae un género por id — usado para mostrar el título en Libros.php
    public function getById(int $idGenero): ?Genero {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM genero WHERE id_genero = ?"
        );
        $stmt->bind_param("i", $idGenero);
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