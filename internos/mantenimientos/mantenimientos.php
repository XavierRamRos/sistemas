<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('location: ../../../index.php');
    exit();
}

require_once '../../php/conexion.php';

// Obtener áreas para el filtro
$areas_query = "SELECT id_area, area FROM areas";
$areas_result = $conn->query($areas_query);
$areas = [];
while ($row = $areas_result->fetch_assoc()) {
    $areas[] = $row;
}

// Obtener usuarios de mantenimiento (3, 42, 43, 48)
$usuarios_mtto_query = "SELECT id_usuario, CONCAT(nombre, ' ', apellido_paterno) AS nombre_completo 
                        FROM usuarios 
                        WHERE id_usuario IN (3, 42, 43, 48)";
$usuarios_mtto_result = $conn->query($usuarios_mtto_query);
$usuarios_mtto = [];
while ($row = $usuarios_mtto_result->fetch_assoc()) {
    $usuarios_mtto[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>REPORTES DE MANTENIMIENTO</title>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="../../img/UNEVE.png">
</head>

<body data-role="<?php echo $_SESSION['id_tipo_usuario']; ?>" class="bg-light">

<!-- NAVBAR -->
<div class="content">
    <nav class="navbar">
        <button type="button" id="sidebarCollapse" class="menu-toggle">
            <span>&#9776;</span>
        </button>
        <a href="../../subsistemas/subsistemas.php">
            <img src="../../img/uneve-text.png" alt="Logo" class="logo">
        </a>
        <div class="user-info">
            <span class="user-name">
                <div class="dropdown">
                    <button class="btn btn-etiqueta dropdown-toggle" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido_paterno']; ?>
                        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="../../php/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </span>
        </div>
    </nav>

    <!-- Encabezado -->
    <div class="container">
        <div class="ticket-header">
            <h2 class="text-center">REPORTES DE MANTENIMIENTO</h2>
        </div>
        
        <!-- Contenedor principal -->
        <div class="consulta-container">
            <!-- Sección de filtros -->
            <div class="filtros-section">
                <form id="formFiltros">
                    <div class="row g-3">
                        <!-- Filtro de Área -->
                        <div class="col-md-3">
                            <label for="filtroArea" class="form-label">Área</label>
                            <select class="form-select" id="filtroArea" name="filtroArea">
                                <option value="">TODAS LAS ÁREAS</option>
                                <?php foreach ($areas as $area): ?>
                                    <option value="<?php echo $area['id_area']; ?>"><?php echo $area['area']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Filtro de Fecha Inicio -->
                        <div class="col-md-2">
                            <label for="filtroFechaInicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="filtroFechaInicio" name="filtroFechaInicio">
                        </div>
                        
                        <!-- Filtro de Fecha Fin -->
                        <div class="col-md-2">
                            <label for="filtroFechaFin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="filtroFechaFin" name="filtroFechaFin">
                        </div>
                        
                        <!-- Filtro de Estado -->
                        <div class="col-md-2">
                            <label for="filtroEstado" class="form-label">Estado</label>
                            <select class="form-select" id="filtroEstado" name="filtroEstado">
                                <option value="">TODOS</option>
                                <option value="validado">VALIDADO</option>
                                <option value="por_validar">POR VALIDAR</option>
                            </select>
                        </div>
                        
                        <!-- Filtro de Usuario Mantenimiento -->
                        <div class="col-md-3">
                            <label for="filtroUsuarioMtto" class="form-label">Usuario Mantenimiento</label>
                            <select class="form-select" id="filtroUsuarioMtto" name="filtroUsuarioMtto">
                                <option value="">TODOS</option>
                                <?php foreach ($usuarios_mtto as $usuario): ?>
                                    <option value="<?php echo $usuario['id_usuario']; ?>"><?php echo $usuario['nombre_completo']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Botones -->
                        <div class="col-md-12 d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary" id="btnBuscar">
                                <span id="searchText"><i class="bi bi-search"></i> Buscar</span>
                                <span id="searchLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Buscando...
                                </span>
                            </button>
                            <button type="button" id="btnLimpiar" class="btn btn-secondary">
                                <i class="bi bi-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Botón para generar PDF -->
            <div class="row mt-3">
                <div class="col-md-12 text-end">
                    <button id="btnGenerarPDF" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                    </button>
                </div>
            </div>
            
            <!-- Resultados -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover table-mantenimientos">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Solicitante</th>
                                    <th>Técnico</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Término</th>
                                    <th>Fecha Validación</th>
                                    <th>Estado</th>
                                    <th>Equipos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMantenimientos">
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Ingrese criterios de búsqueda y presione "Buscar"</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginación -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center" id="pagination">
                            <!-- Los controles de paginación se generarán aquí -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles de equipos -->
<div class="modal fade" id="detalleEquiposModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DETALLES DE EQUIPOS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Inventario</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody id="detalleEquiposBody">
                            <!-- Los detalles de equipos se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="js/reportes_mantenimiento.js"></script>    

<footer>
    <div class="barra-bottom">
        <img src="../../img/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</body>
</html>