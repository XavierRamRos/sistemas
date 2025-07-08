// Verificación de que jQuery está cargado
if (typeof jQuery == 'undefined') {
    console.error('jQuery no está cargado correctamente');
} else {
    console.log('jQuery versión ' + jQuery.fn.jquery + ' cargado correctamente');
}

$(document).ready(function () {
    console.log('Documento listo, jQuery funcionando');

    // Variables globales
    let horarioActual = null;
    let currentPage = 1;
    const itemsPerPage = 15;
    let totalItems = 0;

    // Mostrar/ocultar formulario
    $('#btnNuevoHorario').click(function () {
        console.log('Botón Nuevo Horario clickeado');
        const form = $('#formHorario');
        const isVisible = form.is(':visible');

        if (isVisible) {
            form.hide();
            $(this).html('<i class="bi bi-plus-circle"></i> Nuevo Horario');
        } else {
            form.show();
            $(this).html('<i class="bi bi-x-circle"></i> Cancelar');
            // Desplazarse suavemente al formulario
            $('html, body').animate({
                scrollTop: form.offset().top - 20
            }, 500);
        }

        horarioActual = null;
        $('#horarioForm')[0].reset();
    });

    // Cargar horarios al iniciar
    cargarHorarios();

    // Guardar horario
    $('#horarioForm').submit(function (e) {
        e.preventDefault();

        const idTaller = $('#selectTaller').val();
        const idDia = $('#selectDia').val();
        const idHoraInicio = $('#selectHoraInicio').val();
        const idHoraFin = $('#selectHoraFin').val();

        if (!idTaller || !idDia || !idHoraInicio || !idHoraFin) {
            Swal.fire('Error', 'Todos los campos son requeridos', 'error');
            return;
        }

        const url = horarioActual ?
            'php/actualizar_horario.php' :
            'php/guardar_horario.php';

        const data = horarioActual ?
            { id: horarioActual, id_taller: idTaller, id_dia: idDia, id_hora_inicio: idHoraInicio, id_hora_fin: idHoraFin } :
            { id_taller: idTaller, id_dia: idDia, id_hora_inicio: idHoraInicio, id_hora_fin: idHoraFin };

        console.log('Enviando datos:', data);

        $.post(url, data, function (response) {
            console.log('Respuesta recibida:', response);
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success');
                $('#formHorario').hide();
                $('#btnNuevoHorario').html('<i class="bi bi-plus-circle"></i> Nuevo Horario');
                $('#horarioForm')[0].reset();
                cargarHorarios(currentPage); // Recargar manteniendo la página actual
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
        });
    });

    // Función para cargar horarios con paginación
    function cargarHorarios(page = 1) {
        console.log(`Cargando horarios, página ${page}...`);
        currentPage = page;

        $.ajax({
            url: 'php/obtener_horarios.php',
            dataType: 'json',
            data: {
                page: page,
                per_page: itemsPerPage
            },
            success: function (response) {
                console.log('Horarios recibidos:', response);
                $('#tablaHorarios').empty();

                if (response.success && response.data && response.data.length > 0) {
                    totalItems = response.total || response.data.length;

                    response.data.forEach(function (horario) {
                        const row = `
                        <tr>
                            <td>${horario.taller}</td>
                            <td>${horario.dia}</td>
                            <td>${horario.hora_inicio}</td>
                            <td>${horario.hora_fin}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-editar" data-id="${horario.id_horario_taller}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-eliminar" data-id="${horario.id_horario_taller}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        `;
                        $('#tablaHorarios').append(row);
                    });

                    // Actualizar paginación
                    actualizarPaginacion(response.total);
                } else {
                    const mensaje = response.message || 'No hay horarios asignados';
                    $('#tablaHorarios').append(`<tr><td colspan="5" class="text-center">${mensaje}</td></tr>`);
                    $('#pagination').empty();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error al cargar horarios:', textStatus, errorThrown);
                try {
                    const response = JSON.parse(jqXHR.responseText);
                    $('#tablaHorarios').append(`<tr><td colspan="5" class="text-center text-danger">${response.message || 'Error al cargar los horarios'}</td></tr>`);
                } catch (e) {
                    console.error('Respuesta del servidor:', jqXHR.responseText);
                    $('#tablaHorarios').append('<tr><td colspan="5" class="text-center text-danger">Error al cargar los horarios. Ver consola para detalles.</td></tr>');
                }
                $('#pagination').empty();
            }
        });
    }

    // Función para actualizar los controles de paginación
    function actualizarPaginacion(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const pagination = $('#pagination');
        pagination.empty();

        if (totalPages <= 1) return;

        // Botón Anterior
        const prevDisabled = currentPage === 1 ? 'disabled' : '';
        pagination.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" aria-label="Previous" data-page="${currentPage - 1}">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        // Rango de páginas (mostramos 5 páginas alrededor de la actual)
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        // Ajustar si estamos cerca del inicio o final
        if (currentPage <= 3) {
            endPage = Math.min(5, totalPages);
        }
        if (currentPage >= totalPages - 2) {
            startPage = Math.max(1, totalPages - 4);
        }

        // Página inicial
        if (startPage > 1) {
            pagination.append(`
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
            `);
            if (startPage > 2) {
                pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
            }
        }

        // Páginas intermedias
        for (let i = startPage; i <= endPage; i++) {
            const active = i === currentPage ? 'active' : '';
            pagination.append(`
                <li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        // Página final
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
            }
            pagination.append(`
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>
            `);
        }

        // Botón Siguiente
        const nextDisabled = currentPage === totalPages ? 'disabled' : '';
        pagination.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" aria-label="Next" data-page="${currentPage + 1}">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }

    // Manejar clic en paginación
    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && page !== currentPage) {
            cargarHorarios(page);
            // Desplazarse a la parte superior de la tabla
            $('html, body').animate({
                scrollTop: $('#tablaHorarios').offset().top - 20
            }, 500);
        }
    });

    // Editar horario
    $(document).on('click', '.btn-editar', function () {
        const id = $(this).data('id');
        console.log('Editando horario ID:', id);

        $.ajax({
            url: 'php/obtener_horario.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                console.log('Respuesta edición:', response);
                if (response.success) {
                    horarioActual = id;
                    $('#selectTaller').val(response.data.id_taller);
                    $('#selectDia').val(response.data.id_dia);
                    $('#selectHoraInicio').val(response.data.id_hora_inicio);
                    $('#selectHoraFin').val(response.data.id_hora_fin);
                    $('#formHorario').show();
                    $('#btnNuevoHorario').html('<i class="bi bi-x-circle"></i> Cancelar');
                    // Desplazarse al formulario
                    $('html, body').animate({
                        scrollTop: $('#formHorario').offset().top - 20
                    }, 500);
                } else {
                    Swal.fire('Error', response.message || 'Error desconocido al cargar el horario', 'error');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error al editar:', textStatus, errorThrown);
                console.error('Respuesta del servidor:', jqXHR.responseText);

                let errorMsg = 'No se pudo cargar el horario para edición';

                try {
                    const response = JSON.parse(jqXHR.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    errorMsg += '<br><br>' + jqXHR.responseText.substring(0, 200).replace(/<[^>]*>?/gm, '');
                }

                Swal.fire({
                    title: 'Error',
                    html: errorMsg,
                    icon: 'error'
                });
            }
        });
    });

    // Eliminar horario
    $(document).on('click', '.btn-eliminar', function () {
        const id = $(this).data('id');
        console.log('Solicitando eliminar horario ID:', id);
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));

        modal.show();

        $('#confirmDelete').off('click').on('click', function () {
            console.log('Confirmando eliminación de horario ID:', id);
            $.post('php/eliminar_horario.php', { id: id }, function (response) {
                console.log('Respuesta eliminación:', response);
                if (response.success) {
                    Swal.fire('Éxito', response.message, 'success');
                    modal.hide();
                    cargarHorarios(currentPage); // Recargar manteniendo la página actual
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error al eliminar:', textStatus, errorThrown);
                Swal.fire('Error', 'Error al eliminar el horario', 'error');
            });
        });
    });
});