<?php
require_once __DIR__ . '/../models/Venta.php';

class VentaDAO {

    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // convierte una fila de la BD en un objeto Venta
    private function hidratar(array $fila): Venta {
        return new Venta(
            (int)   $fila['id_venta'],
            (int)   $fila['id_usuario'],
            (float) $fila['total'],
            (string)$fila['fecha'],
            (string)($fila['metodo_pago'] ?? '')
        );
    }

    // INSERT de la venta, devuelve el id generado (para armar el ticket)
    // la fecha la maneja MySQL con NOW(), no viene del objeto
    public function registrarVenta(Venta $v): int {
        $stmt = $this->conexion->prepare(
            "INSERT INTO ventas (id_usuario, total, metodo_pago)
             VALUES (?, ?, ?)"
        );

        if (!$stmt) {
            return 0;
        }

        $idUsuario  = $v->getIdUsuario();
        $total      = $v->getTotal();
        $metodoPago = $v->getMetodoPago();

        // i=int d=double(float) s=string
        $stmt->bind_param("ids", $idUsuario, $total, $metodoPago);

        if ($stmt->execute()) {
            $idGenerado = $this->conexion->insert_id;
            $stmt->close();
            return $idGenerado; // la vista lo usa para armar el ticket
        }

        $stmt->close();
        return 0; // 0 indica que falló
    }

    // trae todas las ventas de un usuario — usado por mis_compras.php
    public function listarPorUsuario(int $idUsuario): array {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM ventas WHERE id_usuario = ? ORDER BY id_venta DESC"
        );

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $ventas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ventas[] = $this->hidratar($fila);
        }
        $stmt->close();
        return $ventas;
    }
}