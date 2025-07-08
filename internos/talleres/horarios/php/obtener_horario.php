<?php
require_once '../../../../php/conexion.php'; // Asegúrate que esta ruta es correcta
header('Content-Type: application/json');

// Iniciar buffer de salida para capturar posibles errores
ob_start();

try {
    // Verificar que el archivo de conexión existe y obtener $conn
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Verificar que el ID está presente y es válido
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('ID de horario inválido');
    }

    $idHorario = intval($_GET['id']);

    $sql = "SELECT ht.id_horario_taller, ht.id_taller, ht.id_dia, 
                   ht.id_hora_inicio, ht.id_hora_fin,
                   t.nombre as nombre_taller, d.nombre as nombre_dia,
                   hi.hora as hora_inicio, hf.hora as hora_fin
            FROM tall_horario_taller ht
            JOIN tall_talleres t ON ht.id_taller = t.id_taller
            JOIN tall_dias d ON ht.id_dia = d.id_dia
            JOIN tall_horario hi ON ht.id_hora_inicio = hi.id_horario
            JOIN tall_horario hf ON ht.id_hora_fin = hf.id_horario
            WHERE ht.id_horario_taller = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $idHorario);
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Error al obtener resultados: " . $stmt->error);
    }

    $horario = $result->fetch_assoc();
    
    if (!$horario) {
        throw new Exception("Horario no encontrado");
    }

    // Limpiar buffer antes de enviar respuesta
    ob_end_clean();
    
    echo json_encode([
        'success' => true,
        'data' => $horario
    ]);

} catch (Exception $e) {
    // Limpiar buffer y enviar error
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// No es necesario cerrar la conexión aquí si se reutiliza
?>