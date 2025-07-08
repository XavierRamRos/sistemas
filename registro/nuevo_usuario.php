<?php
session_start();
if (isset($_SESSION['correo'])) {

    if (isset($_SESSION['id_tipo_usuario']) and $_SESSION['id_tipo_usuario'] >= '3' ) {
        header('location: ../index.php');
    }
} else {
    header('location: ../index.php');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../tickets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                    <?php echo $_SESSION['nombre_completo']; ?><i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="../tickets/php/logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </span>
    </div>
</nav>

<!-- body -->

<body class="bg-light">
    <div class="container">

        <div class="ticket-header">
            <h2 class="text">NUEVO USUARIO</h2>
        </div>
    <div class="form-container">

            <div class="card-body">
                <form id="registroForm">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required placeholder='Ingrese el nombre'>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno"
                                required placeholder='Ingrese el apellido paterno'>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"
                                required placeholder='Ingrese el apellido materno'>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required placeholder='Ingrese el usuario'>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="area" class="form-label">Área</label>
                            <select class="form-select" id="area" name="area" required>
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
                                <option value="21">21. DEPARTAMENTO DE ACTIVIDADES CULTURALES Y DEPORTIVAS</option>
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
                            <label for="puesto" class="form-label">Puesto</label>
                            <input type="text" class="form-control" id="puesto" name="puesto" required placeholder='Ingrese el puesto'>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="num_empleado" class="form-label">Número de Empleado</label>
                            <input type="text" class="form-control" id="num_empleado" name="num_empleado" required placeholder='Ingrese el número de empleado'>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="extension" class="form-label">Extensión</label>
                            <input type="text" class="form-control" id="extension" name="extension" required placeholder='Ingrese la extensión'>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                            <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                                <option value="">Seleccione tipo</option>
                                <option value="1">1. Administrador</option>
                                <option value="2">2. Soporte Técnico</option>
                                <option value="3">3. Usuario</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo" required placeholder='Ingrese el correo'>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                                <input type="text" class="form-control" id="password" name="password" placeholder='Ingrese la contraseña'>

                            </div>
                        </div>

                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-registrar">Registrar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
        </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="js/registro.js"></script>
</body>

<footer>
    <div class="barra-bottom">
        <img src="../tickets/imgs/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</html>