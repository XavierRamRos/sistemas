<?php
require_once '../../../../php/conexion.php';

// Establecer headers primero
header('Content-Type: application/json; charset=utf-8');

// Iniciar buffer de salida
ob_start();

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    $id_taller = $_POST['id_taller'] ?? null;
    $id_dia = $_POST['id_dia'] ?? null;
    $id_hora_inicio = $_POST['id_hora_inicio'] ?? null;
    $id_hora_fin = $_POST['id_hora_fin'] ?? null;

    if (!$id_taller || !$id_dia || !$id_hora_inicio || !$id_hora_fin) {
        throw new Exception('Todos los campos son requeridos');
    }

    $stmt = $conn->prepare("INSERT INTO tall_horario_taller (id_taller, id_dia, id_hora_inicio, id_hora_fin) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("iiii", $id_taller, $id_dia, $id_hora_inicio, $id_hora_fin);

    if ($stmt->execute()) {
        // Limpiar buffer antes de enviar JSON
        ob_end_clean();
        
        echo json_encode([
            'success' => true,
            'message' => 'Horario guardado correctamente',
            'id' => $stmt->insert_id
        ]);
    } else {
        throw new Exception('Error al guardar el horario: ' . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    // Limpiar buffer de cualquier salida no deseada
    ob_end_clean();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}

$conn->close();
?>