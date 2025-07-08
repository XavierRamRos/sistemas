<?php
session_start();
if (!isset($_SESSION['num_empleado']) || !isset($_SESSION['id_tipo_usuario']) || $_SESSION['id_tipo_usuario'] >= '3') {
    header('location: ../index.php');
    exit;
}

// Incluir el archivo de conexión
require('../php/conexion.php');

// Consulta para obtener los usuarios
$sql = "
    SELECT usuarios.*, areas.area 
    FROM usuarios 
    LEFT JOIN areas ON usuarios.id_area = areas.id_area 
    ORDER BY usuarios.id_usuario DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../tickets/css/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <link rel="shortcut icon" href="../tickets/imgs/UNEVE.png">
</head>
    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="../subsistemas/subsistemas.php">
            <img src="../tickets/imgs/uneve-text.png" alt="Logo" class="logo">
        </a>
        <div class="user-info">
            <span class="user-name">
                <div class="dropdown">
                    <button class="btn btn-etiqueta dropdown-toggle" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $_SESSION['nombre_completo']; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="../tickets/php/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </span>
        </div>
    </nav>

<body class="bg-light">

    <div class="container mt-4">
        <div class="ticket-header">
            <h2 class="text">ACTUALIZAR USUARIOS</h2>
        </div>
        <div class="card">
            <div class="card-body">
                <table id="usuariosTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>NOMBRE</th>
                            <th>ÁREA</th>
                            <th>CORREO</th>
                            <th>DETALLES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['nombre'] . " " . $usuario['apellido_paterno'] . " " . $usuario['apellido_materno']; ?></td>
                                <td><?php echo $usuario['area']; ?></td>
                                <td><?php echo $usuario['correo']; ?></td>
                                <td>
                                    <button class="btn btn-success btn-editar" data-id="<?php echo $usuario['id_usuario']; ?>">Editar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 65%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarForm">
                        <input type="hidden" id="usuario_id" name="usuario_id">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_nombre">Nombre</label>
                                <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_apellido_paterno">Apellido Paterno</label>
                                <input type="text" class="form-control" id="edit_apellido_paterno" name="apellido_paterno" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_apellido_materno">Apellido Materno</label>
                                <input type="text" class="form-control" id="edit_apellido_materno" name="apellido_materno" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_usuario">Usuario</label>
                                <input type="text" class="form-control" id="edit_usuario" name="usuario" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_area">Área</label>
                                <select class="form-select" id="edit_area" name="area" required>
                                <option value="">Seleccione un área</option>
                                <option value="1">1. RECTORÍA</option>
                                <option value="2">2. UNIDAD DE INFORMACIÓN, PLANEACIÓN, PROGRAMACIÓN Y EVALUACIÓN
                                </option>
                                <option value="3">3. SECRETARIA ADMINISTRATIVA</option>
                                <option value="4">4. ABOGACÍA GENERAL E IGUALDAD DE GENERO</option>
                                <option value="5">5. SECRETARIA ACADÉMICA</option>
                                <option value="6">6. SERVICIOS ESCOLARES</option>
                                <option value="7">7. DIRECCIÓN DE LA LICENCIATURA EN QUIROPRÁCTICA</option>
                                <option value="8">8. LICENCIATURA EN ACUPUNTURA HUMANA REHABILITADORA</option>
                                <option value="9">9. DIRECCIÓN DE LA LICENCIATURA EN GERONTOLOGÍA</option>
                                <option value="10">10. DIRECCIÓN DE LA LICENCIATURA EN HUMANIDADES EMPRESA</option>
                                <option value="11">11. DIRECCIÓN DE LA LICENCIATURA EN GASTRONOMÍA NUTRICIONAL</option>
                                <option value="12">12. DIRECCIÓN DE INGENIERÍA EN LOGÍSTICA AEROPORTUARIA</option>
                                <option value="13">13. DIRECCIÓN DE INGENIERÍA EN COMUNICACIÓN MULTIMEDIA</option>
                                <option value="14">14. DEPARTAMENTO DE ADMINISTRACIÓN DE PERSONAL</option>
                                <option value="15">15. DEPARTAMENTO DE RECURSOS MATERIALES Y SERVICIOS GENERALES
                                </option>
                                <option value="16">16. DEPARTAMENTO DE PRESUPUESTO Y CONTABILIDAD</option>
                                <option value="17">17. DEPARTAMENTO DE INFORMÁTICA</option>
                                <option value="18">18. CLÍNICA INTEGRAL UNIVERSITARIA</option>
                                <option value="19">19. DIRECCIÓN DE PROMOCIÓN EDUCATIVA Y VINCULACIÓN</option>
                                <option value="20">20. DEPARTAMENTO DE VINCULACIÓN Y DIFUSIÓN</option>
                                <option value="21">21. DEPARTAMENTO DE ACTIVIDADES CULTURALES Y DEPORTIVASDEPARTAMENTO
                                    DE ACTIVIDADES CULTURALES Y DEPORTIVAS</option>
                                <option value="22">22. DEPARTAMENTO DE SERVICIOS BIBLIOTECARIOS</option>
                                <option value="23">23. ÁREA DE CALIDAD</option>
                                <option value="24">24. ÁREA DE ARCHIVO</option>
                                <option value="25">25. SEGUIMIENTO DE EGRESADOS</option>
                                <option value="26">26. ÓRGANO INTERNO DE CONTROL</option>
                                <option value="27">27. MAESTRÍA EN CIENCIAS DEL DEPORTE Y EL EJERCICIO</option>
                                <option value="28">28. ÁREA DE AUDITORÍA DEL OIC</option>
                                <option value="29">29. SERVICIO MEDICO</option>
                                <option value="30">30. SERVICIO PSICOLÓGICO</option>     
                                <option value="31">31. ÁREA DE QUEJAS</option>  
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_puesto">Puesto</label>
                                <input type="text" class="form-control" id="edit_puesto" name="puesto" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_num_empleado">Número de Empleado</label>
                                <input type="text" class="form-control" id="edit_num_empleado" name="num_empleado" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_extension">Extensión</label>
                                <input type="text" class="form-control" id="edit_extension" name="extension" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_correo">Correo</label>
                                <input type="email" class="form-control" id="edit_correo" name="correo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_tipo_usuario">Tipo de Usuario</label>
                                <select class="form-select" id="edit_tipo_usuario" name="tipo_usuario" required>
                                    <option value="1">1. Administrador</option>
                                    <option value="2">2. Soporte Técnico</option>
                                    <option value="3">3. Usuario</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit_password">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                                <input type="password" class="form-control" id="edit_password" name="password">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>    
                    <button type="button" class="btn btn-danger" onclick="eliminarUsuario()">Eliminar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizarUsuario()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/editar_usuario.js"></script>

    <footer>
        <div class="barra-bottom">
            <img src="../tickets/imgs/logoedomex.png" alt="logoedomex" class="logoedomex">
            <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
        </div>
    </footer>
</body>
</html>