<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    echo "No tienes permiso para acceder a esta página";
    exit();
}

// Incluir el archivo de conexión
require('../../php/conexion.php');

// Obtener datos del formulario
$passwordActual = $_POST['password_actual'];
$nuevaPassword = $_POST['nueva_password'];
$confirmarPassword = $_POST['confirmar_password'];

// Validar que la nueva contraseña y la confirmación coincidan
if ($nuevaPassword !== $confirmarPassword) {
    echo "La nueva contraseña y la confirmación no coinciden";
    exit();
}

// Obtener la contraseña actual del usuario
$idUsuario = $_SESSION['id_usuario'];
$sql = "SELECT contraseña FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $hashActual = $usuario['contraseña'];

    // Verificar si la contraseña actual es correcta
    if (!password_verify($passwordActual, $hashActual)) {
        echo "La contraseña actual es incorrecta";
        exit();
    }

    // Actualizar la contraseña
    $nuevaPasswordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $sqlUpdate = "UPDATE usuarios SET contraseña = ? WHERE id_usuario = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $nuevaPasswordHash, $idUsuario);

    if ($stmtUpdate->execute()) {
        echo "success";
    } else {
        echo "Error al actualizar la contraseña";
    }

    $stmtUpdate->close();
} else {
    echo "Usuario no encontrado";
}

$stmt->close();
$conn->close();
?>