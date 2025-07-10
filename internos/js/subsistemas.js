$(document).ready(function () {
    cargarSubsistemas();
});

function cargarSubsistemas() {
    $.ajax({
        url: 'php/get_tipo_usuario.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.id_tipo_usuario) {
                const subsistemas = CATALOGO_SUBSISTEMAS[response.id_tipo_usuario] || [];
                mostrarSubsistemas(subsistemas, response.id_tipo_usuario);
            } else {
                manejarError('No se pudo obtener el tipo de usuario');
            }
        },
        error: function (xhr, status, error) {
            manejarError(error);
        }
    });
}

function mostrarSubsistemas(subsistemas, tipoUsuario) {
    const container = $('.container');
    container.empty();

    subsistemas.forEach(function (subsistema) {
        const card = crearCardSubsistema(subsistema, tipoUsuario);
        container.append(card);
    });

    // Add click event listeners
    agregarEventosClick(tipoUsuario);
}

function crearCardSubsistema(subsistema, tipoUsuario) {
    return `
        <div class="card ${subsistema.clase}" data-subsistema="${subsistema.id}">
            <img src="${subsistema.imagen}" alt="${subsistema.nombre}" class="icon" draggable="false">
            <div class="card-text">${subsistema.nombre}</div>
        </div>
    `;
}

function agregarEventosClick(tipoUsuario) {
    $('.container .card').css({
        'cursor': 'pointer',
        'user-select': 'none',
        '-moz-user-select': 'none',
        '-webkit-user-select': 'none'
    }).on('click', function () {
        const subsistemaId = $(this).data('subsistema');

        switch (subsistemaId) {
            case 1: // Agendar cita
                window.location.href = 'internos/agendar_cita/agendar_cita.php';
                break;

            case 2: // Reagendar Cita
                window.location.href = 'internos/reagendar/citas.php';
                break;

            case 3: // Consultar Citas
                window.location.href = 'internos/citas/citas.php';
                break;

            case 4: // NUEVO PACIENTE
                window.location.href = 'internos/registrar_paciente/registrar_paciente.php';
                break;

            case 5: // BUSCAR PACIENTE
                window.location.href = 'internos/buscar_pacientes/buscar_pacientes.php';
                break;

            case 6: // EDITAR PACIENTE
                window.location.href = 'internos/editar_pacientes/editar_pacientes.php';
                break;

            case 7: // CONSULTORIO
                window.location.href = 'internos/validar_cita/validar_cita.php';
                break;

            case 8: // CONSULTORIO
                window.location.href = 'internos/consultorios/consultorios.php';
                break;

            case 9: // INDICADORES
                window.location.href = 'internos/indicadores/indicadores.php';
                break;

            case 10: // DISPONIBILIDAD
                window.location.href = 'internos/disponibilidad/disponibilidad.php';
                break;

            case 11: // BLOQUEAR DIAS
                window.location.href = 'internos/bloquear_dias/disponibilidad.php';
                break;

            case 12: // EDITAR DIAGNOSTICO
                window.location.href = 'internos/citas_editables/citas_editables.php';
                break;

            case 13: // HABILITAR DIAGNOSTICO
                window.location.href = 'internos/editar_diagnostico/editar_diagnostico.php';
                break;

            case 14: // HORARIOS
                window.location.href = 'internos/horarios/horarios.php';
                break;

            case 15: // HOJA DE TRABAJO
                window.location.href = 'internos/hoja_trabajo/hoja_trabajo.php';
                break;

            case 16: // BLOQUEAR HORARIOS
                window.location.href = 'internos/bloqueo_horarios/bloqueo_horarios.php';
                break;

            case 17: // QR
                window.location.href = 'internos/codigo_barras/codigo_barras.php';
                break;

            case 18: // ASIGNAR EXPEDIENTE
                window.location.href = 'internos/asignar_expediente/asignar_expediente.php';
                break;
        }
    });

    // Prevent image dragging and context menu
    $('.container .card img').on('dragstart', function (e) {
        e.preventDefault();
    }).on('contextmenu', function (e) {
        e.preventDefault();
    });
}

function manejarError(error) {
    console.error('Error:', error);
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Hubo un error al cargar los subsistemas'
    });
}