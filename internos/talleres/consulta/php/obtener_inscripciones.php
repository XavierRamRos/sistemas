<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../../../php/conexion.php';

$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$taller = isset($_GET['taller']) ? intval($_GET['taller']) : 0;
$estado = isset($_GET['estado']) ? intval($_GET['estado']) : 0;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Consulta optimizada para mostrar todos los inscritos
$sql = "SELECT SQL_CALC_FOUND_ROWS 
            i.id_inscrito, 
            i.matricula, 
            CONCAT(i.nombre, ' ', i.paterno, ' ', i.materno) AS nombre_completo,
            IFNULL(c.nombre, 'N/A') AS carrera,
            t.nombre AS taller,
            i.num_movil,
            DATE_FORMAT(i.fecha_registro, '%d/%m/%Y %H:%i') AS fecha_registro,
            IFNULL(ut.nombre, 'N/A') AS tipo_usuario,
            i.id_tipo,
            et.nombre AS estado_taller,
            i.id_estado
        FROM tall_inscritos i
        JOIN tall_talleres t ON i.id_taller = t.id_taller
        LEFT JOIN carreras c ON i.carrera = c.id_carrera
        LEFT JOIN tall_usuario_tipo ut ON i.id_tipo = ut.id_tipo
        LEFT JOIN tall_estado_taller et ON i.id_estado = et.id_estado
        WHERE 1=1";

$params = [];
if (!empty($busqueda)) {
    $sql .= " AND (i.matricula LIKE ? OR 
                  CONCAT(i.nombre, ' ', i.paterno, ' ', i.materno) LIKE ? OR 
                  i.num_movil LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

if ($taller > 0) {
    $sql .= " AND i.id_taller = ?";
    $params[] = $taller;
}

if ($estado > 0) {
    $sql .= " AND i.id_estado = ?";
    $params[] = $estado;
}

$sql .= " ORDER BY i.fecha_registro DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error al preparar la consulta: ' . $conn->error
    ]);
    exit();
}

$types = '';
foreach ($params as $param) {
    $types .= is_int($param) ? 'i' : 's';
}

$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error al ejecutar la consulta: ' . $stmt->error
    ]);
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