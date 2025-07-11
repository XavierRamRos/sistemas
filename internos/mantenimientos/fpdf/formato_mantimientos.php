<?php
require('fpdf/fpdf.php');
require 'vendor/autoload.php';
use setasign\Fpdi\Fpdi;

// Incluir el archivo de conexión
require('../../../php/conexion.php');

// Verificar sesión y permisos
session_start();
if (!isset($_SESSION['id_usuario'])) {
    die("No autorizado");
}

// Obtener ID del mantenimiento
$id_mantenimiento = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_mantenimiento <= 0) {
    die("ID inválido");
}

// Consulta para obtener datos del mantenimiento
$query_mantenimiento = "SELECT 
    m.id_mantenimientos,
    m.nombre AS nombre_mantenimiento,
    CONCAT(u1.nombre, ' ', u1.apellido_paterno) AS solicitante,
    a.area AS area_solicitante,
    CONCAT(u2.nombre, ' ', u2.apellido_paterno) AS tecnico,
    DATE_FORMAT(m.fecha_inicio, '%d/%m/%Y %H:%i') AS fecha_inicio,
    DATE_FORMAT(m.fecha_termino, '%d/%m/%Y %H:%i') AS fecha_termino,
    IFNULL(DATE_FORMAT(m.fecha_validacion, '%d/%m/%Y %H:%i'), 'POR VALIDAR') AS fecha_validacion,
    CASE 
        WHEN m.fecha_validacion IS NOT NULL THEN 'VALIDADO'
        ELSE 'POR VALIDAR'
    END AS estado
FROM mantenimientos m
JOIN usuarios u1 ON m.id_usuario_solicitante = u1.id_usuario
JOIN areas a ON u1.id_area = a.id_area
JOIN usuarios u2 ON m.id_usuario_informatica = u2.id_usuario
WHERE m.id_mantenimientos = ?";

$stmt_mtto = $conn->prepare($query_mantenimiento);
$stmt_mtto->bind_param('i', $id_mantenimiento);
$stmt_mtto->execute();
$mantenimiento = $stmt_mtto->get_result()->fetch_assoc();
$stmt_mtto->close();

if (!$mantenimiento) {
    die("No se encontró el mantenimiento");
}

// Consulta para obtener equipos del mantenimiento
$query_equipos = "SELECT 
    marca, 
    modelo, 
    inventario, 
    descripcion
FROM detalles_mantenimientos
WHERE id_mantenimientos = ?";

$stmt_equipos = $conn->prepare($query_equipos);
$stmt_equipos->bind_param('i', $id_mantenimiento);
$stmt_equipos->execute();
$equipos = $stmt_equipos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_equipos->close();

// Crear PDF
class PDF extends Fpdi {
    protected $showTemplate = true;
    protected $firstPage = true;
    
    function Header() {
        // Solo mostrar el template en la primera página
        if ($this->showTemplate && $this->PageNo() == 1) {
            // Importar página del formato base
            $this->setSourceFile('formato/formato.pdf');
            $tplIdx = $this->importPage(1);
            $this->useTemplate($tplIdx, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
        }
        $this->showTemplate = false;
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

// Configuración inicial
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0, 0, 0);

// id consecutivo folio
$pdf->SetXY(170, 50);
$pdf->Cell(0, 10, '' . $mantenimiento['id_mantenimientos'], 0, 1);


// reponsable area
$pdf->SetXY(39.5, 76.5);
$pdf->Cell(0, 10, $mantenimiento['solicitante'], 0, 1);

// area
$pdf->SetXY(39.5, 92);
$pdf->Cell(0, 10, $mantenimiento['area_solicitante'], 0, 1);

// nombre de quien dio mantenimiento
$pdf->SetXY(145, 76);
$pdf->Cell(0, 10, $mantenimiento['tecnico'], 0, 1);


// fecha inicio
$pdf->SetXY(145, 84);
$pdf->Cell(0, 10, $mantenimiento['fecha_inicio'], 0, 1);


// fecha termino
$pdf->SetXY(145, 91);
$pdf->Cell(0, 10, $mantenimiento['fecha_termino'], 0, 1);


// reponsable area
$pdf->SetXY(55, 255);
$pdf->Cell(0, 10, $mantenimiento['solicitante'], 0, 1);

// fecha validacion
$pdf->SetXY(58, 260);
$pdf->Cell(0, 10, $mantenimiento['fecha_validacion'], 0, 1);

// nombre de quien dio mantenimiento final
$pdf->SetXY(145, 255);
$pdf->Cell(0, 10, $mantenimiento['tecnico'], 0, 1);

// fecha termino final informatica
$pdf->SetXY(145, 260);
$pdf->Cell(0, 10, $mantenimiento['fecha_termino'], 0, 1);

// Tabla de equipos
$pdf->SetY(120);
// $pdf->SetFont('Arial', 'B', 10);
// $pdf->Cell(0, 10, 'Equipos atendidos (' . count($equipos) . ')', 0, 1);

// // Encabezados de tabla
// $pdf->SetFillColor(160, 36, 68); // Rojo UNEVE
// $pdf->SetTextColor(255, 255, 255);
// $pdf->SetFont('Arial', 'B', 9);

// $pdf->Cell(30, 8, 'Inventario', 1, 0, 'C', true);
// $pdf->Cell(40, 8, 'Marca', 1, 0, 'C', true);
// $pdf->Cell(40, 8, 'Modelo', 1, 0, 'C', true);
// $pdf->Cell(80, 8, 'Descripción', 1, 1, 'C', true);

// Contenido de la tabla
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 7);

foreach ($equipos as $equipo) {
    $pdf->Cell(30, 8, $equipo['inventario'] ?: 'N/A', 1);
    $pdf->Cell(40, 8, $equipo['marca'] ?: 'N/A', 1);
    $pdf->Cell(40, 8, $equipo['modelo'] ?: 'N/A', 1);
    $pdf->Cell(80, 8, $equipo['descripcion'] ?: 'N/A', 1);
    $pdf->Ln();
}

// Nombre del archivo PDF
$nombreArchivo = 'Mantenimiento_' . $mantenimiento['id_mantenimientos'] . '_' . date('d-m-Y') . '.pdf';

// Salida del PDF
$pdf->Output('I', $nombreArchivo);
?>