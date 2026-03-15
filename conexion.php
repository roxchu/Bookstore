<?php 

$mysqli = new mysqli("localhost", "root", "", "bookstore");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

?>