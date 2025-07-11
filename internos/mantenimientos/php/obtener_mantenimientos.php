<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../../php/conexion.php';

// Obtener parámetros de filtro
$area = isset($_GET['area']) ? intval($_GET['area']) : 0;
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$usuario_mtto = isset($_GET['usuario_mtto']) ? intval($_GET['usuario_mtto']) : 0;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Construir consulta base
$sql = "SELECT SQL_CALC_FOUND_ROWS
            m.id_mantenimientos,
            m.nombre,
            CONCAT(u1.nombre, ' ', u1.apellido_paterno) AS solicitante,
            CONCAT(u2.nombre, ' ', u2.apellido_paterno) AS tecnico,
            DATE_FORMAT(m.fecha_inicio, '%d/%m/%Y %H:%i') AS fecha_inicio,
            DATE_FORMAT(m.fecha_termino, '%d/%m/%Y %H:%i') AS fecha_termino,
            IFNULL(DATE_FORMAT(m.fecha_validacion, '%d/%m/%Y %H:%i'), 'POR VALIDAR') AS fecha_validacion,
            CASE 
                WHEN m.fecha_validacion IS NOT NULL THEN 'VALIDADO'
                ELSE 'POR VALIDAR'
            END AS estado,
            (SELECT COUNT(*) FROM detalles_mantenimientos dm WHERE dm.id_mantenimientos = m.id_mantenimientos) AS num_equipos
        FROM mantenimientos m
        JOIN usuarios u1 ON m.id_usuario_solicitante = u1.id_usuario
        JOIN usuarios u2 ON m.id_usuario_informatica = u2.id_usuario
        WHERE 1=1";

$params = [];
$types = '';

// Aplicar filtros
if ($area > 0) {
    $sql .= " AND u1.id_area = ?";
    $params[] = $area;
    $types .= 'i';
}

if (!empty($fecha_inicio)) {
    $sql .= " AND m.fecha_inicio >= ?";
    $params[] = $fecha_inicio;
    $types .= 's';
}

if (!empty($fecha_fin)) {
    $sql .= " AND m.fecha_inicio <= ?";
    $params[] = $fecha_fin . ' 23:59:59';
    $types .= 's';
}

if (!empty($estado)) {
    if ($estado == 'validado') {
        $sql .= " AND m.fecha_validacion IS NOT NULL";
    } else {
        $sql .= " AND m.fecha_validacion IS NULL";
    }
}

if ($usuario_mtto > 0) {
    $sql .= " AND m.id_usuario_informatica = ?";
    $params[] = $usuario_mtto;
    $types .= 'i';
}

$sql .= " ORDER BY m.fecha_inicio DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit();
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta: ' . $stmt->error]);
    exit();
}

$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

$totalResult = $conn->query("SELECT FOUND_ROWS() AS total");
$total = $totalResult->fetch_assoc()['total'];

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'data' => $data,
    'total' => $total,
    'page' => $page,
    'per_page' => $perPage
]);
?>