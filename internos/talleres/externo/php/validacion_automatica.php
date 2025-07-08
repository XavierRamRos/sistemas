<?php
// validacion_automatica.php - Este código debe incluirse al inicio de validacion.php

require_once '../../../php/conexion.php';


// Obtener la fecha actual
$fechaActual = date('Y-m-d H:i:s');

// Consulta para actualizar usuarios externos con más de 120 días
$sql = "UPDATE tall_inscritos 
        SET id_estado = 3, 
            ultima_modificacion = ?
        WHERE id_tipo = 2 
        AND id_estado = 1 
        AND (
            DATEDIFF(?, COALESCE(ultima_modificacion, fecha_registro)) > 120";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('ss', $fechaActual, $fechaActual);
    $stmt->execute();
    
    // Opcional: registrar cuántos registros se actualizaron
    $affectedRows = $stmt->affected_rows;
    if ($affectedRows > 0) {
        error_log("Validación automática: Se actualizaron $affectedRows registros de usuarios externos con más de 120 días.");
    }
    $stmt->close();
} else {
    error_log("Error en la validación automática: " . $conn->error);
}

// No es necesario cerrar la conexión aquí si se sigue usando en la página
?>