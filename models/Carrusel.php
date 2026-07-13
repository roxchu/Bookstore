<?php
class Carrusel {
    private int $id;
    private int $id_producto;
    private int $posicion;
    private bool $activo;

    public function __construct(int $id, int $id_producto, int $posicion, bool $activo = true) {
        $this->id = $id;
        $this->id_producto = $id_producto;
        $this->posicion = $posicion;
        $this->activo = $activo;
    }

    public function getId(): int { return $this->id; }
    public function getIdProducto(): int { return $this->id_producto; }
    public function getPosicion(): int { return $this->posicion; }
    public function isActivo(): bool { return $this->activo; }

    public function setIdProducto(int $id): void { $this->id_producto = $id; }
    public function setPosicion(int $pos): void { $this->posicion = $pos; }
    public function setActivo(bool $activo): void { $this->activo = $activo; }
}
?>