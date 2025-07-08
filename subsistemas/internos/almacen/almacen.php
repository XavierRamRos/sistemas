<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('location: ../../../index.php');
    exit();
}

// Conexión a la base de datos (ajusta según tu configuración)
require_once '../../../php/conexion.php';

// Obtener almacenes del usuario actual
$almacenes_usuario = [];
if ($stmt = $conn->prepare("SELECT id_almacen FROM alm_usuarioalmacen WHERE id_usuario = ?")) {
    $stmt->bind_param("i", $_SESSION['id_usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $almacenes_usuario[] = $row['id_almacen'];
    }
    $stmt->close();
}

// Convertir array de almacenes a string para usar en consulta SQL
$almacenes_str = implode(",", $almacenes_usuario);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>INVENTARIO DE ALMACÉN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="shortcut icon" href="../../../img/UNEVE.png">
    <style>
        .search-container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .search-box {
            border-radius: 20px;
            padding: 15px;
            font-size: 16px;
            border: 2px solid #ddd;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .productos-container {
            margin-top: 20px;
        }
        .producto-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .producto-nombre {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        .producto-detalles {
            color: #666;
            margin-top: 5px;
        }
        .producto-cantidad {
            color: #2a6496;
            font-weight: bold;
        }
        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body data-role="<?php echo $_SESSION['id_tipo_usuario']; ?>" class="bg-light">

<!-- NAVBAR -->
<nav class="navbar">
    <a href="../../subsistemas.php">
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
                    <li><a class="dropdown-item" href="../php/logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </span>
    </div>
</nav>

<!-- Encabezado -->
<div class="container">
    <div class="ticket-header">
        <h2 class="text-center">BUSCAR PRODUCTOS EN ALMACÉN</h2>
    </div>
    
    <div class="search-container">
        <input type="text" id="productoSearch" class="form-control search-box" 
               placeholder="Buscar por nombre o descripción del producto...">
        <div id="productosContainer" class="productos-container"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Almacenes del usuario (convertidos a array JavaScript)
    const userAlmacenes = [<?php echo $almacenes_str; ?>];
    
    $('#productoSearch').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        if (searchTerm.length >= 2) { // Buscar solo si hay al menos 2 caracteres
            $.ajax({
                url: 'php/buscar_productos.php',
                method: 'POST',
                data: {
                    term: searchTerm,
                    almacenes: userAlmacenes
                },
                success: function(response) {
                    $('#productosContainer').html(response);
                },
                error: function() {
                    $('#productosContainer').html('<div class="no-results">Error al buscar productos</div>');
                }
            });
        } else if (searchTerm.length === 0) {
            $('#productosContainer').empty();
        }
    });
});
</script>

<footer>
    <div class="barra-bottom">
        <img src="../../../img/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>

</body>
</html>