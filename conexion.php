<?php 

$mysqli = new mysqli("localhost", "root", "", "books_store");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

?>