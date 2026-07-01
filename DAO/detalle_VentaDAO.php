<?php
require_once __DIR__ . '/../models/detalle_venta.php';

class DetalleVentaDAO {

    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // convierte una fila en objeto DetalleVenta
    private function hidratar(array $fila): DetalleVenta {
        return new DetalleVenta(
            (int)  $fila['id_detalle'],
            (int)  $fila['id_venta'],
            (int)  $fila['id_producto'],
            (int)  $fila['cantidad'],
            (float)$fila['precio_unitario']
        );
    }

    // INSERT de un ítem del carrito — se llama una vez por cada producto comprado
    public function registrarDetalle(DetalleVenta $detalle): bool {
        $stmt = $this->conexion->prepare(
            "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario)
             VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $idVenta        = $detalle->getIdVenta();
        $idProducto     = $detalle->getIdProducto();
        $cantidad       = $detalle->getCantidad();
        $precioUnitario = $detalle->getPrecioUnitario();

        // i=int d=double(float)
        $stmt->bind_param("iiid", $idVenta, $idProducto, $cantidad, $precioUnitario);

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    // trae todos los ítems de una venta por su id
    public function getByVenta(int $idVenta): array {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM detalle_ventas WHERE id_venta = ?"
        );
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $detalles = [];
        while ($fila = $resultado->fetch_assoc()) {
            $detalles[] = $this->hidratar($fila);
        }
        $stmt->close();
        return $detalles;
    }
}