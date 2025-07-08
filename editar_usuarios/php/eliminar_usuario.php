<?php
session_start();
if (!isset($_SESSION['correo']) || !isset($_SESSION['id_tipo_usuario']) || $_SESSION['id_tipo_usuario'] >= '3') {
    echo 'unauthorized';
    exit;
}

// Incluir el archivo de conexión
require('../../php/conexion.php');

$idUsuario = $_POST['id_usuario'];

// Eliminar el usuario
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $idUsuario);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'Error: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>