<?php
// restaurar_ticket.php

header('Content-Type: application/json');

// Incluir la conexión a la base de datos
include '../../php/conexion.php';

// Obtener el ID del ticket desde la solicitud
$ticketId = $_POST['ticketId'];

// Verificar si el ID del ticket es válido
if (!isset($ticketId) || empty($ticketId)) {
    echo json_encode(['success' => false, 'message' => 'ID del ticket no válido']);
    exit;
}

// Actualizar el campo estado_eliminado a 0
$sql = "UPDATE ticket SET estado_eliminado = 0 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticketId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Ticket restaurado con éxito']);
} else {
    // Mostrar el error de MySQL para depuración
    echo json_encode(['success' => false, 'message' => 'Error al restaurar el ticket: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>