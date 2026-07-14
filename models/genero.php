<?php

class Genero {

    // atributos
    private int    $idGenero;
    private string $nombreGenero;
    private ?string $imagen;

    // constructor
    public function __construct(
        int    $idGenero,
        string $nombreGenero,
        ?string $imagen = null
    ) {
        $this->idGenero     = $idGenero;
        $this->nombreGenero = $nombreGenero;
        $this->imagen       = $imagen;
    }

    // getters
    public function getIdGenero(): int {
        return $this->idGenero;
    }

    public function getNombreGenero(): string {
        return $this->nombreGenero;
    }

    public function getImagen(): ?string {
        return $this->imagen;
    }

    // setters
    public function setIdGenero(int $idGenero): void {
        $this->idGenero = $idGenero;
    }

    public function setNombreGenero(string $nombreGenero): void {
        $this->nombreGenero = $nombreGenero;
    }

    public function setImagen(?string $imagen): void {
        $this->imagen = $imagen;
    }
}