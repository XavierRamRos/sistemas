<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../../../php/conexion.php';

// Obtener datos del POST
$nombre = trim($_POST['nombre'] ?? '');
$paterno = trim($_POST['paterno'] ?? '');
$materno = trim($_POST['materno'] ?? '');
$id_taller = intval($_POST['id_taller'] ?? 0);
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$num_movil = trim($_POST['num_movil'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$calle = trim($_POST['calle'] ?? '');
$colonia = trim($_POST['colonia'] ?? '');
$num_interior = trim($_POST['num_interior'] ?? null);
$num_exterior = trim($_POST['num_exterior'] ?? null);
$id_salud = intval($_POST['id_salud'] ?? 0);
$num_medico = trim($_POST['num_medico'] ?? null);
$padecimiento = trim($_POST['padecimiento'] ?? null);
$alergia = trim($_POST['alergia'] ?? null);
$nombre_alt = trim($_POST['nombre_alt'] ?? null);
$paterno_alt = trim($_POST['paterno_alt'] ?? null);
$movil_alt = trim($_POST['movil_alt'] ?? null);
$calle_alt = trim($_POST['calle_alt'] ?? null);
$colonia_alt = trim($_POST['colonia_alt'] ?? null);
$num_interno_alt = trim($_POST['num_interno_alt'] ?? null);
$num_externo_alt = trim($_POST['num_externo_alt'] ?? null);
$fecha_registro = trim($_POST['fecha_registro'] ?? date('Y-m-d H:i:s'));
$id_usuario_registro = intval($_POST['id_usuario_registro'] ?? $_SESSION['id_usuario']);
$id_tipo = intval($_POST['id_tipo'] ?? 2); // 2 = Externo
$id_medio = intval($_POST['id_medio'] ?? 0);
$linea_captura = trim($_POST['linea_captura'] ?? '');
$id_horario = intval($_POST['id_horario_taller'] ?? 0);
$id_estado = 1; // Valor fijo para id_estado
$id_sexo = intval($_POST['id_sexo'] ?? 0);

// Validar campos requeridos
if (empty($nombre) || empty($paterno) || empty($materno) || empty($id_taller) || 
    empty($fecha_nacimiento) || empty($id_sexo) || empty($num_movil) || empty($correo) || 
    empty($calle) || empty($colonia) || empty($id_salud) || empty($id_medio) || 
    empty($linea_captura) || empty($id_horario)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos requeridos deben estar completos']);
    exit();
}

// Validar que se haya seleccionado un horario
if (empty($id_horario)) {
    echo json_encode(['success' => false, 'message' => 'Debe seleccionar un horario']);
    exit();
}

// Calcular edad a partir de la fecha de nacimiento
$fechaNac = new DateTime($fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNac)->y;

try {
    // Iniciar transacción
    $conn->begin_transaction();

    // 1. Insertar en tall_inscritos
    $sql = "INSERT INTO tall_inscritos (
        nombre, paterno, materno, id_taller, edad, fecha_nacimiento, 
        num_movil, correo, calle, colonia, num_interior, num_exterior, id_salud, num_medico, 
        padecimiento, alergia, nombre_alt, paterno_alt, movil_alt, calle_alt, colonia_alt, 
        num_interno_alt, num_externo_alt, fecha_registro, id_usuario_registro, id_tipo, id_medio,
        id_horario, id_estado, id_sexo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param(
        "sssiisssssssissssssssssssiiiii",
        $nombre, $paterno, $materno, $id_taller, $edad, $fecha_nacimiento,
        $num_movil, $correo, $calle, $colonia, $num_interior, $num_exterior, $id_salud, $num_medico,
        $padecimiento, $alergia, $nombre_alt, $paterno_alt, $movil_alt, $calle_alt, $colonia_alt,
        $num_interno_alt, $num_externo_alt, $fecha_registro, $id_usuario_registro, $id_tipo, $id_medio,
        $id_horario, $id_estado, $id_sexo
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $id_inscrito = $stmt->insert_id;
    $stmt->close();

    // 2. Insertar en tall_validacion
    $sql_validacion = "INSERT INTO tall_validacion (linea_captura, id_inscrito, id_estado) VALUES (?, ?, 1)"; // 1 = Pendiente
    $stmt_validacion = $conn->prepare($sql_validacion);
    
    if (!$stmt_validacion) {
        throw new Exception("Error al preparar la consulta de validación: " . $conn->error);
    }
    
    $stmt_validacion->bind_param("si", $linea_captura, $id_inscrito);
    
    if (!$stmt_validacion->execute()) {
        throw new Exception("Error al ejecutar la consulta de validación: " . $stmt_validacion->error);
    }
    
    $id_validacion = $stmt_validacion->insert_id;
    $stmt_validacion->close();

    // 3. Actualizar tall_inscritos con el id_validacion
    $sql_update = "UPDATE tall_inscritos SET id_validacion = ? WHERE id_inscrito = ?";
    $stmt_update = $conn->prepare($sql_update);
    
    if (!$stmt_update) {
        throw new Exception("Error al preparar la consulta de actualización: " . $conn->error);
    }
    
    $stmt_update->bind_param("ii", $id_validacion, $id_inscrito);
    
    if (!$stmt_update->execute()) {
        throw new Exception("Error al ejecutar la consulta de actualización: " . $stmt_update->error);
    }
    
    $stmt_update->close();

    // Confirmar transacción
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Inscripción de externo registrada correctamente']);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}

$conn->close();
?>