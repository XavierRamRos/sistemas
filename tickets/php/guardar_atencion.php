<?php
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['num_empleado'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit;
}

// Incluir el archivo de conexión
require('../../php/conexion.php');

// Obtener y sanitizar datos del formulario
$ticketId = $conn->real_escape_string($_POST['ticketId']);
$fechaAtencion = $conn->real_escape_string($_POST['fechaAtencion']);
$personalAtendio = $conn->real_escape_string($_POST['personalAtendio']);
$tipoFalla = $conn->real_escape_string($_POST['tipoFalla']);
$descripcionSolucion = $conn->real_escape_string($_POST['descripcionSolucion']);
$usoMaterial = $conn->real_escape_string($_POST['usoMaterial']);

// Inicializar variables para materiales y piezas
$material_1 = "N/A";
$material_2 = "N/A";
$material_3 = "N/A";
$piezas_1 = "N/A";
$piezas_2 = "N/A";
$piezas_3 = "N/A";

// Si se usaron materiales, obtener los datos de los materiales y piezas
if ($usoMaterial == "1") {
    $material_1 = $conn->real_escape_string($_POST['material_1']);
    $material_2 = $conn->real_escape_string($_POST['material_2']);
    $material_3 = $conn->real_escape_string($_POST['material_3']);
    $piezas_1 = $conn->real_escape_string($_POST['piezas_1']);
    $piezas_2 = $conn->real_escape_string($_POST['piezas_2']);
    $piezas_3 = $conn->real_escape_string($_POST['piezas_3']);
}

// Preparar y ejecutar la consulta SQL
$sql = "UPDATE ticket SET 
        fecha_atencion = ?, 
        nombre_atendio = ?, 
        tipo_falla = ?, 
        desc_solucion = ?,
        material_1 = ?,
        material_2 = ?,
        material_3 = ?,
        piezas_1 = ?,
        piezas_2 = ?,
        piezas_3 = ?,
        status = '2'
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssssssi", 
    $fechaAtencion, 
    $personalAtendio, 
    $tipoFalla, 
    $descripcionSolucion,
    $material_1,
    $material_2,
    $material_3,
    $piezas_1,
    $piezas_2,
    $piezas_3,
    $ticketId
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Información guardada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la información: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>