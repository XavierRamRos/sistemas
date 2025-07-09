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
$tipo = isset($_GET['tipo']) ? intval($_GET['tipo']) : 0; // Nuevo parámetro para tipo

// 1. Obtener información del taller (si se especificó)
$nombreTaller = "Todos los talleres";
$estadoFiltro = "Todos los estados";
$tipoFiltro = "Todos los tipos";

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

// Obtener nombre del estado si se filtró
if ($estado > 0) {
    $queryEstado = "SELECT nombre FROM tall_estado_taller WHERE id_estado = ?";
    $stmtEstado = $conn->prepare($queryEstado);
    if (!$stmtEstado) die("Error preparando consulta de estado: " . $conn->error);
    $stmtEstado->bind_param("i", $estado);
    if (!$stmtEstado->execute()) die("Error ejecutando consulta de estado: " . $stmtEstado->error);
    $resultEstado = $stmtEstado->get_result();
    if ($resultEstado->num_rows > 0) {
        $estadoFiltro = $resultEstado->fetch_assoc()['nombre'];
    }
}

// Obtener nombre del tipo si se filtró
if ($tipo > 0) {
    $queryTipo = "SELECT nombre FROM tall_usuario_tipo WHERE id_tipo = ?";
    $stmtTipo = $conn->prepare($queryTipo);
    if (!$stmtTipo) die("Error preparando consulta de tipo: " . $conn->error);
    $stmtTipo->bind_param("i", $tipo);
    if (!$stmtTipo->execute()) die("Error ejecutando consulta de tipo: " . $stmtTipo->error);
    $resultTipo = $stmtTipo->get_result();
    if ($resultTipo->num_rows > 0) {
        $tipoFiltro = $resultTipo->fetch_assoc()['nombre'];
    } else {
        $tipoFiltro = ($tipo == 1) ? "INTERNOS" : "EXTERNOS";
    }
}

// Consulta unificada para obtener todos los inscritos con sus tipos
$query = "SELECT 
            i.id_inscrito, 
            IF(i.id_tipo = 1, i.matricula, 'EXTERNO') AS matricula,
            i.nombre, 
            i.paterno, 
            i.materno,
            IF(i.id_tipo = 1, 'INTERNO', 'EXTERNO') AS tipo,
            et.nombre AS estado
          FROM tall_inscritos i
          JOIN tall_talleres t ON i.id_taller = t.id_taller
          LEFT JOIN tall_estado_taller et ON i.id_estado = et.id_estado
          WHERE 1=1";

// Aplicar filtros
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

// Nuevo filtro por tipo de usuario
if ($tipo > 0) {
    $whereCommon .= " AND i.id_tipo = ?";
    $params[] = $tipo;
    $types .= "i";
}

$orderBy = " ORDER BY i.id_tipo ASC, i.fecha_registro DESC";

// Preparar consulta final
$query .= $whereCommon . $orderBy;

// Ejecutar consulta
$stmt = $conn->prepare($query);
if (!$stmt) die("Error preparando consulta: " . $conn->error);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) die("Error ejecutando consulta: " . $stmt->error);
$inscritos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Crear PDF usando el formato base
class PDF extends Fpdi {
    protected $totalPages = 0;
    protected $isFirstPage = true;
    protected $skipHeader = false;
    protected $taller = "";
    protected $estado = "";
    protected $tipo = "";
    
    // Métodos públicos para establecer los valores
    public function setTaller($taller) {
        $this->taller = $taller;
    }
    
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
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
            $this->SetFont('Arial', '', 10);
            
            // Taller
            $this->SetXY(41, 57.25);
            $this->Cell(0, 10, utf8_decode($this->taller), 0, 1, 'L');
        }
    }
    
    function Footer() {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página (Página X/Y)
        $this->Cell(350, 10, $this->PageNo().'/{nb}', 0, 0, 'C');
    }
    
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

// Usar los métodos setters para establecer los valores
$pdf->setTaller($nombreTaller);
$pdf->setEstado($estadoFiltro);
$pdf->setTipo($tipoFiltro);

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
$nombreArchivo = 'Listado_Inscritos_' . 
                ($tallerId > 0 ? str_replace(' ', '_', $nombreTaller) . '_' : '') . 
                ($estado > 0 ? 'Estado_' . str_replace(' ', '_', $estadoFiltro) . '_' : '') . 
                ($tipo > 0 ? 'Tipo_' . str_replace(' ', '_', $tipoFiltro) . '_' : '') . 
                date('d-m-Y') . '.pdf';

// Salida del PDF
$pdf->Output('I', $nombreArchivo);