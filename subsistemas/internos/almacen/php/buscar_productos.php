<?php
session_start();
require_once '../../../../php/conexion.php';

if (isset($_POST['term']) && isset($_POST['almacenes'])) {
    $searchTerm = $conn->real_escape_string($_POST['term']);
    $almacenes = $_POST['almacenes'];
    
    // Convertir array de almacenes a string para la consulta SQL
    $almacenes_str = implode(",", $almacenes);
    
    $query = "SELECT p.*, a.nombre as nombre_almacen, u.descripcion as unidad 
              FROM alm_productos p
              LEFT JOIN alm_almacen a ON p.id_almacen = a.id_almacen
              LEFT JOIN alm_unidad u ON p.id_unidad = u.id_unidad
              WHERE (p.nombre LIKE '%$searchTerm%' OR p.detalles LIKE '%$searchTerm%')
              AND p.id_almacen IN ($almacenes_str)
              ORDER BY p.nombre";
    
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="producto-card">';
            echo '<div class="producto-nombre">' . htmlspecialchars($row['nombre']) . '</div>';
            echo '<div class="producto-detalles">' . htmlspecialchars($row['detalles']) . '</div>';
            echo '<div class="producto-cantidad">Cantidad: ' . htmlspecialchars($row['cantidad']) . ' ' . htmlspecialchars($row['unidad']) . '</div>';
            echo '<div class="text-muted small">Almacén: ' . htmlspecialchars($row['nombre_almacen']) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="no-results">No se encontraron productos que coincidan con la búsqueda</div>';
    }
} else {
    echo '<div class="no-results">Parámetros de búsqueda no válidos</div>';
}
?>