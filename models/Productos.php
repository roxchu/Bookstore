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
    private ?string $imagen;
    private ?string $imagen2; 
    private ?string $imagen3;

    //constructor
    public function __construct(
        int     $id,
        string  $nombre,
        string  $autor,
        string  $detalle,
        float   $precio,
        int     $stock,
        int     $idGenero,
        ?string $imagen = null,
        ?string $imagen2 = null,
        ?string $imagen3 = null
    ) {
        $this->id       = $id;
        $this->nombre   = $nombre;
        $this->autor    = $autor;
        $this->detalle  = $detalle;
        $this->precio   = $precio;
        $this->stock    = $stock;
        $this->idGenero = $idGenero;
        $this->imagen   = $imagen;
        $this->imagen2  = $imagen2;
        $this->imagen3  = $imagen3;
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

    public function getImagen2(): ?string { 
        return $this->imagen2; 
    }

    public function getImagen3(): ?string { 
        return $this->imagen3; 
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

    public function setImagen2(?string $imagen2): void { 
        $this->imagen2 = $imagen2; 
    }
    
    public function setImagen3(?string $imagen3): void { 
        $this->imagen3 = $imagen3; 
    }
}

