<?php
require_once '../../../../php/conexion.php';

header('Content-Type: application/json');

if (!isset($_POST['id_taller']) || !isset($_POST['id_dia'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$idTaller = intval($_POST['id_taller']);
$idDia = intval($_POST['id_dia']);

try {
    // Obtener horarios disponibles para el taller en el día seleccionado
    $sql = "SELECT ht.id_horario_taller, hi.hora as hora_inicio, hf.hora as hora_fin
            FROM tall_horario_taller ht
            JOIN tall_horario hi ON ht.id_hora_inicio = hi.id_horario
            LEFT JOIN tall_horario hf ON ht.id_hora_fin = hf.id_horario
            WHERE ht.id_taller = ? AND ht.id_dia = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idTaller, $idDia);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $horarios = [];
    while ($row = $result->fetch_assoc()) {
        $horarios[] = $row;
    }
    
    echo json_encode(['success' => true, 'horarios' => $horarios]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener horarios: ' . $e->getMessage()]);
}

$conn->close();
?>