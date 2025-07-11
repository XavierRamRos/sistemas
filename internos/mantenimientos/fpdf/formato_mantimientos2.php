<?php
require('fpdf/fpdf.php');
require 'vendor/autoload.php';
use setasign\Fpdi\Fpdi;

// Incluir el archivo de conexión
require('../../../php/conexion.php');

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener parámetros de filtro
$tallerId = isset($_GET['taller']) ? intval($_GET['taller']) : 0;
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$estado = isset($_GET['estado']) ? intval($_GET['estado']) : 0;
$tipo = isset($_GET['tipo']) ? intval($_GET['tipo']) : 0;

// Consulta para obtener inscritos
$query = "SELECT 
            i.id_inscrito, 
            IF(i.id_tipo = 1, i.matricula, 'EXTERNO') AS matricula,
            CONCAT(i.nombre, ' ', i.paterno, ' ', i.materno) AS nombre_completo,
            t.nombre AS taller,
            et.nombre AS estado
          FROM tall_inscritos i
          JOIN tall_talleres t ON i.id_taller = t.id_taller
          LEFT JOIN tall_estado_taller et ON i.id_estado = et.id_estado
          WHERE 1=1";

// Aplicar filtros
$params = [];
$types = "";

if ($tallerId > 0) {
    $query .= " AND i.id_taller = ?";
    $params[] = $tallerId;
    $types .= "i";
}

if (!empty($busqueda)) {
    $query .= " AND (i.matricula LIKE ? OR CONCAT(i.nombre, ' ', i.paterno, ' ', i.materno) LIKE ? OR i.num_movil LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if ($estado > 0) {
    $query .= " AND i.id_estado = ?";
    $params[] = $estado;
    $types .= "i";
}

if ($tipo > 0) {
    $query .= " AND i.id_tipo = ?";
    $params[] = $tipo;
    $types .= "i";
}

// Modificar la cláusula ORDER BY para que los de estado BAJA (3) aparezcan al final
$query .= " ORDER BY 
            CASE WHEN i.id_estado = 3 THEN 1 ELSE 0 END,  
            t.nombre,                                     
            i.fecha_registro DESC,                       
            i.id_tipo ASC,                                
            i.id_estado DESC";                            

// Preparar consulta
$stmt = $conn->prepare($query);
if (!$stmt) die("Error preparando consulta: " . $conn->error);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) die("Error ejecutando consulta: " . $stmt->error);
$inscritos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Crear PDF
class PDF extends Fpdi {
    protected $showTemplate = true;
    protected $firstPage = true;
    
