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
    <title>INSCRIPCIÓN A TALLERES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="shortcut icon" href="../../../img/UNEVE.png">
    <style>
        .selection-card {
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .selection-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
            <h2 class="text-center">INSCRIPCIÓN A TALLERES</h2>
            <p class="text-center">Seleccione el tipo de inscripción</p>
        </div>
        
        <!-- Tarjetas de selección -->
        <div class="row justify-content-center mt-5">
            <div class="col-md-5 mb-4">
                <div class="card selection-card h-100 text-center" onclick="window.location.href='inscripcion_interno.php'">
                    <div class="card-body">
                        <i class="bi bi-person-vcard" style="font-size: 3rem; color: #0d6efd;"></i>
                        <h3 class="card-title mt-3">Interno</h3>
                        <p class="card-text">Seleccione esta opción si es alumno o personal de la UNEVE</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5 mb-4">
                <div class="card selection-card h-100 text-center" onclick="window.location.href='inscripcion_externo.php'">
                    <div class="card-body">
                        <i class="bi bi-person" style="font-size: 3rem; color: #0d6efd;"></i>
                        <h3 class="card-title mt-3">Externo</h3>
                        <p class="card-text">Seleccione esta opción si es público en general</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="barra-bottom">
        <img src="../../../img/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</body>
</html>