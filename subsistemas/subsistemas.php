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
    <link rel="stylesheet" href="../tickets/css/style.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/catalogo.js"></script>
    <script src="js/subsistemas.js"></script>
    <link rel="shortcut icon" href="../img/UNEVE.png">
    <title>Subsistemas</title>
</head>
<body>
  <!-- NAVBAR -->
<nav class="navbar">
    <img src="../img/uneve-text.png" alt="Logo" class="logo">
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

<body >
    <div class='top-title'>SUBSISTEMAS</div>
    <div class="container">
        <div class="card tickets">
            <img src="img/tickets.png" alt="Sistema de Tickets" class="icon"></a>
            <div class="card-text">SISTEMA DE TICKETS</div>
        </div>
        
        <div class="card add-users">
            <img src="img/agregar.png" alt="Agregar Usuarios" class="icon">
            <div class="card-text">AGREGAR USUARIOS</div>
        </div>
        
        <div class="card edit-users">
            <img src="img/editar.png" alt="Editar Usuarios" class="icon">
            <div class="card-text">EDITAR USUARIOS</div>
        </div>

        <div class="card update-password">
            <img src="img/reestablecer.png" alt="Actualizar Password" class="icon">
            <div class="card-text">ACTUALIZAR CONTRASEÑA</div>
        </div>
        </div>

</body>

    <footer>
        <div class="barra-bottom">
            <img src="../tickets/imgs/logoedomex.png" alt="logoedomex" class="logoedomex">
            <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
        </div>
    </footer>

    
</html>