<?php
session_start();
if (isset($_SESSION['num_empleado'])){

    if (isset($_SESSION['id_tipo_usuario']) and $_SESSION['id_tipo_usuario'] > '3') {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../../../tickets/css/style.css">
    <link rel="stylesheet" href="../../../subsistemas/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="../../../subsistemas/js/catalogo.js"></script>
    <script src="../../../subsistemas/js/subsistemas.js"></script> -->
    <link rel="shortcut icon" href="../../../img/UNEVE.png">
    <title>Departamento de Actividades Culturales y Deportivas</title>
    <style>
        .main-header {
            /* background-color: #2c3e50; */
            /* color: black; */
            padding: 70px 0;
            text-align: center;
            /* margin-bottom: 30px; */
            border-radius: 5px;
        }
        
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        
        .service-card {
            width: 200px;
            height: 180px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: transform 0.3s ease;
            cursor: pointer;
            padding: 15px;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .service-icon {
            font-size: 40px;
            margin-bottom: 15px;
            color: #3498db;
        }
        
        .service-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .top-title {
            display: none;
        }
    </style>
</head>
<body>
  <!-- NAVBAR -->
<nav class="navbar">
    <img src="../../../img/uneve-text.png" alt="Logo" class="logo">
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

<body>
    <div class="main-header">
        <h1>DEPARTAMENTO DE ACTIVIDADES CULTURALES Y DEPORTIVAS</h1>
    </div>
    
    <div class="card-container">
        <div class="service-card" onclick="window.location.href='../consulta/consulta.php'">
            <div class="service-icon">👥</div>
            <div class="service-title">Consulta de inscritos</div>
        </div>
        
        <div class="service-card" onclick="window.location.href='../estadisticas/estadisticas.php'">
            <div class="service-icon">📊</div>
            <div class="service-title">Estadísticas</div>
        </div>
        
        <div class="service-card" onclick="window.location.href='../inscripcion/inscripcion.php'">
            <div class="service-icon">📝</div>
            <div class="service-title">Inscripción</div>
        </div>
        
        <div class="service-card" onclick="window.location.href='../horarios/horarios.php'">
            <div class="service-icon">⏰</div>
            <div class="service-title">Horarios</div>
        </div>
        
        <div class="service-card" onclick="window.location.href='../validacion/validacion.php'">
            <div class="service-icon">✅</div>
            <div class="service-title">Validación</div>
        </div>
    </div>
</body>

    <footer>
        <div class="barra-bottom">
            <img src="../../../tickets/imgs/logoedomex.png" alt="logoedomex" class="logoedomex">
            <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
        </div>
    </footer>
</html>