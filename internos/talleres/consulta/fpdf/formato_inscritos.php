<?php
require('fpdf/fpdf.php');
require 'vendor/autoload.php';
use setasign\Fpdi\Fpdi;

// Incluir el archivo de conexión
require('../../../../php/conexion.php');

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener parámetros de filtro
$tallerId = isset($_GET['taller']) ? intval($_GET['taller']) : 0;
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$estado = isset($_GET['estado']) ? intval($_GET['estado']) : 0;

// 1. Obtener información del taller (si se especificó)
$nombreTaller = "Todos";
if ($tallerId > 0) {
    $queryTaller = "SELECT nombre FROM tall_talleres WHERE id_taller = ?";
    $stmtTaller = $conn->prepare($queryTaller);
    if (!$stmtTaller) die("Error preparando consulta de taller: " . $conn->error);
    $stmtTaller->bind_param("i", $tallerId);
    if (!$stmtTaller->execute()) die("Error ejecutando consulta de taller: " . $stmtTaller->error);
    $resultTaller = $stmtTaller->get_result();
    if ($resultTaller->num_rows > 0) {
        $nombreTaller = $resultTaller->fetch_assoc()['nombre'];
    }
}

// 2. Obtener inscritos internos (tipo 1)
$queryInternos = "SELECT 
                    i.id_inscrito, 
                    i.matricula, 
                    i.nombre, 
                    i.paterno, 
                    i.materno,
                    'INTERNO' AS tipo
                FROM tall_inscritos i
                JOIN tall_talleres t ON i.id_taller = t.id_taller
                WHERE i.id_tipo = 1"; // Internos

// 3. Obtener inscritos externos (tipo 2)
$queryExternos = "SELECT 
                    i.id_inscrito, 
                    'EXTERNO' AS matricula, 
                    i.nombre, 
                    i.paterno, 
                    i.materno,
                    'EXTERNO' AS tipo
                FROM tall_inscritos i
                JOIN tall_talleres t ON i.id_taller = t.id_taller
                WHERE i.id_tipo = 2"; // Externos

// Aplicar filtros comunes
$whereCommon = "";
$params = [];
$types = "";

if ($tallerId > 0) {
    $whereCommon .= " AND i.id_taller = ?";
    $params[] = $tallerId;
    $types .= "i";
}

if (!empty($busqueda)) {
    $whereCommon .= " AND (i.matricula LIKE ? OR CONCAT(i.nombre, ' ', i.paterno, ' ', i.materno) LIKE ? OR i.num_movil LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if ($estado > 0) {
    $whereCommon .= " AND i.id_estado = ?";
    $params[] = $estado;
    $types .= "i";
}

$orderBy = " ORDER BY i.fecha_registro DESC";

// Preparar consultas con filtros
$queryInternos .= $whereCommon . $orderBy;
$queryExternos .= $whereCommon . $orderBy;

// Ejecutar consulta para internos
$stmtInternos = $conn->prepare($queryInternos);
if (!$stmtInternos) die("Error preparando consulta de internos: " . $conn->error);

if (!empty($params)) {
    $stmtInternos->bind_param($types, ...$params);
}

if (!$stmtInternos->execute()) die("Error ejecutando consulta de internos: " . $stmtInternos->error);
$internos = $stmtInternos->get_result()->fetch_all(MYSQLI_ASSOC);

// Ejecutar consulta para externos
$stmtExternos = $conn->prepare($queryExternos);
if (!$stmtExternos) die("Error preparando consulta de externos: " . $conn->error);

if (!empty($params)) {
    $stmtExternos->bind_param($types, ...$params);
}

if (!$stmtExternos->execute()) die("Error ejecutando consulta de externos: " . $stmtExternos->error);
$externos = $stmtExternos->get_result()->fetch_all(MYSQLI_ASSOC);

// Combinar resultados (primero internos, luego externos)
$inscritos = array_merge($internos, $externos);

// Crear PDF usando el formato base
class PDF extends Fpdi {
    protected $totalPages = 0;
    protected $isFirstPage = true;
    protected $skipHeader = false;
    
