<?php

class DetalleVenta {

    // atributos
    private int   $idDetalle;
    private int   $idVenta;
    private int   $idProducto;
    private int   $cantidad;
    private float $precioUnitario;

    // constructor
    public function __construct(
        int   $idDetalle,
        int   $idVenta,
        int   $idProducto,
        int   $cantidad,
        float $precioUnitario
    ) {
        $this->idDetalle      = $idDetalle;
        $this->idVenta        = $idVenta;
        $this->idProducto     = $idProducto;
        $this->cantidad       = $cantidad;
        $this->precioUnitario = $precioUnitario;
    }

    // getters
    public function getIdDetalle(): int {
        return $this->idDetalle;
    }

    public function getIdVenta(): int {
        return $this->idVenta;
    }

    public function getIdProducto(): int {
        return $this->idProducto;
    }

    public function getCantidad(): int {
        return $this->cantidad;
    }

    public function getPrecioUnitario(): float {
        return $this->precioUnitario;
    }

    // setters
    public function setIdDetalle(int $idDetalle): void {
        $this->idDetalle = $idDetalle;
    }

    public function setIdVenta(int $idVenta): void {
        $this->idVenta = $idVenta;
    }

    public function setIdProducto(int $idProducto): void {
        $this->idProducto = $idProducto;
    }

    public function setCantidad(int $cantidad): void {
        $this->cantidad = $cantidad;
    }

    public function setPrecioUnitario(float $precioUnitario): void {
        $this->precioUnitario = $precioUnitario;
    }
}