<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require('../../php/conexion.php');

function generarNumeroTicket($conn) {
    $fecha = date('Y'); // Obtener el año actual

    // Obtener el último contador para el año actual
    $sql = "SELECT MAX(contador) as ultimo_contador FROM ticket WHERE YEAR(fecha_creacion) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Incrementar el contador
    $contador = (!empty($row['ultimo_contador'])) ? $row['ultimo_contador'] + 1 : 1;

    // Formatear el nuevo número de ticket
    $nuevoNumero = $fecha . '-0' . $contador; // Siempre agregar un '0' después del guion

    return [
        'nticket' => $nuevoNumero,
        'contador' => $contador
    ];
}

// Generar ID de ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escapar y obtener los datos del formulario
    $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
    $area = $conn->real_escape_string($_POST['area'] ?? '');
    $asunto = $conn->real_escape_string($_POST['asunto'] ?? '');
    $categoria = $conn->real_escape_string($_POST['categoria'] ?? '');
    $marca = $conn->real_escape_string($_POST['marca'] ?? '');
    $numero_inventario = $conn->real_escape_string($_POST['numero_inventario'] ?? '');
    $descripcion = $conn->real_escape_string($_POST['descripcion'] ?? '');
    $medio_soli = $conn->real_escape_string($_POST['medio_soli'] ?? '');
    $modelo = $conn->real_escape_string($_POST['modelo'] ?? '');

    // GENERAR NÚMERO DE TICKET
    $ticketData = generarNumeroTicket($conn);
    $nticket = $ticketData['nticket'];
    $contador = $ticketData['contador'];

    // PREPARAR CONSULTA DE INSERCIÓN DE DATOS
    $sql = "INSERT INTO ticket (
        nticket, 
        contador, 
        nombre, 
        area, 
        asunto, 
        categoria, 
        medio_soli, 
        marca, 
        numero_inventario,
        modelo,
        descripcion,
        fecha_creacion
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
    )";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            "sisssssssss", 
            $nticket, 
            $contador, 
            $nombre, 
            $area, 
            $asunto, 
            $categoria, 
            $medio_soli,
            $marca, 
            $numero_inventario,
            $modelo,
            $descripcion
        );

        // GENERAR/EJECUTAR CONSULTA
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'nticket' => $nticket
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al insertar ticket: ' . $stmt->error
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Error al preparar la consulta: ' . $conn->error
        ]);
    }

    $conn->close();
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Método no permitido'
    ]);
}
?>