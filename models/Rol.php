<?php

class Rol {

    //contenido de la clase Rol
    private int    $idRol;
    private string $nombreRol;
    private string $rolDescripcion;

    //CONSTRUCTOR
    public function __construct(
        int    $idRol,
        string $nombreRol,
        string $rolDescripcion
    ) {
        $this->idRol          = $idRol;
        $this->nombreRol      = $nombreRol;
        $this->rolDescripcion = $rolDescripcion;
    }

    // get para obtener el valor de un atributo
    public function getIdRol(): int {
        return $this->idRol;
    }

    public function getNombreRol(): string {
        return $this->nombreRol;
    }

    public function getRolDescripcion(): string {
        return $this->rolDescripcion;
    }

    
    // set para guardar el valor de un atributo
    public function setIdRol(int $idRol): void {
        $this->idRol = $idRol;
    }

    public function setNombreRol(string $nombreRol): void {
        $this->nombreRol = $nombreRol;
    }

    public function setRolDescripcion(string $rolDescripcion): void {
        $this->rolDescripcion = $rolDescripcion;
    }
}