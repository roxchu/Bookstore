<?php
require_once __DIR__ . '/../models/Carrusel.php';

class CarruselDAO {
    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    // Obtener libros del carrusel activos
    public function obtenerLibrosCarrusel() {
        // Verificar si la tabla existe
        $sql_check = "SHOW TABLES LIKE 'carrusel'";
        $result_check = $this->conexion->query($sql_check);
        
        if ($result_check->num_rows === 0) {
            // La tabla no existe, crear tabla
            $this->crearTabla();
        }

        $sql = "SELECT p.id, p.nombre, p.autor, p.precio, p.imagen, c.posicion, c.id_producto
                FROM carrusel c
                JOIN producto p ON c.id_producto = p.id
                WHERE c.activo = 1
                ORDER BY c.posicion ASC
                LIMIT 6";
        
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            error_log("Error en CarruselDAO: " . $this->conexion->error);
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $libros = [];

        while ($row = $result->fetch_assoc()) {
            $libros[] = [
                'id' => $row['id'],
                'id_producto' => $row['id_producto'],
                'nombre' => $row['nombre'],
                'autor' => $row['autor'],
                'precio' => $row['precio'],
                'imagen' => $row['imagen'],
                'posicion' => $row['posicion']
            ];
        }

        $stmt->close();
        return $libros;
    }

    // Crear tabla si no existe
    private function crearTabla() {
        $sql = "CREATE TABLE IF NOT EXISTS carrusel (
            id INT PRIMARY KEY AUTO_INCREMENT,
            id_producto INT NOT NULL UNIQUE,
            posicion INT NOT NULL,
            activo BOOLEAN DEFAULT 1,
            FOREIGN KEY (id_producto) REFERENCES producto(id)
        )";
        
        $this->conexion->query($sql);
    }

    // Guardar carrusel completo
    public function guardarCarrusel($libros) {
        try {
            // Primero, desactivar todos
            $this->conexion->query("UPDATE carrusel SET activo = 0");
            
            // Luego, insertar/actualizar los nuevos
            foreach ($libros as $pos => $libro) {
                $posicion = $pos + 1;
                $id_producto = (int)$libro['id_producto'];
                
                $sql = "INSERT INTO carrusel (id_producto, posicion, activo) 
                        VALUES (?, ?, 1)
                        ON DUPLICATE KEY UPDATE posicion = ?, activo = 1";
                
                $stmt = $this->conexion->prepare($sql);
                if (!$stmt) {
                    error_log("Error preparando stmt: " . $this->conexion->error);
                    return false;
                }
                $stmt->bind_param('iii', $id_producto, $posicion, $posicion);
                if (!$stmt->execute()) {
                    error_log("Error ejecutando stmt: " . $stmt->error);
                    $stmt->close();
                    return false;
                }
                $stmt->close();
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error en guardarCarrusel: " . $e->getMessage());
            return false;
        }
    }

    // Agregar libro al carrusel
    public function agregarAlCarrusel($id_producto, $posicion) {
        $sql = "INSERT INTO carrusel (id_producto, posicion, activo) 
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE posicion = ?, activo = 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('iii', $id_producto, $posicion, $posicion);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    // Remover del carrusel (solo desactiva, no borra)
    public function removerDelCarrusel($id_producto) {
        $sql = "UPDATE carrusel SET activo = 0 WHERE id_producto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id_producto);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    // Actualizar posición
    public function actualizarPosicion($id_producto, $posicion) {
        $sql = "UPDATE carrusel SET posicion = ? WHERE id_producto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ii', $posicion, $id_producto);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }
}
?>
