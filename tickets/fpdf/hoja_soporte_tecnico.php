<?php
require('fpdf/fpdf.php');
require 'vendor/autoload.php';
use setasign\Fpdi\Fpdi;

// Incluir el archivo de conexión
require('../../php/conexion.php');

// Conexión a la base de datos de tickets
$conn_tickets = $conn; // Usamos la misma conexión para ambas bases de datos (ajusta si es necesario)

// Conexión a la base de datos de sistemas
$conn_sistemas = $conn; // Usamos la misma conexión para ambas bases de datos (ajusta si es necesario)

// Consulta para obtener los datos de los tickets
$query_tickets = "SELECT nticket, fecha_creacion, fecha_atencion, area, nombre, medio_soli, tipo_falla, desc_solucion, fecha_validacion, fecha_inicio, nombre_atendio, calificacion, material_1, material_2, material_3, piezas_1, piezas_2, piezas_3 FROM ticket ORDER BY id ASC";
$result_tickets = $conn_tickets->query($query_tickets);

if (!$result_tickets) {
    die("Error en la consulta de tickets: " . $conn_tickets->error);
}

$tickets = [];
while ($row = $result_tickets->fetch_assoc()) {
    $tickets[] = $row;
}

// Crear un nuevo PDF
$pdf = new Fpdi();
$baseFile = 'formato/hoja_soporte_tecnico_v2.pdf';
$pdf->AliasNbPages();

