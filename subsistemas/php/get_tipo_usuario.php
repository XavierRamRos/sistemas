<?php
session_start();
require_once '../../php/conexion.php';

header('Content-Type: application/json');

if (isset($_SESSION['id_tipo_usuario'])) {
    echo json_encode(['id_tipo_usuario' => $_SESSION['id_tipo_usuario']]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
}