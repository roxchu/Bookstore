<?php
require_once __DIR__ . '/../models/Venta.php';

class VentaDAO {

    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
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
}