<?php
header('Content-Type: text/plain; charset=utf-8');

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conectar a la base de datos
    
    
    require('../../php/conexion.php');

    // Obtener y validar los datos enviados
    $ticketId = isset($_POST['ticketId']) ? intval($_POST['ticketId']) : 0;
    $nuevoEstado = isset($_POST['nuevoEstado']) ? intval($_POST['nuevoEstado']) : 0;

    // Verificar que los datos sean válidos
    if ($ticketId > 0) {
        // Preparar y ejecutar la consulta con prepared statement
        $sql = "UPDATE ticket SET status = ? WHERE id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $nuevoEstado, $ticketId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "Estado actualizado correctamente";
            } else {
                echo "No se encontró el ticket especificado";
            }
        } else {
            echo "Error al actualizar el estado: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "ID de ticket inválido";
    }

    $conn->close();
} else {
    echo "Método de solicitud no válido";
}
?>