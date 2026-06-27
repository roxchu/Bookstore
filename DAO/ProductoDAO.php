<?php
require_once __DIR__ . '/../models/Productos.php';

class ProductoDAO {

    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // convierte una fila en objeto Producto
    private function hidratar(array $fila): Producto {
        return new Producto(
            (int)   $fila['id'],
            (string)$fila['nombre'],
            (string)$fila['autor'],
            (string)$fila['detalle'],
            (float) $fila['precio'],
            (int)   $fila['stock'],
            (int)   $fila['id_genero'],
            $fila['imagen'] ?? null
        );
    }

    // trae todos los productos
    public function getAll(): array {
        $resultado = $this->conexion->query(
            "SELECT * FROM producto ORDER BY id DESC"
        );
        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $this->hidratar($fila);
        }
        return $productos;
    }

    // filtra por género
    public function getByGenero(int $idGenero): array {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM producto WHERE id_genero = ? ORDER BY id DESC"
        );
        $stmt->bind_param("i", $idGenero);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $this->hidratar($fila);
        }
        $stmt->close();
        return $productos;
    }

    // busca por nombre o autor
    public function search(string $term): array {
        $like = "%$term%";
        $stmt = $this->conexion->prepare(
            "SELECT * FROM producto WHERE nombre LIKE ? OR autor LIKE ? ORDER BY id DESC"
        );
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $this->hidratar($fila);
        }
        $stmt->close();
        return $productos;
    }

    // INSERT si id es 0, UPDATE si tiene id
    public function save(Producto $producto): bool {
        if ($producto->getId() === 0) {
            $stmt = $this->conexion->prepare(
                "INSERT INTO producto (nombre, autor, detalle, precio, stock, id_genero, imagen)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $nombre   = $producto->getNombre();
            $autor    = $producto->getAutor();
            $detalle  = $producto->getDetalle();
            $precio   = $producto->getPrecio();
            $stock    = $producto->getStock();
            $idGenero = $producto->getIdGenero();
            $imagen   = $producto->getImagen();
            // s=string d=double(float) i=int
            $stmt->bind_param("sssdiss", $nombre, $autor, $detalle, $precio, $stock, $idGenero, $imagen);
        } else {
            $stmt = $this->conexion->prepare(
                "UPDATE producto SET nombre=?, autor=?, detalle=?, precio=?, stock=?, id_genero=?, imagen=?
                 WHERE id=?"
            );
            $nombre   = $producto->getNombre();
            $autor    = $producto->getAutor();
            $detalle  = $producto->getDetalle();
            $precio   = $producto->getPrecio();
            $stock    = $producto->getStock();
            $idGenero = $producto->getIdGenero();
            $imagen   = $producto->getImagen();
            $id       = $producto->getId();
            // s=string d=double(float) i=int
            $stmt->bind_param("sssdisi", $nombre, $autor, $detalle, $precio, $stock, $idGenero, $imagen, $id);
        }

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    // elimina por id
    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare(
            "DELETE FROM producto WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Busca por término dentro de un género específico o en general
    public function buscarYFiltrar(string $term, int $idGenero = 0): array {
        $like = "%$term%";
        
        if ($idGenero > 0) {
            $stmt = $this->conexion->prepare(
                "SELECT * FROM producto WHERE id_genero = ? AND (nombre LIKE ? OR autor LIKE ?) ORDER BY id DESC"
            );
            $stmt->bind_param("iss", $idGenero, $like, $like);
        } else {
            $stmt = $this->conexion->prepare(
                "SELECT * FROM producto WHERE nombre LIKE ? OR autor LIKE ? ORDER BY id DESC"
            );
            $stmt->bind_param("ss", $like, $like);
        }
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $this->hidratar($fila);
        }
        $stmt->close();
        return $productos;
    }
}