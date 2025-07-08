<?php
// Incluir el archivo de conexión
require('../../php/conexion.php');

$idUsuario = $_POST['usuario_id'];
$nombre = $_POST['nombre'];
$apellido_paterno = $_POST['apellido_paterno'];
$apellido_materno = $_POST['apellido_materno'];
$usuario = $_POST['usuario'];
$id_area = $_POST['area'];
$puesto = $_POST['puesto'];
$num_empleado = $_POST['num_empleado'];
$extension = $_POST['extension'];
$correo = $_POST['correo'];
$id_tipo_usuario = $_POST['tipo_usuario'];
$password = $_POST['password'];

if (!empty($password)) {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, usuario = ?, id_area = ?, puesto = ?, num_empleado = ?, extension = ?, correo = ?, id_tipo_usuario = ?, contraseña = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissssisi", $nombre, $apellido_paterno, $apellido_materno, $usuario, $id_area, $puesto, $num_empleado, $extension, $correo, $id_tipo_usuario, $passwordHash, $idUsuario);
} else {
    $sql = "UPDATE usuarios SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, usuario = ?, id_area = ?, puesto = ?, num_empleado = ?, extension = ?, correo = ?, id_tipo_usuario = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissssii", $nombre, $apellido_paterno, $apellido_materno, $usuario, $id_area, $puesto, $num_empleado, $extension, $correo, $id_tipo_usuario, $idUsuario);
}

if ($stmt->execute()) {
    echo 'success';
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>