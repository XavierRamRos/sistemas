<?php
require('../../php/conexion.php');

$estado = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$estadoEliminado = isset($_GET['estado_eliminado']) ? $_GET['estado_eliminado'] : '';

session_start();
$usuario = $_SESSION['correo'];

// Base de la consulta, excluyendo por defecto los eliminados (estado_eliminado = 1)
$sql = "SELECT * FROM ticket WHERE estado_eliminado = 0";

if ($estado !== '') {
    if ($estado == '4') {
        // Si se filtra por eliminados, mostrar únicamente los que tienen estado_eliminado = 1
        $sql = "SELECT * FROM ticket WHERE estado_eliminado = 1";
    } else {
        $sql .= " AND status = '" . $conn->real_escape_string($estado) . "'";
    }
}

// Filtro de búsqueda por varios campos
if (!empty($busqueda)) {
    $busqueda = $conn->real_escape_string($busqueda);
    $sql .= " AND (nombre LIKE '%$busqueda%' 
              OR id LIKE '%$busqueda%' 
              OR nticket LIKE '%$busqueda%' 
              OR area LIKE '%$busqueda%' 
              OR asunto LIKE '%$busqueda%' 
              OR marca LIKE '%$busqueda%' 
              OR modelo LIKE '%$busqueda%' 
              OR fecha_creacion LIKE '%$busqueda%' 
              OR numero_inventario LIKE '%$busqueda%' 
              OR medio_soli LIKE '%$busqueda%')";
}

// Ordenar por ID de manera descendente
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);

$tickets = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($tickets);

$conn->close();
?>