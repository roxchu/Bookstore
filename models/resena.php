<?php

class Resena {

    // contenido de la clase Resena
    private int    $id;
    private int    $usuarioId;
    private int    $productoId;
    private string $comentario;

    // constructor
    public function __construct(
        int    $id,
        int    $usuarioId,
        int    $productoId,
        string $comentario
    ) {
        $this->id         = $id;
        $this->usuarioId  = $usuarioId;
        $this->productoId = $productoId;
        $this->comentario = $comentario;
    }

    // get para obtener el valor de un atributo
    public function getId(): int {
        return $this->id;
    }

    public function getUsuarioId(): int {
        return $this->usuarioId;
    }

    public function getProductoId(): int {
        return $this->productoId;
    }

    public function getComentario(): string {
        return $this->comentario;
    }

    // set para guardar el valor de un atributo
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setUsuarioId(int $usuarioId): void {
        $this->usuarioId = $usuarioId;
    }

    public function setProductoId(int $productoId): void {
        $this->productoId = $productoId;
    }

    public function setComentario(string $comentario): void {
        $this->comentario = $comentario;
    }
}