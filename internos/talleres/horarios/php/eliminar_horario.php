<?php
require_once '../../../../php/conexion.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception('ID de horario no proporcionado');
    }

    $stmt = $conn->prepare("DELETE FROM tall_horario_taller WHERE id_horario_taller = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Horario eliminado correctamente';
    } else {
        throw new Exception('Error al eliminar el horario');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>