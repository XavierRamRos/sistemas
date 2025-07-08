<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../../../php/conexion.php';

$id_inscrito = isset($_POST['id_inscrito']) ? intval($_POST['id_inscrito']) : 0;

if ($id_inscrito <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

// Actualizar el estado a 3 (Cancelado) y la fecha de última modificación
$sql = "UPDATE tall_inscritos SET id_estado = 3, ultima_modificacion = CURRENT_TIMESTAMP WHERE id_inscrito = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param('i', $id_inscrito);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Inscripción cancelada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al cancelar la inscripción: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>