    function Header() {
        if ($this->skipHeader) {
            return;
        }
        
        // Solo mostrar encabezado en la primera página
        if ($this->isFirstPage) {
            // Importar página del formato base
            $this->setSourceFile('formato/formato.pdf');
            $tplIdx = $this->importPage(1);
            $this->useTemplate($tplIdx, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
            
            // Escribir el nombre del taller si está definido
            if (isset($this->taller)) {
                $this->SetFont('Arial', '', 10);
                $this->SetXY(41, 57.25);
                $this->Cell(0, 10, utf8_decode($this->taller), 0, 1, 'L');
            }
        }
    }
    
    function Footer() {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página (Página X/Y)
        $this->Cell(350, 10, $this->PageNo().'/{nb}', 0, 0, 'C');    }
    
    function SetTotalPages($total) {
        $this->totalPages = $total;
    }
    
    function SetFirstPage($value) {
        $this->isFirstPage = $value;
    }
    
    function IsFirstPage() {
        return $this->isFirstPage;
    }
    
    // Método para añadir página sin encabezado
    function AddPageWithoutHeader() {
        $this->skipHeader = true;
        $this->AddPage();
        $this->skipHeader = false;
    }
}

// Crear instancia PDF
$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->taller = $nombreTaller;

// Calcular número total de páginas necesarias
$rowsPerPage = floor((250 - 73) / 4.5); // Altura disponible / altura de fila
$totalPages = ceil(count($inscritos) / $rowsPerPage);
$pdf->SetTotalPages($totalPages);

$pdf->AddPage();

// Configuración inicial para los datos
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0, 0, 0);

// Posiciones y dimensiones
$startY = 73;       // Posición Y inicial de la primera fila
$rowHeight = 4.5;   // Altura de cada fila
$endY = 250;        // Posición Y máxima antes de nueva página
$leftMargin = 29.25; // Margen izquierdo de la tabla
$rightMargin = 180.75; // Margen derecho de la tabla

// Dibujar línea superior inicial
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line($leftMargin, $startY, $rightMargin, $startY);

$contador = 0;
$rowsInCurrentPage = 0;


foreach ($inscritos as $inscrito) {
    $contador++;
    $rowsInCurrentPage++;
    
    // Verificar si necesitamos nueva página (antes de dibujar la fila)
    if ($rowsInCurrentPage > 1 && ($startY + ($rowsInCurrentPage * $rowHeight) > $endY)) {
        // Dibujar bordes verticales para la página actual antes de crear una nueva
        $initialY = $pdf->IsFirstPage() ? 73 : 30;
        $finalY = $startY + (($rowsInCurrentPage - 1) * $rowHeight);
        
        $pdf->Line($leftMargin, $initialY, $leftMargin, $finalY);
        $pdf->Line($rightMargin, $initialY, $rightMargin, $finalY);
        
        $columnDividers = [36.55, 56, 99.75, 143.5];
        foreach ($columnDividers as $xPos) {
            $pdf->Line($xPos, $initialY, $xPos, $finalY);
        }
        
        $pdf->AddPageWithoutHeader();
        $pdf->SetFirstPage(false);
        $startY = 30;
        $rowsInCurrentPage = 1;
        
        // Dibujar línea superior en la nueva página
        $pdf->Line($leftMargin, $startY, $rightMargin, $startY);
    }
    
    $currentY = $startY + (($rowsInCurrentPage - 1) * $rowHeight);
    
    // # Consecutivo
    $pdf->SetXY(28, $currentY);
    $pdf->Cell(10, $rowHeight, $contador, 0, 0, 'C');
    
    // Matrícula
    $pdf->SetXY(33.5, $currentY);
    $pdf->Cell(25, $rowHeight, utf8_decode($inscrito['matricula']), 0, 0, 'C');
    
    // Apellido Paterno
    $pdf->SetXY(56, $currentY);
    $pdf->Cell(40, $rowHeight, utf8_decode($inscrito['paterno']), 0, 0, 'L');
    
    // Apellido Materno
    $pdf->SetXY(99.5, $currentY);
    $pdf->Cell(40, $rowHeight, utf8_decode($inscrito['materno']), 0, 0, 'L');
    
    // Nombre
    $pdf->SetXY(143.5, $currentY);
    $pdf->Cell(50, $rowHeight, utf8_decode($inscrito['nombre']), 0, 0, 'L');
    
    // Tipo (Interno/Externo)
    $pdf->SetXY(205, $currentY);
    $pdf->Cell(30, $rowHeight, utf8_decode($inscrito['tipo']), 0, 1, 'C');
    
    // Dibujar línea horizontal inferior
    $pdf->Line($leftMargin, $currentY + $rowHeight, $rightMargin, $currentY + $rowHeight);
}

// Dibujar bordes verticales para la última página
$initialY = $pdf->IsFirstPage() ? 73 : 30;
$finalY = $startY + ($rowsInCurrentPage * $rowHeight);

$pdf->Line($leftMargin, $initialY, $leftMargin, $finalY);
$pdf->Line($rightMargin, $initialY, $rightMargin, $finalY);

$columnDividers = [36.55, 56, 99.75, 143.5];
foreach ($columnDividers as $xPos) {
    $pdf->Line($xPos, $initialY, $xPos, $finalY);
}

// Nombre del archivo PDF
$nombreArchivo = 'Listado_Inscritos_' . ($tallerId > 0 ? str_replace(' ', '_', $nombreTaller) . '_' : '') . date('d-m-Y') . '.pdf';

// Salida del PDF
$pdf->Output('I', $nombreArchivo);