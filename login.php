<?php
session_start();
header('Content-Type: application/json');
require_once 'php/passwords.php';
require_once 'php/conexion.php'; // Incluir el archivo de conexión MySQLi

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate response
function send_response($success, $message = '', $redirect = '') {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit;
}

try {
    // Validate and sanitize inputs
    $num_empleado = isset($_POST['num_empleado']) ? sanitize_input($_POST['num_empleado']) : '';
    $password = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';

    if (empty($num_empleado) || empty($password)) {
        send_response(false, 'Todos los campos son requeridos');
    }

    // Obtener usuario por num_empleado
    $sql = "
        SELECT 
            usuarios.*, 
            areas.area
        FROM areas
        INNER JOIN usuarios ON areas.id_area = usuarios.id_area
        WHERE usuarios.num_empleado = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $num_empleado);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && verifyPassword($password, $usuario['contraseña'])) {
        // Verificar si el hash necesita ser actualizado
        if (needsRehash($usuario['contraseña'])) {
            // Actualizar el hash en la base de datos
            $newHash = hashPassword($password);
            $updateSql = "UPDATE usuarios SET contraseña = ? WHERE num_empleado = ?";
            $updateStmt = $conn->prepare($updateSql);
            if (!$updateStmt) {
                throw new Exception("Error en la preparación de la consulta de actualización: " . $conn->error);
            }
            $updateStmt->bind_param("ss", $newHash, $num_empleado);
            $updateStmt->execute();
        }

        // Set session variables
        $_SESSION['num_empleado'] = $num_empleado;
        $_SESSION['id_tipo_usuario'] = $usuario['id_tipo_usuario'];
        $_SESSION['usuario'] = $usuario['usuario'];
        $_SESSION['id_area'] = $usuario['id_area'];
        $_SESSION['area'] = $usuario['area'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['extension'] = $usuario['extension'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['apellido_paterno'] = $usuario['apellido_paterno'];
        $_SESSION['apellido_materno'] = $usuario['apellido_materno'];
        $_SESSION['nombre_completo'] = $usuario['nombre'] . " " . $usuario['apellido_paterno'] . " " . $usuario['apellido_materno'];

        $redirect = 'subsistemas/subsistemas.php';

        send_response(true, 'Login exitoso', $redirect);
    } else {
        // Usar un mensaje genérico por seguridad
        send_response(false, 'Número de empleado o contraseña incorrectos');
    }

} catch (Exception $e) {
    error_log("Error en login: " . $e->getMessage());
    send_response(false, 'Error del servidor');
}
?>