<?php
$conexion = new mysqli("localhost", "root", "utom", "aiq");
if ($conexion->connect_error) {
    die("Error de conexión");
}
?>
