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
    <title>INSCRIPCIÓN A TALLERES - EXTERNO</title>
    <!-- Cargar jQuery PRIMERO -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="shortcut icon" href="../../../img/UNEVE.png">
    <style>
        .form-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-section {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #0d6efd;
        }
        .form-section h5 {
            color: #0d6efd;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .required-field::after {
            content: " *";
            color: red;
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
            <h2 class="text-center">INSCRIPCIÓN A TALLERES - EXTERNO</h2>
        </div>
        
        <!-- Contenedor principal -->
        <div class="form-container">
            <form id="formInscripcion">
                <!-- Sección 1: Datos personales -->
                <div class="form-section">
                    <h5>Datos Personales</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nombre" class="form-label required-field">Nombre(s)</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="paterno" class="form-label required-field">Apellido Paterno</label>
                                <input type="text" class="form-control" id="paterno" name="paterno" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="materno" class="form-label required-field">Apellido Materno</label>
                                <input type="text" class="form-control" id="materno" name="materno" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                 <label for="taller" class="form-label required-field">Taller</label>
                                <select class="form-select" id="taller" name="taller" required>
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="dia" class="form-label required-field">Día</label>
                                <select class="form-select" id="dia" name="dia" required disabled>
                                    <option value="">Primero seleccione un taller</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="horario" class="form-label required-field">Horario</label>
                                <select class="form-select" id="horario" name="horario" required disabled>
                                    <option value="">Primero seleccione un día</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label required-field">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="id_sexo" class="form-label required-field">Sexo</label>
            <select class="form-select" id="id_sexo" name="id_sexo" required>
                <option value="">Seleccionar sexo</option>
                <?php
                $query = "SELECT id_sexo, nombre FROM tall_sexo";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="'.$row['id_sexo'].'">'.$row['nombre'].'</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="num_movil" class="form-label required-field">Número de Teléfono</label>
            <input type="tel" class="form-control" id="num_movil" name="num_movil" required>
        </div>
    </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="correo" class="form-label required-field">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="id_medio" class="form-label required-field">¿Cómo se enteró del taller?</label>
                                <select class="form-select" id="id_medio" name="id_medio" required>
                                    <option value="">Seleccionar medio</option>
                                    <?php
                                    $query = "SELECT id_medio, nombre FROM tall_medio";
                                    $result = $conn->query($query);
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="'.$row['id_medio'].'">'.$row['nombre'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 2: Domicilio -->
                <div class="form-section">
                    <h5>Domicilio</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="calle" class="form-label required-field">Calle</label>
                                <input type="text" class="form-control" id="calle" name="calle" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="colonia" class="form-label required-field">Colonia</label>
                                <input type="text" class="form-control" id="colonia" name="colonia" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="num_exterior" class="form-label">Número Exterior</label>
                                <input type="text" class="form-control" id="num_exterior" name="num_exterior">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="num_interior" class="form-label">Número Interior</label>
                                <input type="text" class="form-control" id="num_interior" name="num_interior">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 3: Salud -->
                <div class="form-section">
                    <h5>Información de Salud</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="id_salud" class="form-label required-field">Sistema de Salud</label>
                                <select class="form-select" id="id_salud" name="id_salud" required>
                                    <option value="">Seleccionar sistema de salud</option>
                                    <?php
                                    $query = "SELECT id_salud, nombre FROM seguro_social";
                                    $result = $conn->query($query);
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="'.$row['id_salud'].'">'.$row['nombre'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="num_medico" class="form-label">Número de Expediente Médico</label>
                                <input type="text" class="form-control" id="num_medico" name="num_medico">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="padecimiento" class="form-label">Padecimiento(s)</label>
                                <textarea class="form-control" id="padecimiento" name="padecimiento" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="alergia" class="form-label">Alergia(s)</label>
                                <textarea class="form-control" id="alergia" name="alergia" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 4: Contacto alterno -->
                <div class="form-section">
                    <h5>Contacto Alterno (Familiar)</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nombre_alt" class="form-label">Nombre(s)</label>
                                <input type="text" class="form-control" id="nombre_alt" name="nombre_alt">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="paterno_alt" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="paterno_alt" name="paterno_alt">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="movil_alt" class="form-label">Número de Teléfono</label>
                                <input type="tel" class="form-control" id="movil_alt" name="movil_alt">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="calle_alt" class="form-label">Calle</label>
                                <input type="text" class="form-control" id="calle_alt" name="calle_alt">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="colonia_alt" class="form-label">Colonia</label>
                                <input type="text" class="form-control" id="colonia_alt" name="colonia_alt">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="num_externo_alt" class="form-label">Número Exterior</label>
                                <input type="text" class="form-control" id="num_externo_alt" name="num_externo_alt">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="num_interno_alt" class="form-label">Número Interior</label>
                                <input type="text" class="form-control" id="num_interno_alt" name="num_interno_alt">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 5: Pago -->
                <div class="form-section">
                    <h5>Información de Pago</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="linea_captura" class="form-label required-field">Línea de Captura</label>
                                <input type="text" class="form-control" id="linea_captura" name="linea_captura" required>
                                <small class="text-muted">Ingrese el número de línea de captura del pago</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campos ocultos -->
                <input type="hidden" id="id_usuario_registro" name="id_usuario_registro" value="<?php echo $_SESSION['id_usuario']; ?>">
                <input type="hidden" id="fecha_registro" name="fecha_registro" value="<?php echo date('Y-m-d H:i:s'); ?>">
                <input type="hidden" id="id_tipo" name="id_tipo" value="2"> <!-- 2 = Externo -->
                
                <!-- Botones -->
                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> Guardar Inscripción
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg ms-3">
                            <i class="bi bi-eraser"></i> Limpiar Formulario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/inscripcion_externo.js"></script>    

<footer>
    <div class="barra-bottom">
        <img src="../../../img/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</body>
</html>