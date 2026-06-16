<?php

class Genero {

    // atributos
    private int    $idGenero;
    private string $nombreGenero;

    // constructor
    public function __construct(
        int    $idGenero,
        string $nombreGenero
    ) {
        $this->idGenero     = $idGenero;
        $this->nombreGenero = $nombreGenero;
    }

    // getters
    public function getIdGenero(): int {
        return $this->idGenero;
    }

    public function getNombreGenero(): string {
        return $this->nombreGenero;
    }

    // setters
    public function setIdGenero(int $idGenero): void {
        $this->idGenero = $idGenero;
    }

    public function setNombreGenero(string $nombreGenero): void {
        $this->nombreGenero = $nombreGenero;
    }
}