<?php
header('Content-Type: application/json');
session_start();

// Validar que el usuario sea administrador
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
    exit;
}

try {
    // Conexión 
    $pdo = new PDO('mysql:host=localhost;dbname=books_store;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Listar los géneros
if ($action === 'list') {
    try {
        // BD
        $stmt = $pdo->query("SELECT id_genero, nombre_genero, destacado FROM genero ORDER BY id_genero ASC");
        $generos = $stmt->fetchAll();
        
        echo json_encode(['status' => 'success', 'generos' => $generos]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error SQL: ' . $e->getMessage()]);
    }
    exit;
}

// ACCIÓN: Guardar los cambios desde el panel
if ($action === 'update_destacados') {
    try {
        $destacados = json_decode($_POST['destacados'] ?? '[]');

        // Validación de respaldo: Máximo 3
        if (count($destacados) > 3) {
            echo json_encode(['status' => 'error', 'message' => 'Límite excedido. Máximo de 3 géneros destacados.']);
            exit;
        }

        $pdo->beginTransaction();

        // CORRECCIÓN AQUÍ: Reseteamos tu tabla 'genero'
        $pdo->query("UPDATE genero SET destacado = 0");

        // Activamos los seleccionados usando 'id_genero'
        if (count($destacados) > 0) {
            $placeholders = implode(',', array_fill(0, count($destacados), '?'));
            $stmt = $pdo->prepare("UPDATE genero SET destacado = 1 WHERE id_genero IN ($placeholders)");
            $stmt->execute($destacados);
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Géneros destacados actualizados con éxito.']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar: ' . $e->getMessage()]);
    }
    exit;
}
// CREAR NUEVO GÉNERO
if ($action === 'create_genero') {
    try {
        $nombre = $_POST['nombre'] ?? '';

        if (empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre es requerido']);
            exit;
        }

        // Verificar que no exista
        $stmt = $pdo->prepare("SELECT id_genero FROM genero WHERE nombre_genero = ?");
        $stmt->execute([$nombre]);
        
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'El género ya existe']);
            exit;
        }

        // Insertar
        $stmt = $pdo->prepare("INSERT INTO genero (nombre_genero, destacado) VALUES (?, 0)");
        $stmt->execute([$nombre]);

        echo json_encode(['status' => 'success', 'message' => 'Género creado']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}