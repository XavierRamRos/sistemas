<?php
session_start();

header('Content-Type: application/json');

try {
    if (!isset($_POST['ticketId']) || !isset($_SESSION['nombre_completo'])) {
        throw new Exception('No se recibió el ID del ticket o el nombre del usuario');
    }
    
    // Database connection
    date_default_timezone_set('America/Mexico_City');

    $ticketId = intval($_POST['ticketId']);
    $nombre_atendio = $_SESSION['nombre_completo'];
    $fechaEliminacion = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual

    // Conectar a la base de datos
    require('../../php/conexion.php');

    // Preparar la consulta SQL para actualizar el estado_eliminado, el nombre del usuario y la fecha de eliminación
    $stmt = $conn->prepare("UPDATE ticket SET estado_eliminado = 1, eliminado_por = ?, fecha_eliminacion = ? WHERE id = ?");
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular parámetros y ejecutar
    $stmt->bind_param("ssi", $nombre_atendio, $fechaEliminacion, $ticketId);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    // Verificar si se actualizó algún registro
    if ($stmt->affected_rows === 0) {
        throw new Exception("No se encontró el ticket o ya estaba eliminado");
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => 'Ticket eliminado con éxito'
    ]);

} catch (Exception $e) {
    // Enviar respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>