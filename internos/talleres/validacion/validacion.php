<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('location: ../../../index.php');
    exit();
}

require_once '../../../php/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>VALIDACIÓN INTERNOS</title>
    <!-- Cargar jQuery PRIMERO -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="../../../img/UNEVE.png">
</head>

<body data-role="<?php echo $_SESSION['id_tipo_usuario']; ?>" class="bg-light">

<!-- NAVBAR -->
<div class="content">
    <nav class="navbar">
        <button type="button" id="sidebarCollapse" class="menu-toggle">
            <span>&#9776;</span>
        </button>
        <a href="../../../subsistemas/subsistemas.php">
            <img src="../../../img/uneve-text.png" alt="Logo" class="logo">
        </a>
        <div class="user-info">
            <span class="user-name">
                <div class="dropdown">
                    <button class="btn btn-etiqueta dropdown-toggle" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $_SESSION['nombre_completo']; ?><i
                            class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="../../../php/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </span>
        </div>
    </nav>

   <!-- Encabezado -->
<div class="container">
    <div class="ticket-header d-flex justify-content-between align-items-center">
        <h2 class="text-center m-0">VALIDACIÓN INTERNOS</h2>
        <a href="../externo/validacion.php" class="btn btn-primary">EXTERNOS</a>
    </div>
        
<!-- Contenedor principal -->
<!-- Contenedor principal -->
<div class="consulta-container">
    <!-- Sección de filtros -->
    <div class="filtros-section">
        <form id="formFiltros">
            <div class="row align-items-center g-2">
                <!-- Campo de búsqueda principal -->
                <div class="col-sm-6 col-md-8">
                    <input type="text" class="form-control" id="filtroBusqueda" name="filtroBusqueda" 
                           placeholder="Ingrese matrícula, nombre completo o número de teléfono">
                </div>
                
                <!-- Select de talleres -->
                <div class="col-sm-3 col-md-2">
                    <select class="form-select" id="filtroTaller" name="filtroTaller">
                        <option value="">TODOS LOS TALLERES</option>
                        <?php
                        $query = "SELECT id_taller, nombre FROM tall_talleres";
                        $result = $conn->query($query);
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="'.$row['id_taller'].'">'.$row['nombre'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <!-- Botones -->
                <div class="col-sm-3 col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1" id="btnBuscar">
                            <span id="searchText"><i class="bi bi-search"></i> Buscar</span>
                            <span id="searchLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Buscando...
                            </span>
                        </button>
                        <button type="button" id="btnLimpiar" class="btn btn-secondary flex-grow-1">
                            <i class="bi bi-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Resultados -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-inscripciones">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nombre Completo</th>
                            <th>Carrera</th>
                            <th>Taller</th>
                            <th>Días Activo</th>
                            <th>Fecha Inscripción</th>
                            <th>Última Modificación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaInscripciones">
                        <!-- Los resultados se cargarán aquí mediante AJAX -->
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

<!-- Modal para detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 60%;">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DETALLES DE INSCRIPCIÓN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detalleInscripcion">
                <!-- Los detalles se cargarán aquí -->
            </div>
            <div class="modal-footer">
    <button type="button" class="btn btn-danger" id="btnCancelarInscripcion" data-id="">Cancelar Inscripción</button>
    <button type="button" class="btn btn-success" id="btnRenovarInscripcion" data-id="">Renovar Inscripción</button>
</div>
        </div>
    </div>
</div>

<script src="js/validacion.js"></script>    

<footer>
    <div class="barra-bottom">
        <img src="../../../img/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</body>
</html>