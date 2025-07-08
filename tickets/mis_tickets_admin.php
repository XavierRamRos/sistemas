<?php
session_start();
if (isset($_SESSION['num_empleado'])) {
    if (isset($_SESSION['id_tipo_usuario']) && $_SESSION['id_tipo_usuario'] === '3') {
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
    <title>MIS TICKETS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="../img/UNEVE.png">
</head>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="../subsistemas/subsistemas.php">
        <img src="imgs/uneve-text.png" alt="Logo" class="logo">
    </a>
    <div class="user-info">
        <span class="user-name">
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

<body data-role="<?php echo $_SESSION['id_tipo_usuario']; ?>" class="bg-light">

<!-- Encabezado Tickets -->
<div class="container">
    <div class="ticket-header">
        <h2 class="text">MIS TICKETS</h2>
        <a href="fpdf/hoja_soporte_tecnico.php" target="_blank" class="descargar-tickets-btn"><i class="fas fa-file-pdf"></i>DESCARGAR TICKETS</a>
        <a href="nuevo_ticket_admin.php" class="mis-tickets-btn">NUEVO TICKET</a>
    </div>
    <div class="form-container">
        <div class="d-flex gap-2 align-items-center mb-5 justify-content-center">
            <input type="text" id="busquedaFiltro" class="form-control flex-grow-1" placeholder="Buscar por nombre o asunto">
            <select id="categoriaFiltro" class="form-select w-auto" onchange="actualizarEstadoSeleccionado()">
                <option value="" hidden>ESTADO</option>
                <option value="0">POR ATENDER</option>
                <option value="1">EN PROCESO</option>
                <option value="2">POR VALIDAR</option>
                <option value="3">FINALIZADO</option>
                <option value="4">ELIMINADOS</option>
            </select>
            <button id="btnFiltrar" class="btn btn-primary">Filtrar</button>
            <button id="btnRestablecer" class="btn btn-secondary text-nowrap">Restablecer Filtros</button>
        </div>

        <!-- Contenedor de la tabla para hacerla responsiva -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#TICKET</th>
                    <th>NOMBRE</th>
                    <th>ÁREA</th>
                    <th>ASUNTO</th>
                    <th>DETALLES</th>
                    <th>ESTADO</th>
                </tr>
                </thead>
                <tbody id="ticketsTableBody">
                <?php
                require('../php/conexion.php');

                if ($_SESSION['id_tipo_usuario'] === '1') {
                    $sql = "SELECT * FROM ticket WHERE estado_eliminado = '0' ORDER BY id DESC";
                } else {
                    $sql = "SELECT * FROM ticket WHERE estado_eliminado = '0' ORDER BY id DESC";
                }

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $estadoColor = '';
                        $estadoLabel = '';

                        switch ($row['status']) {
                            case 1:
                                $estadoColor = 'bg-proceso';
                                $estadoLabel = 'EN PROCESO';
                                break;
                            case 2:
                                $estadoColor = 'bg-validar';
                                $estadoLabel = 'POR VALIDAR';
                                break;
                            case 3:
                                $estadoColor = 'bg-finalizado';
                                $estadoLabel = 'FINALIZADO';
                                break;
                            default:
                                $estadoColor = 'bg-atender';
                                $estadoLabel = 'POR ATENDER';
                                break;
                        }

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nticket']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['area']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['asunto']) . "</td>";
                        echo "<td><button class='btn bg-detalles' onclick='mostrarDetalles(" . htmlspecialchars(json_encode($row)) . ")'>Detalles</button></td>";
echo "<td><button class='btn btn-tamanos $estadoColor' onclick='confirmarAtencion(" . $row['id'] . ", \"" . $estadoLabel . "\")'>" . $estadoLabel . "</button></td>";                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay tickets registrados</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ventana MODAL de los detalles -->
<div class="modal fade" id="detallesModal" tabindex="-1" aria-labelledby="detallesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 75%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitulo"></h5>
                <div class="ms-auto d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm" id="descargarBtn" onclick="descargarTicket()">DESCARGAR</button>
                    <button type="button" class="btn btn-light btn-sm" id="cambiarEstadoBtn" onclick="mostrarModalEstado(ticket)">CAMBIAR ESTADO</button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <br>
                        <center><h4><strong>DATOS DEL SOLICITANTE</strong></h4></center>
                        <br>
                        <p><strong>Nombre solicitante:</strong> <span id="detalleNombre"></span></p>
                        <p><strong>Área:</strong> <span id="detalleArea"></span></p>
                        <p><strong>Asunto:</strong> <span id="detalleAsunto"></span></p>
                        <p><strong>Marca y modelo:</strong> <span id="detalleMarca"></span> <span id="detalleModelo"></span></p>
                        <p><strong>N° inventario:</strong> <span id="detalleInventario"></span></p>
                        <p><strong>Categoría:</strong> <span id="detalleCategoria"></span></p>
                        <p><strong>Medio de solicitud:</strong> <span id="detalleMedioSoli"></span></p>
                        <p><strong>Fecha de solicitud:</strong> <span id="detalleFechaCreacion"></span></p>
                        <p><strong>Detalles específicos:</strong> <span id="detalleDescripcion"></span></p>
                        <p><strong>Detalles del problema no resuelto:</strong> <span id="detalleNoResuelto"></span></p>
                    </div>
                    <div class="col-md-6">
                        <br>
                        <center><h4><strong>DETALLES SOPORTE TÉCNICO</strong></h4></center>
                        <br>
                        <p><strong>Personal quien atiende:</strong> <span id="detallePersonalAtendio"></span></p>
                        <p><strong>Fecha de inicio:</strong> <span id="detalleFechaInicio"></span></p>
                        <p><strong>Fecha de término:</strong> <span id="detalleFechaAtencion"></span></p>
                        <p><strong>Tipo de falla:</strong> <span id="detalleTipoFalla"></span></p>
                        <p><strong>Descripción de la solución:</strong> <span id="detalleDescSolucion"></span></p>


                     <div id="eliminadoSection">
                        <p><strong>Eliminado por:</strong> <span id="detalleEliminado"></span></p>
                        <p><strong>Fecha eliminado:</strong> <span id="detalleFechaEliminado"></span></p>
                    </div>

                    
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="eliminarTemporalBtn" onclick="eliminarTemporal()">ELIMINAR</button>

                <button type="button" class="btn btn-secondary" id="restaurarBtn" onclick="restaurarTicket()" data-id="">RESTAURAR</button>

                <button type="button" class="btn btn-danger" id="eliminarBtn" onclick="eliminarTicketModal()">ELIMINAR DEFINITIVAMENTE</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar estado -->
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1" aria-labelledby="cambiarEstadoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cambiarEstadoModalLabel"><b>CAMBIAR ESTADO</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ticketId">
                <label for="estadoSelect">Selecciona el nuevo estado:</label>
                <select id="estadoSelect" class="form-select">
                    <option value="0">POR ATENDER</option>
                    <option value="1">EN PROCESO</option>
                    <option value="2">POR VALIDAR</option>
                    <option value="3">FINALIZADO</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="cambiarEstadoTicket()">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
const nombreAtendio = "<?php echo $_SESSION['nombre_completo']; ?>";
</script>

<script src="js/js.js"></script>
</body>
<footer>
    <div class="barra-bottom">
        <img src="imgs/logoedomex.png" alt="logoedomex" class="logoedomex">
        <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
    </div>
</footer>
</html>