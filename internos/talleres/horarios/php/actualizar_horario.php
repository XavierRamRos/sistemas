<?php
require_once '../../../../php/conexion.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $id = $_POST['id'] ?? null;
    $id_taller = $_POST['id_taller'] ?? null;
    $id_dia = $_POST['id_dia'] ?? null;
    $id_hora_inicio = $_POST['id_hora_inicio'] ?? null;
    $id_hora_fin = $_POST['id_hora_fin'] ?? null;

    if (!$id || !$id_taller || !$id_dia || !$id_hora_inicio || !$id_hora_fin) {
        throw new Exception('Todos los campos son requeridos');
    }

    $stmt = $conn->prepare("UPDATE tall_horario_taller 
                           SET id_taller = ?, id_dia = ?, id_hora_inicio = ?, id_hora_fin = ?
                           WHERE id_horario_taller = ?");
    $stmt->bind_param("iiiii", $id_taller, $id_dia, $id_hora_inicio, $id_hora_fin, $id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Horario actualizado correctamente';
    } else {
        throw new Exception('Error al actualizar el horario');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>