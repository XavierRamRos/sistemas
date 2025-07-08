<?php
// Conexión a la base de datos
require '../../php/conexion.php';


// Leer y decodificar el JSON recibido
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar si se recibió un ID
if (isset($data['id'])) {
    $id = intval($data['id']);

    // Preparar y ejecutar la consulta
    $sql = "DELETE FROM ticket WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket eliminado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el ticket.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID.']);
}

$conn->close();
?>
