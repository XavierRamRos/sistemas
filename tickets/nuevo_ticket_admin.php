<?php
session_start();
if (isset($_SESSION['num_empleado'])) {

    if (isset($_SESSION['id_tipo_usuario']) and $_SESSION['id_tipo_usuario'] === '3') {
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
    <title>NUEVO TICKET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="../img/UNEVE.png">
</head>
<!-- NAVBAR -->
<nav class="navbar">

<a href="../subsistemas/subsistemas.php">
    <img src="imgs/uneve-text.png" alt="Logo" class="logo">
</a>    <div class="user-info">
        <span class="user-name">
            </style>
            <div class="dropdown">
                <button class="btn btn-etiqueta dropdown-toggle" type="button" id="dropdownMenuButton1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo $_SESSION['nombre_completo']; ?><i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="php/logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </span>
    </div>
</nav>

<!-- Encabezado Tickets -->

<body class="bg-light">
    <div class="container">
        <div class="ticket-header">
            <h2 class="text">NUEVO TICKET</h2>
            <a href="mis_tickets_admin.php" class="mis-tickets-btn">MIS TICKETS</a>
        </div>

        <form class="form-container" id="ticketForm" method="POST">
            <div class="form-row">
                <div class="form-column">
                    <div class="form-group">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $_SESSION['nombre_completo']; ?>" readonly>
            </div>

                    <div class="form-group">
                        <label for="asunto" class="form-label">Asunto</label>
                        <input type="text" class="form-control" id="asunto" name="asunto" required
                            placeholder="Ingrese el asunto">
                    </div>
                    <div class="form-group">
                        <label for="categoria" class="form-label">Categoría</label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <option value="">Seleccionar Categoría</option>
                            <option value="Computadora">Computadora</option>
                            <option value="Telefono">Teléfono</option>
                            <option value="Red">Red</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required
                            placeholder="Ingrese la marca">
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-group">
                        <label for="area" class="form-label">Área</label>
                        <input type="text" class="form-control" id="area" name="area" value="<?php echo $_SESSION['area']; ?>" readonly>

                    </div>
                    <div class=" form-group">
                        <label for="numero_inventario" class="form-label">N° de Inventario</label>
                        <input type="text" class="form-control" id="numero_inventario" name="numero_inventario" required
                            placeholder="Ingrese el número de inventario">
                    </div>


                    <div class="form-group">
                        <label for="medio_soli" class="form-label">Medio de solicitud</label>
                        <select class="form-select" id="medio_soli" name="medio_soli" required>
                            <option value="">Seleccionar Medio de Solicitud</option>
                            <option value="1">Personal</option>
                            <option value="2">Teléfono</option>
                            <option value="3">Sistema</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required
                            placeholder="Ingrese el modelo">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="descripcion" class="form-label">DESCRIPCIÓN DETALLADA DEL PROBLEMA</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">Enviar</button>

        </form>
    </div>


    <script src="js/js.js"></script>
</body>
<footer>
    <div class="barra-bottom">
            <img src="imgs/logoedomex.png" alt="logoedomex" class="logoedomex">
            <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
        </div>
        </footer>
</html>