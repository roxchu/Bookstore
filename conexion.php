<?php
class Conexion {
    private static $host = "localhost";
    private static $user = "root";
    private static $pass = "";
    private static $bd = "books_store";
    private static $link = null;

    public static function conectar() {
        // Usamos el patrón Singleton para no abrir mil conexiones si ya hay una abierta
        if (self::$link === null) {
            try {
                // Configuramos PDO con el charset utf8 para que no rompa con acentos o eñes
                self::$link = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$bd . ";charset=utf8",
                    self::$user,
                    self::$pass
                );
                
                // Le decimos a PDO que lance excepciones si hay errores de SQL
                self::$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } catch (PDOException $e) {
                die("Error crítico en la conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$link;
    }
}
?>