foreach ($tickets as $ticket) {
    // Obtener la extensión desde la base de datos de sistemas
    $query_extension = "SELECT extension FROM usuarios WHERE CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) = ?";
    $stmt_extension = $conn_sistemas->prepare($query_extension);
    $stmt_extension->bind_param("s", $ticket['nombre']);
    $stmt_extension->execute();
    $extension_result = $stmt_extension->get_result()->fetch_assoc();
    $extension = $extension_result['extension'] ?? 'N/A';

    $pdf->AddPage();
    $pdf->setSourceFile($baseFile);
    $tplIdx = $pdf->importPage(1);
    $pdf->useTemplate($tplIdx);

    // Configuración de fuentes y colores
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);

    // Agregar datos al PDF
    $pdf->SetXY(173, 59); // Coordenadas para nticket
    $pdf->Write(0, utf8_decode($ticket['nticket']));

    // Coordenadas para fecha_creacion
    $pdf->SetXY(80, 67);  // año
    $pdf->Write(0, substr($ticket['fecha_creacion'], 0, 4)); // Extraer el año
    $pdf->SetXY(73.5, 67); // mes
    $pdf->Write(0, substr($ticket['fecha_creacion'], 5, 2)); // Extraer el mes
    $pdf->SetXY(65.5, 67); // dia
    $pdf->Write(0, substr($ticket['fecha_creacion'], 8, 2)); // Extraer el dia

    // Coordenadas para fecha_de inicio
    $pdf->SetXY(130.5, 67);  // año
    $pdf->Write(0, substr($ticket['fecha_inicio'], 0, 4)); // Extraer el año
    $pdf->SetXY(122, 67); // mes
    $pdf->Write(0, substr($ticket['fecha_inicio'], 5, 2)); // Extraer el mes
    $pdf->SetXY(111, 67); // dia
    $pdf->Write(0, substr($ticket['fecha_inicio'], 8, 2)); // Extraer el dia

    // Coordenadas para fecha_de termino (validacion)
    $pdf->SetXY(185, 67);  // año
    $pdf->Write(0, substr($ticket['fecha_atencion'], 0, 4)); // Extraer el año
    $pdf->SetXY(175, 67); // mes
    $pdf->Write(0, substr($ticket['fecha_atencion'], 5, 2)); // Extraer el mes
    $pdf->SetXY(165, 67); // dia
    $pdf->Write(0, substr($ticket['fecha_atencion'], 8, 2)); // Extraer el dia

    $pdf->SetXY(54, 82); // Coordenadas para nombre
    $pdf->Write(0, utf8_decode($ticket['nombre']));

    $pdf->SetXY(57, 90); // Coordenadas para extension
    $pdf->Write(0, utf8_decode($extension));

    // Vía de solicitud
    $pdf->SetXY(113, 105.5); // Coordenadas para Personal
    if ($ticket['medio_soli'] == '1') {
        $pdf->Write(0, 'X');
    }

    $pdf->SetXY(151, 105.5); // Coordenadas para Teléfono
    if ($ticket['medio_soli'] == '2') {
        $pdf->Write(0, 'X');
    }

    $pdf->SetXY(182.5, 105.5); // Coordenadas para Sistema
    if ($ticket['medio_soli'] == '3') {
        $pdf->Write(0, 'X');
    }

    // Tipo de falla
    $pdf->SetXY(127, 113); // Coordenadas para Hardware
    if ($ticket['tipo_falla'] == '1') {
        $pdf->Write(0, 'X');
    }

    $pdf->SetXY(181, 113); // Coordenadas para Software
    if ($ticket['tipo_falla'] == '2') {
        $pdf->Write(0, 'X');
    }

    $pdf->SetXY(35, 126.5); // Coordenadas para desc_solucion
    $pdf->MultiCell(0, 4.5, utf8_decode($ticket['desc_solucion']));

    // coordenadas para Nombre y Fecha de Conformidad Área Solicitante(15)
    $pdf->SetXY(50, 198); // Coordenadas para nombre
    $pdf->Write(0, utf8_decode($ticket['nombre']));
    $pdf->SetXY(52, 193); // Coordenadas para fecha
    $pdf->Write(0, utf8_decode($ticket['fecha_validacion']));

    // coordenadas para Nombre y Fecha Depto. de Informática(16)
    $pdf->SetXY(135, 198); // Coordenadas para nombre
    $pdf->Write(0, utf8_decode($ticket['nombre_atendio']));
    $pdf->SetXY(137, 193); // Coordenadas para fecha
    $pdf->Write(0, utf8_decode($ticket['fecha_atencion']));

    $pdf->SetXY(164, 215);
    $pdf->Write(0, ('N/A'));

    // calificacion
    $estrella = __DIR__ . '/formato/star.png'; // Asegúrate de que la ruta sea correcta

    // Obtener el número de calificación
    $numEstrellas = intval($ticket['calificacion']);
    $x = 58;
    $y = 186;
    $tamaño = 3; // Tamaño de la estrella (ancho y alto en mm)

    // Dibujar las estrellas
    for ($i = 0; $i < $numEstrellas; $i++) {
        $pdf->Image($estrella, $x, $y, $tamaño, $tamaño);
        $x += $tamaño + 2; // Espaciado entre estrellas
    }

    $pdf->SetXY(37, 158);
    $pdf->Write(0, ('1'));
    $pdf->SetXY(37, 166);
    $pdf->Write(0, ('2'));
    $pdf->SetXY(37, 173);
    $pdf->Write(0, ('3'));

    // materiales usados
    $pdf->SetXY(47, 158); // Coordenadas para material 1
    $pdf->Write(0, utf8_decode($ticket['material_1']));
    $pdf->SetXY(47, 166); // Coordenadas para material 2
    $pdf->Write(0, utf8_decode($ticket['material_2']));
    $pdf->SetXY(47, 173); // Coordenadas para material 3
    $pdf->Write(0, utf8_decode($ticket['material_3']));
    // cantidad de piezas usadas
    $pdf->SetXY(180, 158); // Coordenadas para piezas 1
    $pdf->Write(0, utf8_decode($ticket['piezas_1']));
    $pdf->SetXY(180, 166); // Coordenadas para piezas 2
    $pdf->Write(0, utf8_decode($ticket['piezas_2']));
    $pdf->SetXY(180, 173); // Coordenadas para piezas 3
    $pdf->Write(0, utf8_decode($ticket['piezas_3']));

    // Agregar el número de página y el total de páginas
    $pdf->SetFont('Arial', 'B', 9.5);
    $pdf->SetXY(153, 42); // Coordenadas para el número de página
    $pdf->Write(0, utf8_decode('Página ' . $pdf->PageNo() . ' de {nb}'));

    ///AREA
    $pdf->SetFont('Arial', '', 9.5);
    $pdf->SetXY(60, 74.8); // Coordenadas para area
    $pdf->Write(0, utf8_decode(substr($ticket['area'], 0, 65)));
}

// Guardar el archivo
$pdf->Output('I', 'Soporte Tecnico Informatica.pdf');