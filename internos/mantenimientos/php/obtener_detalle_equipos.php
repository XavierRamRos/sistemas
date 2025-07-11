<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../../php/conexion.php';

$id_mantenimiento = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_mantenimiento <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

$sql = "SELECT 
            marca, 
            modelo, 
            inventario, 
            descripcion
        FROM detalles_mantenimientos
        WHERE id_mantenimientos = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param('i', $id_mantenimiento);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta: ' . $stmt->error]);
    exit();
}

$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'data' => $data
]);
?>