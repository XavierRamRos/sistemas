<?php
$servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "uneve_sistemas";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);

    // Establecer configuración regional y codificación de caracteres
    setlocale(LC_TIME, 'spanish');
    $conn->query("SET NAMES 'utf8'");

    // Establecer la zona horaria
    date_default_timezone_set("America/Mexico_City");

    return $conn;
}
