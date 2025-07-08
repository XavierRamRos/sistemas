<?php
session_start();
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    // Incluir el archivo de conexión
    require('../../php/conexion.php');

    $required_fields = [
        'nombre', 'apellido_paterno', 'apellido_materno', 'usuario', 
        'area', 'puesto', 'num_empleado', 'correo', 'password', 'tipo_usuario', 'extension'
    ];
    
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        throw new Exception("Campos requeridos faltantes: " . implode(', ', $missing_fields));
    }

    // Verificar si el usuario o número de empleado ya existe
    $stmt = $conn->prepare("SELECT usuario FROM usuarios WHERE usuario = ? OR num_empleado = ?");
    $stmt->bind_param("ss", $_POST['usuario'], $_POST['num_empleado']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        throw new Exception("El usuario o número de empleado ya existe");
    }

    // Hash de la contraseña
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, usuario, 
            id_area, puesto, num_empleado, correo, contraseña, id_tipo_usuario, extension) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssissssis", 
        $_POST['nombre'],
        $_POST['apellido_paterno'],
        $_POST['apellido_materno'],
        $_POST['usuario'],
        $_POST['area'],
        $_POST['puesto'],
        $_POST['num_empleado'],
        $_POST['correo'],
        $password_hash,
        $_POST['tipo_usuario'],
        $_POST['extension']
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al insertar en la base de datos: " . $stmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente']);

} catch (Exception $e) {
    error_log("Error en registro.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'debug' => [
            'post_data' => $_POST,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>