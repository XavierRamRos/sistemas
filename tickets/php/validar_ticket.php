<?php
session_start();

if (!isset($_SESSION['correo'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit;
}

date_default_timezone_set('America/Mexico_City');

// Incluir el archivo de conexión
require('../../php/conexion.php');

$ticketId = $conn->real_escape_string($_POST['ticketId']);
$accion = $_POST['accion'];
$fecha_validacion = date('Y-m-d H:i:s'); // Agregamos la fecha y hora actual

if ($accion === 'calificar') {
    $calificacion = $conn->real_escape_string($_POST['calificacion']);
    
    $sql = "UPDATE ticket SET 
            calificacion = ?, 
            status = '3',
            fecha_validacion = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $calificacion, $fecha_validacion, $ticketId);
    
} elseif ($accion === 'comentar') {
    $comentario = $conn->real_escape_string($_POST['comentario']);
    
    $sql = "UPDATE ticket SET 
            comentario = ?, 
            status = '0',
            fecha_validacion = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $comentario, $fecha_validacion, $ticketId);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Información actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la información: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>