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
            case 1: // Sistema de Tickets
                if (tipoUsuario <= 2) {
                    window.location.href = '../tickets/mis_tickets_admin.php';
                } else {
                    window.location.href = '../tickets/mis_tickets.php';
                }
                break;

            case 2: // Agregar Usuarios
                window.location.href = '../registro/nuevo_usuario.php';
                break;

            case 3: // Editar Usuarios
                window.location.href = '../editar_usuarios/editar_usuario.php';
                break;

            case 4: // CAMBIAR Contraseña
                window.location.href = '../restablecer_password/restablecer_password.php';
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