<?php
require_once '../../../../php/conexion.php';

header('Content-Type: application/json');

if (!isset($_POST['id_taller'])) {
    echo json_encode(['success' => false, 'message' => 'ID de taller no proporcionado']);
    exit();
}

$idTaller = intval($_POST['id_taller']);

try {
    // Obtener días distintos disponibles para el taller
    $sql = "SELECT DISTINCT d.id_dia, d.nombre 
            FROM tall_horario_taller ht
            JOIN tall_dias d ON ht.id_dia = d.id_dia
            WHERE ht.id_taller = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idTaller);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $dias = [];
    while ($row = $result->fetch_assoc()) {
        $dias[] = $row;
    }
    
    echo json_encode(['success' => true, 'dias' => $dias]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener días: ' . $e->getMessage()]);
}

$conn->close();
?>