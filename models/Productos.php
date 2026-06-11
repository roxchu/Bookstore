<?php

class Producto {

    //atributos
    private int    $id;
    private string $nombre;
    private string $autor;
    private string $detalle;
    private float  $precio;
    private int    $stock;
    private int    $idGenero;
    private ?string $imagen;   // puede no tener imagen cargada

    //constructor
    public function __construct(
        int     $id,
        string  $nombre,
        string  $autor,
        string  $detalle,
        float   $precio,
        int     $stock,
        int     $idGenero,
        ?string $imagen = null
    ) {
        $this->id       = $id;
        $this->nombre   = $nombre;
        $this->autor    = $autor;
        $this->detalle  = $detalle;
        $this->precio   = $precio;
        $this->stock    = $stock;
        $this->idGenero = $idGenero;
        $this->imagen   = $imagen;
    }

    //getters
    public function getId(): int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getAutor(): string {
        return $this->autor;
    }

    public function getDetalle(): string {
        return $this->detalle;
    }

    public function getPrecio(): float {
        return $this->precio;
    }

    public function getStock(): int {
        return $this->stock;
    }

    public function getIdGenero(): int {
        return $this->idGenero;
    }

    public function getImagen(): ?string {
        return $this->imagen;
    }

    //setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setAutor(string $autor): void {
        $this->autor = $autor;
    }

    public function setDetalle(string $detalle): void {
        $this->detalle = $detalle;
    }

    public function setPrecio(float $precio): void {
        $this->precio = $precio;
    }

    public function setStock(int $stock): void {
        $this->stock = $stock;
    }

    public function setIdGenero(int $idGenero): void {
        $this->idGenero = $idGenero;
    }

    public function setImagen(?string $imagen): void {
        $this->imagen = $imagen;
    }
}