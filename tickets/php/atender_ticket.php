<?php
session_start();

if (!isset($_SESSION['correo']) || !isset($_SESSION['nombre_completo'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit;
}

// Obtener el nombre del usuario de la sesión
$nombre_atendio = $_SESSION['nombre_completo'];

date_default_timezone_set('America/Mexico_City');

require('../../php/conexion.php');

if (isset($_POST['ticketId'])) {
    $ticketId = $conn->real_escape_string($_POST['ticketId']);
    
    $fecha_inicio = date('Y-m-d H:i:s');
    
    // Actualizar datos en ticket status, fecha_inicio, and nombre_atendio
    $sql = "UPDATE ticket SET status = '1', fecha_inicio = ?, nombre_atendio = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $fecha_inicio, $nombre_atendio, $ticketId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating ticket: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No ticket ID provided']);
}

$conn->close();
?>
