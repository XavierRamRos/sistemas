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
    <title>ESTADÍSTICAS DE TALLERES</title>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="shortcut icon" href="../../../img/UNEVE.png">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Flatpickr para fechas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .card-stat {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .stat-label {
            font-size: 1rem;
            color: #6c757d;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 30px;
        }
        .filter-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
        .bg-fondo  {
            background: #a02444;
        }
    </style>
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
        <div class="ticket-header">
            <h2 class="text-center">ESTADÍSTICAS DE TALLERES</h2>
        </div>
        
<!-- Filtros -->
<div class="filter-container container-fluid">
    <div class="row g-2 align-items-end">

        <div class="col-lg-3 col-md-6">
            <label for="tallerFiltro" class="form-label">Taller</label>
            <select id="tallerFiltro" class="form-select">
                <option value="0">Todos los talleres</option>
                <?php
                $query = "SELECT id_taller, nombre FROM tall_talleres";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="'.$row['id_taller'].'">'.$row['nombre'].'</option>';
                }
                ?>
            </select>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <label for="estadoFiltro" class="form-label">Estado</label>
            <select id="estadoFiltro" class="form-select">
                <option value="0">Todos</option>
                <option value="1">Activos</option>
                <option value="3">De baja</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <label for="sexoFiltro" class="form-label">Sexo</label>
            <select id="sexoFiltro" class="form-select">
                <option value="0">Todos</option>
                <option value="1">Hombre</option>
                <option value="2">Mujer</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-6">
            <label for="fechaInicio" class="form-label">Fecha inicio <span class="text-danger">*</span></label>
            <input type="date" id="fechaInicio" class="form-control" required>
        </div>

        <div class="col-lg-2 col-md-6">
            <label for="fechaFin" class="form-label">Fecha fin <span class="text-danger">*</span></label>
            <input type="date" id="fechaFin" class="form-control" required>
        </div>

        <div class="col-lg-1 d-flex gap-2">
            <button id="btnFiltrar" class="btn btn-primary w-100">
                <i class="bi bi-funnel"></i>
            </button>
            <button id="btnResetFilters" class="btn btn-secondary w-100">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
        </div>

            </div>
        </div>
        
        <!-- Estadísticas generales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stat bg-white">
                    <div class="card-body text-center">
                        <div class="stat-number" id="totalInscritos">0</div>
                        <div class="stat-label">Total inscritos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-white">
                    <div class="card-body text-center">
                        <div class="stat-number" id="totalHombres">0</div>
                        <div class="stat-label">Hombres</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-white">
                    <div class="card-body text-center">
                        <div class="stat-number" id="totalMujeres">0</div>
                        <div class="stat-label">Mujeres</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-white">
                    <div class="card-body text-center">
                        <div class="stat-number" id="totalBajas">0</div>
                        <div class="stat-label">Bajas</div>
                    </div>
                </div>
            </div>
        </div>
        

<!-- Nuevas secciones de Externos/Internos y Activos/De baja -->
<div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-fondo text-white">
                        <h5 class="mb-0">Tipo de usuario</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="tipoUsuarioChart"></canvas>
                        </div>
                        <div class="row mt-3 text-center">
                            <div class="col-md-6">
                                <div class="stat-number" id="totalInternos">0</div>
                                <div class="stat-label">Internos</div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-number" id="totalExternos">0</div>
                                <div class="stat-label">Externos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-fondo text-white">
                        <h5 class="mb-0">Estado de inscripción</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="estadoInscripcionChart"></canvas>
                        </div>
                        <div class="row mt-3 text-center">
                            <div class="col-md-6">
                                <div class="stat-number" id="totalActivos">0</div>
                                <div class="stat-label">Activos</div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-number" id="totalBajasEstado">0</div>
                                <div class="stat-label">De baja</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


          <!-- Gráficos -->
          <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-fondo text-white">
                        <h5 class="mb-0">Inscritos por taller</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="inscritosTallerChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-fondo text-white">
                        <h5 class="mb-0">Distribución por sexo</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="sexoChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-fondo text-white">
                            <h5 class="mb-0">Horarios más frecuentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="horariosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-fondo text-white">
                            <h5 class="mb-0">Inscritos por periodo</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="periodoChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br><br>


<script src="js/estadisticas_talleres.js"></script>

<footer>
    <div class="barra-bottom">
        <img src="../../../img/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</body>
</html>