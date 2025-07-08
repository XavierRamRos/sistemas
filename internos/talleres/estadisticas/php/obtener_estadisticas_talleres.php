<?php
require('../../../../php/conexion.php');

header('Content-Type: application/json');

try {
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    // Obtener parámetros de filtro
    $taller = isset($_GET['taller']) ? intval($_GET['taller']) : 0;
    $estado = isset($_GET['estado']) ? intval($_GET['estado']) : 0;
    $sexo = isset($_GET['sexo']) ? intval($_GET['sexo']) : 0;
    $fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '';
    $fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : '';

    // Validar fechas
    if (empty($fechaInicio) || empty($fechaFin)) {
        $fechaInicio = date('Y-m-01');
        $fechaFin = date('Y-m-t');
    }

    $response = [];

    // 1. Estadísticas generales (corregido)
    $sqlGeneral = "SELECT 
                    COUNT(*) as totalInscritos,
                    SUM(CASE WHEN ts.id_sexo = 1 THEN 1 ELSE 0 END) as totalHombres,
                    SUM(CASE WHEN ts.id_sexo = 2 THEN 1 ELSE 0 END) as totalMujeres,
                    SUM(CASE WHEN ti.id_estado = 3 THEN 1 ELSE 0 END) as totalBajas,
                    SUM(CASE WHEN ti.id_estado = 1 THEN 1 ELSE 0 END) as totalActivos,
                    SUM(CASE WHEN ti.id_tipo = 1 THEN 1 ELSE 0 END) as totalInternos,
                    SUM(CASE WHEN ti.id_tipo = 2 THEN 1 ELSE 0 END) as totalExternos
                  FROM tall_inscritos ti
                  LEFT JOIN tall_sexo ts ON ti.id_sexo = ts.id_sexo
                  LEFT JOIN tall_estado_taller te ON ti.id_estado = te.id_estado
                  LEFT JOIN tall_usuario_tipo tut ON ti.id_tipo = tut.id_tipo
                  WHERE ti.fecha_registro BETWEEN ? AND ?";
    
    $params = [$fechaInicio, $fechaFin];
    $types = "ss";

    if ($taller > 0) {
        $sqlGeneral .= " AND ti.id_taller = ?";
        $params[] = $taller;
        $types .= "i";
    }

    if ($estado > 0) {
        $sqlGeneral .= " AND ti.id_estado = ?";
        $params[] = $estado;
        $types .= "i";
    }

    if ($sexo > 0) {
        $sqlGeneral .= " AND ti.id_sexo = ?";
        $params[] = $sexo;
        $types .= "i";
    }

    $stmtGeneral = $conn->prepare($sqlGeneral);
    if (!$stmtGeneral) {
        throw new Exception("Error en la preparación de consulta general: " . $conn->error);
    }

    $stmtGeneral->bind_param($types, ...$params);
    $stmtGeneral->execute();
    $resultGeneral = $stmtGeneral->get_result();
    $rowGeneral = $resultGeneral->fetch_assoc();

    $response['totalInscritos'] = $rowGeneral['totalInscritos'];
    $response['totalHombres'] = $rowGeneral['totalHombres'];
    $response['totalMujeres'] = $rowGeneral['totalMujeres'];
    $response['totalBajas'] = $rowGeneral['totalBajas'];
    $response['totalActivos'] = $rowGeneral['totalActivos'];
    $response['totalInternos'] = $rowGeneral['totalInternos'];
    $response['totalExternos'] = $rowGeneral['totalExternos'];

    // 2. Inscritos por taller (corregido)
    $sqlTalleres = "SELECT 
                    tt.id_taller,
                    tt.nombre as nombre_taller,
                    COUNT(ti.id_inscrito) as total_inscritos
                  FROM tall_talleres tt
                  LEFT JOIN tall_inscritos ti ON tt.id_taller = ti.id_taller 
                    AND ti.fecha_registro BETWEEN ? AND ?";
    
    if ($estado > 0) {
        $sqlTalleres .= " AND ti.id_estado = ?";
    }

    if ($sexo > 0) {
        $sqlTalleres .= " AND ti.id_sexo = ?";
    }

    $sqlTalleres .= " GROUP BY tt.id_taller ORDER BY total_inscritos DESC";

    $stmtTalleres = $conn->prepare($sqlTalleres);
    if (!$stmtTalleres) {
        throw new Exception("Error en la preparación de consulta de talleres: " . $conn->error);
    }

    $paramsTalleres = [$fechaInicio, $fechaFin];
    $typesTalleres = "ss";

    if ($estado > 0) {
        $paramsTalleres[] = $estado;
        $typesTalleres .= "i";
    }

    if ($sexo > 0) {
        $paramsTalleres[] = $sexo;
        $typesTalleres .= "i";
    }

    $stmtTalleres->bind_param($typesTalleres, ...$paramsTalleres);
    $stmtTalleres->execute();
    $resultTalleres = $stmtTalleres->get_result();

    $response['talleres'] = [];
    $response['inscritosPorTaller'] = [];

    while ($rowTaller = $resultTalleres->fetch_assoc()) {
        $response['talleres'][] = $rowTaller['nombre_taller'];
        $response['inscritosPorTaller'][] = $rowTaller['total_inscritos'] ?? 0;
    }

    // 3. Horarios más frecuentes
    $sqlHorarios = "SELECT 
                    CONCAT(th1.hora, ' - ', th2.hora) as horario,
                    COUNT(ti.id_inscrito) as total_inscritos
                  FROM tall_horario_taller tht
                  JOIN tall_horario th1 ON tht.id_hora_inicio = th1.id_horario
                  JOIN tall_horario th2 ON tht.id_hora_fin = th2.id_horario
                  JOIN tall_inscritos ti ON tht.id_horario_taller = ti.id_horario
                  WHERE ti.fecha_registro BETWEEN ? AND ?";
    
    if ($taller > 0) {
        $sqlHorarios .= " AND ti.id_taller = ?";
    }

    if ($estado > 0) {
        $sqlHorarios .= " AND ti.id_estado = ?";
    }

    if ($sexo > 0) {
        $sqlHorarios .= " AND ti.id_sexo = ?";
    }

    $sqlHorarios .= " GROUP BY horario ORDER BY total_inscritos DESC LIMIT 5";

    $stmtHorarios = $conn->prepare($sqlHorarios);
    if (!$stmtHorarios) {
        throw new Exception("Error en la preparación de consulta de horarios: " . $conn->error);
    }

    $paramsHorarios = [$fechaInicio, $fechaFin];
    $typesHorarios = "ss";

    if ($taller > 0) {
        $paramsHorarios[] = $taller;
        $typesHorarios .= "i";
    }

    if ($estado > 0) {
        $paramsHorarios[] = $estado;
        $typesHorarios .= "i";
    }

    if ($sexo > 0) {
        $paramsHorarios[] = $sexo;
        $typesHorarios .= "i";
    }

    $stmtHorarios->bind_param($typesHorarios, ...$paramsHorarios);
    $stmtHorarios->execute();
    $resultHorarios = $stmtHorarios->get_result();

    $response['horarios'] = [];
    $response['inscritosPorHorario'] = [];

    while ($rowHorario = $resultHorarios->fetch_assoc()) {
        $response['horarios'][] = $rowHorario['horario'];
        $response['inscritosPorHorario'][] = $rowHorario['total_inscritos'];
    }

    // 4. Inscritos por periodo (semanal)
    $sqlPeriodo = "SELECT 
                    CONCAT('Semana ', WEEK(fecha_registro) - WEEK(?) + 1) as semana,
                    COUNT(*) as total_inscritos
                  FROM tall_inscritos
                  WHERE fecha_registro BETWEEN ? AND ?";
    
    if ($taller > 0) {
        $sqlPeriodo .= " AND id_taller = ?";
    }

    if ($estado > 0) {
        $sqlPeriodo .= " AND id_estado = ?";
    }

    if ($sexo > 0) {
        $sqlPeriodo .= " AND id_sexo = ?";
    }

    $sqlPeriodo .= " GROUP BY WEEK(fecha_registro) ORDER BY fecha_registro";

    $stmtPeriodo = $conn->prepare($sqlPeriodo);
    if (!$stmtPeriodo) {
        throw new Exception("Error en la preparación de consulta de periodo: " . $conn->error);
    }

    $paramsPeriodo = [$fechaInicio, $fechaInicio, $fechaFin];
    $typesPeriodo = "sss";

    if ($taller > 0) {
        $paramsPeriodo[] = $taller;
        $typesPeriodo .= "i";
    }

    if ($estado > 0) {
        $paramsPeriodo[] = $estado;
        $typesPeriodo .= "i";
    }

    if ($sexo > 0) {
        $paramsPeriodo[] = $sexo;
        $typesPeriodo .= "i";
    }

    $stmtPeriodo->bind_param($typesPeriodo, ...$paramsPeriodo);
    $stmtPeriodo->execute();
    $resultPeriodo = $stmtPeriodo->get_result();

    $response['periodos'] = [];
    $response['inscritosPorPeriodo'] = [];

    while ($rowPeriodo = $resultPeriodo->fetch_assoc()) {
        $response['periodos'][] = $rowPeriodo['semana'];
        $response['inscritosPorPeriodo'][] = $rowPeriodo['total_inscritos'];
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}