    function Header() {
        // Solo mostrar el template en la primera página
        if ($this->showTemplate && $this->PageNo() == 1) {
            // Importar página del formato base
            $this->setSourceFile('formato/formato2.pdf');
            $tplIdx = $this->importPage(1);
            $this->useTemplate($tplIdx, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
        }
        $this->showTemplate = false;
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(350, 10, ''.$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

// Configuración inicial
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0, 0, 0);

// POSICIONES X EXACTAS ajustadas para nuevas anchuras
$posXConsecutivo = 25;    // Posición X para el consecutivo
$posXMatricula = 32.5;    // Posición X para la matrícula
$posXNombre = 56;         // Posición X para el nombre completo (mantenida)
$posXTaller = 125;        // Posición X para el taller (aumentada de 115 a 125)
$posXEstado = 160;        // Posición X para el estado (aumentada de 146 a 160)

// ANCHOS ajustados según solicitud
$widthConsecutivo = $posXMatricula - $posXConsecutivo;      // 7.5 (mantenido)
$widthMatricula = $posXNombre - $posXMatricula;             // 23.5 (mantenido)
$widthNombre = $posXTaller - $posXNombre;                   // 69 (aumentado de 59)
$widthTaller = $posXEstado - $posXTaller;                   // 35 (aumentado de 31)
$widthEstado = 20;                                          // Reducido a 20 (para "Estado")

// Bordes de la tabla
$leftBorder = $posXConsecutivo;
$rightBorder = $posXEstado + $widthEstado;

// ENCABEZADO DE TABLA SOLO EN PRIMERA PÁGINA
$headerY = 65; // Posición Y del encabezado
$headerHeight = 5; // Altura del encabezado

// Dibujar fondo rojo para el encabezado CON BORDES
// Encabezado con fondo y bordes
$pdf->SetFillColor(160, 36, 68); // Rojo oscuro
$pdf->SetTextColor(255, 255, 255); // Blanco
$pdf->SetDrawColor(0, 0, 0); // Bordes negros
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY($posXConsecutivo, $headerY);

$pdf->Cell($widthConsecutivo, $headerHeight, '#', 1, 0, 'C', true);
$pdf->Cell($widthMatricula, $headerHeight, 'Matricula', 1, 0, 'C', true);
$pdf->Cell($widthNombre, $headerHeight, 'Nombre completo', 1, 0, 'C', true);
$pdf->Cell($widthTaller, $headerHeight, 'Taller', 1, 0, 'C', true);
$pdf->Cell($widthEstado, $headerHeight, 'Estado', 1, 0, 'C', true);

// Restaurar color de texto para el contenido
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0); // Bordes negros para el contenido
$pdf->SetFont('Arial', '', 8);

// Posiciones para el contenido de la tabla
$startY = $headerY + $headerHeight; // Comenzar después del encabezado
$rowHeight = 4.5;   // Altura de fila
$currentY = $startY;

// Contador
$contador = 0;

foreach ($inscritos as $inscrito) {
    $contador++;
    
    // Verificar si necesitamos nueva página
    if ($currentY > 270) {
        // Dibujar bordes verticales completos para la página actual
        $pdf->Line($leftBorder, $startY, $leftBorder, $currentY);
        $pdf->Line($posXMatricula, $startY, $posXMatricula, $currentY);
        $pdf->Line($posXNombre, $startY, $posXNombre, $currentY);
        $pdf->Line($posXTaller, $startY, $posXTaller, $currentY);
        $pdf->Line($posXEstado, $startY, $posXEstado, $currentY);
        $pdf->Line($rightBorder, $startY, $rightBorder, $currentY);
        
        $pdf->AddPage();
        $currentY = 30; // Posición Y inicial en páginas adicionales
        $startY = $currentY;
    }
    
    // Dibujar línea horizontal superior
    $pdf->Line($leftBorder, $currentY, $rightBorder, $currentY);
    
    // Escribir datos con posiciones X EXACTAS y bordes completos
    // Consecutivo (bordes completos: 1)
    $pdf->SetXY($posXConsecutivo, $currentY);
    $pdf->Cell($widthConsecutivo, $rowHeight, $contador, 1, 0, 'C');
    
    // Matrícula (bordes completos: 1)
    $pdf->SetXY($posXMatricula, $currentY);
    $pdf->Cell($widthMatricula, $rowHeight, utf8_decode($inscrito['matricula']), 1, 0, 'C');
    
    // Nombre completo (bordes completos: 1)
    $pdf->SetXY($posXNombre, $currentY);
    $pdf->Cell($widthNombre, $rowHeight, utf8_decode($inscrito['nombre_completo']), 1, 0, 'L');
    
    // Taller (bordes completos: 1)
    $pdf->SetXY($posXTaller, $currentY);
    $pdf->Cell($widthTaller, $rowHeight, utf8_decode($inscrito['taller']), 1, 0, 'L');
    
    // Estado (bordes completos: 1)
    $pdf->SetXY($posXEstado, $currentY);
    $pdf->Cell($widthEstado, $rowHeight, utf8_decode($inscrito['estado']), 1, 1, 'C');
    
    // Dibujar línea horizontal inferior
    $pdf->Line($leftBorder, $currentY + $rowHeight, $rightBorder, $currentY + $rowHeight);
    
    $currentY += $rowHeight;
}

// Dibujar bordes verticales completos para la última página
$pdf->Line($leftBorder, $startY, $leftBorder, $currentY);
$pdf->Line($posXMatricula, $startY, $posXMatricula, $currentY);
$pdf->Line($posXNombre, $startY, $posXNombre, $currentY);
$pdf->Line($posXTaller, $startY, $posXTaller, $currentY);
$pdf->Line($posXEstado, $startY, $posXEstado, $currentY);
$pdf->Line($rightBorder, $startY, $rightBorder, $currentY);

// Nombre del archivo PDF
$nombreArchivo = 'Listado_General_Inscritos_' . date('d-m-Y') . '.pdf';

// Salida del PDF
$pdf->Output('I', $nombreArchivo);
?>