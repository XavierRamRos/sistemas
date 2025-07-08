<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo "No tienes permiso para acceder a esta página";
    exit();
}

// Incluir el archivo de conexión
require('../php/conexion.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../tickets/css/style.css">
    
    <link rel="shortcut icon" href="../img/uneve.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="../subsistemas/subsistemas.php">
        <img src="../img/uneve-text.png" alt="Logo" class="logo">
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

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">RESTABLECER CONTRASEÑA</h3>
                    </div>
                    <div class="card-body">
                        <form id="restablecerPasswordForm">
                            <div class="mb-3">
                                <label for="password_actual" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                            </div>
                            <div class="mb-3">
                                <label for="nueva_password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="nueva_password" name="nueva_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_password" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                            </div>
                            <button type="button" class="btn custom-button w-100" onclick="validarYRestablecer()">RESTABLECER</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/restablecer_password.js"></script>
</body>


<footer>
    <div class="barra-bottom">
        <img src="../tickets/imgs/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</html>