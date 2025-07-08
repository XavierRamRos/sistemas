<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../../../php/conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

$sql = "SELECT 
            i.*,
            IFNULL(c.nombre, 'N/A') AS carrera,
            t.nombre AS taller,
            IFNULL(s.nombre, 'N/A') AS seguro_social,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS usuario_registro,
            IFNULL(CONCAT(i.nombre_alt, ' ', i.paterno_alt), 'N/A') AS contacto_alterno,
            IFNULL(
                CONCAT(
                    i.calle_alt, ' ', 
                    i.colonia_alt, ' ', 
                    IFNULL(CONCAT('No. Ext. ', i.num_externo_alt), ''), 
                    IFNULL(CONCAT('No. Int. ', i.num_interno_alt), '')
                ), 
                'N/A'
            ) AS domicilio_alterno,
            DATE_FORMAT(i.fecha_nacimiento, '%d/%m/%Y') AS fecha_nacimiento_formatted,
            IFNULL(hi.hora, 'N/A') AS hora_inicio, 
            IFNULL(hf.hora, 'N/A') AS hora_fin,
            IFNULL(d.nombre, 'N/A') AS dia,
            IFNULL(ut.nombre, 'N/A') AS tipo_usuario,
            IFNULL(m.nombre, 'N/A') AS medio_registro,
            IFNULL(v.linea_captura, 'N/A') AS linea_captura,
            IFNULL(e.nombre, 'N/A') AS estado_validacion,
            IFNULL(et.nombre, 'N/A') AS estado_taller
        FROM tall_inscritos i
        LEFT JOIN carreras c ON i.carrera = c.id_carrera
        JOIN tall_talleres t ON i.id_taller = t.id_taller
        LEFT JOIN seguro_social s ON i.id_salud = s.id_salud
        JOIN usuarios u ON i.id_usuario_registro = u.id_usuario
        LEFT JOIN tall_horario_taller ht ON i.id_horario = ht.id_horario_taller
        LEFT JOIN tall_horario hi ON ht.id_hora_inicio = hi.id_horario
        LEFT JOIN tall_horario hf ON ht.id_hora_fin = hf.id_horario
        LEFT JOIN tall_dias d ON ht.id_dia = d.id_dia
        LEFT JOIN tall_usuario_tipo ut ON i.id_tipo = ut.id_tipo
        LEFT JOIN tall_medio m ON i.id_medio = m.id_medio
        LEFT JOIN tall_validacion v ON i.id_validacion = v.id_validacion
        LEFT JOIN tall_estado e ON v.id_estado = e.id_estado
        LEFT JOIN tall_estado_taller et ON i.id_estado = et.id_estado
        WHERE i.id_inscrito = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param('i', $id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta: ' . $stmt->error]);
    exit();
}

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No se encontró la inscripción']);
    exit();
}

$data = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'data' => $data
]);
?>