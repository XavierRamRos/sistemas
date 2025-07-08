<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('location: ../../../index.php');
    exit();
}

// Conexión a la base de datos - CORREGIDO LA RUTA
require_once '../../../php/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>GESTIÓN DE HORARIOS DE TALLERES</title>
    <!-- Cargar jQuery PRIMERO -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="shortcut icon" href="../../../img/UNEVE.png">
    <style>
        .horario-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .horario-item {
            background-color: white;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #0d6efd;
        }
        .btn-horario {
            margin-right: 5px;
        }
        .table-horarios {
            width: 100%;
        }
        .table-horarios th {
            background-color: #f1f1f1;
            text-align: center;
        }
        .table-horarios td {
            vertical-align: middle;
        }
        /* Estilo para el botón de nuevo horario */
        #btnNuevoHorario {
            transition: all 0.3s ease;
        }
        #btnNuevoHorario:hover {
            transform: scale(1.05);
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
            <h2 class="text-center">GESTIÓN DE HORARIOS DE TALLERES</h2>
        </div>
        
        <!-- Contenedor principal -->
        <div class="horario-container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Asignar Horario a Taller</h4>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" id="btnNuevoHorario">
                        <i class="bi bi-plus-circle"></i> Nuevo Horario
                    </button>
                </div>
            </div>
            
            <!-- Formulario para asignar horario -->
            <div class="row mb-4" id="formHorario" style="display: none;">
                <div class="col-md-12">
                    <form id="horarioForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="selectTaller" class="form-label">Taller</label>
                                    <select class="form-select" id="selectTaller" required>
                                        <option value="">Seleccionar taller</option>
                                        <?php
                                        $query = "SELECT id_taller, nombre FROM tall_talleres";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="'.$row['id_taller'].'">'.$row['nombre'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="selectDia" class="form-label">Día</label>
                                    <select class="form-select" id="selectDia" required>
                                        <option value="">Seleccionar día</option>
                                        <?php
                                        $query = "SELECT id_dia, nombre FROM tall_dias";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="'.$row['id_dia'].'">'.$row['nombre'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="selectHoraInicio" class="form-label">Hora inicio</label>
                                    <select class="form-select" id="selectHoraInicio" required>
                                        <option value="">Seleccionar hora</option>
                                        <?php
                                        $query = "SELECT id_horario, hora FROM tall_horario";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="'.$row['id_horario'].'">'.$row['hora'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="selectHoraFin" class="form-label">Hora fin</label>
                                    <select class="form-select" id="selectHoraFin" required>
                                        <option value="">Seleccionar hora</option>
                                        <?php
                                        $query = "SELECT id_horario, hora FROM tall_horario";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="'.$row['id_horario'].'">'.$row['hora'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabla de horarios asignados -->
<div class="row">
    <div class="col-md-12">
        <h4>Horarios Asignados</h4>
        <div class="table-responsive">
            <table class="table table-hover table-horarios">
                <thead>
                    <tr>
                        <th>Taller</th>
                        <th>Día</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaHorarios">
                    <!-- Los horarios se cargarán aquí mediante AJAX -->
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
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este horario?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="js/horarios_talleres.js"></script>    

<footer>
    <div class="barra-bottom">
        <img src="../../../img/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</body>
</html>