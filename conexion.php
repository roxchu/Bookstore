<?php
class Conexion {
    private static $host = "localhost";
    private static $user = "root";
    private static $pass = "";
    private static $bd   = "books_store";
    private static ?mysqli $link = null;

    // Patrón Singleton: si ya hay una conexión abierta, la reusamos en vez
    // de abrir una nueva cada vez que un archivo hace require_once acá.
    // Todos los DAO están armados sobre mysqli (no PDO), así que la
    // conexión centralizada también es mysqli para no tener que
    // reescribir ningún DAO.
    public static function conectar(): mysqli {
        if (self::$link === null) {
            self::$link = new mysqli(self::$host, self::$user, self::$pass, self::$bd);

            if (self::$link->connect_error) {
                die("Error crítico en la conexión a la base de datos: " . self::$link->connect_error);
            }

            // Charset utf8mb4 para que no rompa con acentos, eñes ni emojis
            self::$link->set_charset("utf8mb4");
        }
        return self::$link;
    }
}
?>