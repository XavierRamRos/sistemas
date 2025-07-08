<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../../../php/conexion.php';

$id_inscrito = isset($_POST['id_inscrito']) ? intval($_POST['id_inscrito']) : 0;
$linea_captura = isset($_POST['linea_captura']) ? trim($_POST['linea_captura']) : '';

if ($id_inscrito <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

if (strlen($linea_captura) !== 27) {
    echo json_encode(['success' => false, 'message' => 'La línea de captura debe tener exactamente 27 caracteres']);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Insertar nueva validación
    $sqlValidacion = "INSERT INTO tall_validacion (linea_captura, id_inscrito, id_estado, fecha_validacion) 
                      VALUES (?, ?, 1, CURRENT_TIMESTAMP)";
    $stmtValidacion = $conn->prepare($sqlValidacion);
    $stmtValidacion->bind_param('si', $linea_captura, $id_inscrito);
    $stmtValidacion->execute();
    $id_validacion = $conn->insert_id;
    $stmtValidacion->close();
    
    // 2. Actualizar inscrito con nueva validación y fecha
    $sqlInscrito = "UPDATE tall_inscritos 
                    SET id_validacion = ?, ultima_modificacion = CURRENT_TIMESTAMP 
                    WHERE id_inscrito = ?";
    $stmtInscrito = $conn->prepare($sqlInscrito);
    $stmtInscrito->bind_param('ii', $id_validacion, $id_inscrito);
    $stmtInscrito->execute();
    $stmtInscrito->close();
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Inscripción renovada correctamente con la línea de captura registrada'
    ]);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al renovar la inscripción: ' . $e->getMessage()
    ]);
}

$conn->close();
?>