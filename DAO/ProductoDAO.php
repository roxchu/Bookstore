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
            $fila['imagen'] ?? null,
            $fila['imagen2'] ?? null,
            $fila['imagen3'] ?? null
        );
    }

    // trae un solo producto por id — usado por admin_productos.php al editar
    public function getById(int $id): ?Producto {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM producto WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $stmt->close();
            return $this->hidratar($fila);
        }

        $stmt->close();
        return null;
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
        if (strlen($term) < 2) {
            return []; // Requiere mínimo 2 caracteres
        }
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
                "INSERT INTO producto (nombre, autor, detalle, precio, stock, id_genero, imagen, imagen2, imagen3)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $nombre   = $producto->getNombre();
            $autor    = $producto->getAutor();
            $detalle  = $producto->getDetalle();
            $precio   = $producto->getPrecio();
            $stock    = $producto->getStock();
            $idGenero = $producto->getIdGenero();
            $imagen   = $producto->getImagen();
            $imagen2  = $producto->getImagen2();
            $imagen3  = $producto->getImagen3();
            $stmt->bind_param("sssdiisss", $nombre, $autor, $detalle, $precio, $stock, $idGenero, $imagen, $imagen2, $imagen3);
        } else {
            $stmt = $this->conexion->prepare(
                "UPDATE producto SET nombre=?, autor=?, detalle=?, precio=?, stock=?, id_genero=?, imagen=?, imagen2=?, imagen3=?
                 WHERE id=?"
            );
            $nombre   = $producto->getNombre();
            $autor    = $producto->getAutor();
            $detalle  = $producto->getDetalle();
            $precio   = $producto->getPrecio();
            $stock    = $producto->getStock();
            $idGenero = $producto->getIdGenero();
            $imagen   = $producto->getImagen();
            $imagen2  = $producto->getImagen2();
            $imagen3  = $producto->getImagen3();
            $id       = $producto->getId();
            $stmt->bind_param("sssdiisssi", $nombre, $autor, $detalle, $precio, $stock, $idGenero, $imagen, $imagen2, $imagen3, $id);
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
        // Solo validar si hay un término de búsqueda
        if (!empty($term) && strlen($term) < 2) {
            return []; // Solo rechazar si escribió algo MUY corto
        }
        
        // Preparar el LIKE solo si hay búsqueda
        $like = !empty($term) ? "%$term%" : "%";
        
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
