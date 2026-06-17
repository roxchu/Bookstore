<?php
require_once __DIR__ . '/../models/Resena.php';

class ResenaDAO {

    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // Convierte una fila de la BD en objeto Resena
    private function hidratar(array $fila): Resena {
        return new Resena(
            (int)   $fila['id'],
            (int)   $fila['usuario_id'],
            (int)   $fila['producto_id'],
            (string)$fila['comentario']
        );
    }

    // Inserta una nueva reseña 
    public function registrarResena(Resena $r): bool {
        $stmt = $this->conexion->prepare(
            "INSERT INTO reseña (usuario_id, producto_id, comentario) VALUES (?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $usuarioId  = $r->getUsuarioId();
        $productoId = $r->getProductoId();
        $comentario = $r->getComentario();

        $stmt->bind_param("iis", $usuarioId, $productoId, $comentario);

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    // Obtiene todas las reseñas de un producto 
    public function getByProducto(int $productoId): array {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM reseña WHERE producto_id = ? ORDER BY id DESC"
        );
        $stmt->bind_param("i", $productoId);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $reseñas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $reseñas[] = $this->hidratar($fila);
        }
        $stmt->close();
        return $reseñas;
    }

    // Eliminar una reseña por su ID
    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM reseña WHERE id = ?");
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
}
?>