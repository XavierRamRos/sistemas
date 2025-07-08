<?php
require_once '../../../../php/conexion.php';

// Establecer headers primero
header('Content-Type: application/json; charset=utf-8');

// Iniciar buffer de salida
ob_start();

try {
    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    // Obtener parámetros de paginación
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 15;
    $offset = ($page - 1) * $perPage;

    // Validar parámetros
    if ($page < 1) $page = 1;
    if ($perPage < 1) $perPage = 15;

    // Consulta para obtener el total de registros
    $queryTotal = "SELECT COUNT(*) as total FROM tall_horario_taller";
    $resultTotal = $conn->query($queryTotal);
    
    if (!$resultTotal) {
        throw new Exception("Error en la consulta de conteo: " . $conn->error);
    }

    $totalData = $resultTotal->fetch_assoc();
    $totalItems = $totalData['total'];

    // Consulta principal con paginación y ordenamiento
    $query = "SELECT ht.id_horario_taller, t.nombre as taller, d.nombre as dia, d.id_dia,
                     hi.hora as hora_inicio, hf.hora as hora_fin, hi.id_horario as id_hora_inicio
              FROM tall_horario_taller ht
              JOIN tall_talleres t ON ht.id_taller = t.id_taller
              JOIN tall_dias d ON ht.id_dia = d.id_dia
              JOIN tall_horario hi ON ht.id_hora_inicio = hi.id_horario
              JOIN tall_horario hf ON ht.id_hora_fin = hf.id_horario
              ORDER BY t.nombre ASC, d.id_dia ASC, hi.id_horario ASC
              LIMIT ?, ?";

    // Preparar consulta con parámetros para evitar inyección SQL
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("ii", $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $horarios = [];
    while ($row = $result->fetch_assoc()) {
        $horarios[] = $row;
    }

    // Limpiar buffer antes de enviar JSON
    ob_end_clean();
    
    echo json_encode([
        'success' => true,
        'data' => $horarios,
        'total' => $totalItems,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => ceil($totalItems / $perPage)
    ]);

} catch (Exception $e) {
    // Limpiar buffer de cualquier salida no deseada
    ob_end_clean();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}

// Cerrar conexión
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>