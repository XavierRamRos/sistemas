$(document).ready(function () {
    // Variables para paginación
    let currentPage = 1;
    const itemsPerPage = 25; // Cambiado a 25 resultados por página
    let totalItems = 0;
    let hasSearched = false; // Bandera para controlar si se ha realizado una búsqueda

    // Mensaje inicial
    $('#tablaInscripciones').html(`
        <tr>
            <td colspan="7" class="text-center text-muted">Ingrese criterios de búsqueda y presione "Buscar"</td>
        </tr>
    `);
    $('#pagination').empty();

    // Buscar al enviar el formulario
   $('#formFiltros').submit(function (e) {
    e.preventDefault();
    
    // Mostrar loading y bloquear botón
    $('#searchText').addClass('d-none');
    $('#searchLoading').removeClass('d-none');
    $('#btnBuscar').prop('disabled', true);

    currentPage = 1;
    hasSearched = true;
    cargarInscripciones();

        // // Validar que haya al menos un criterio de búsqueda
        // if ($('#filtroBusqueda').val().trim() === '' && $('#filtroTaller').val() === '') {
        //     Swal.fire({
        //         icon: 'warning',
        //         title: 'Advertencia',
        //         text: 'Debe ingresar al menos un criterio de búsqueda',
        //         confirmButtonColor: '#0d6efd'
        //     });
        //     return;
        // }

        // // Mostrar loading y bloquear botón
        // $('#searchText').addClass('d-none');
        // $('#searchLoading').removeClass('d-none');
        // $('#btnBuscar').prop('disabled', true);

        // currentPage = 1;
        // hasSearched = true;
        // cargarInscripciones();
    });

    // Limpiar filtros
    $('#btnLimpiar').click(function () {
    $('#formFiltros')[0].reset();
    currentPage = 1;
    hasSearched = false;

    $('#tablaInscripciones').html(`
        <tr>
            <td colspan="7" class="text-center text-muted">Ingrese criterios de búsqueda y presione "Buscar"</td>
        </tr>
    `);
    $('#pagination').empty();
});

    // Función para cargar inscripciones
function cargarInscripciones(page = 1) {
    currentPage = page;

    const filtros = {
        busqueda: $('#filtroBusqueda').val(),
        taller: $('#filtroTaller').val(),
        estado: $('#filtroEstado').val(), // Nuevo filtro
        page: page,
        per_page: itemsPerPage
    };

    $.ajax({
        url: 'php/obtener_inscripciones.php',
        type: 'GET',
        dataType: 'json',
        data: filtros,
        success: function (response) {
            $('#tablaInscripciones').empty();

            if (response.success && response.data && response.data.length > 0) {
                totalItems = response.total || response.data.length;

                response.data.forEach(function (inscripcion) {
                    const esExterno = inscripcion.id_tipo == 2;
                    
                    // Determinar clase CSS según el estado
                    let estadoClass = '';
                    if (inscripcion.id_estado == 2) { // Por renovar
                        estadoClass = 'table-warning';
                    } else if (inscripcion.id_estado == 3) { // Baja
                        estadoClass = 'table-danger';
                    }
                    
                    const row = `
                        <tr class="${estadoClass}">
                            <td>${esExterno ? 'EXTERNO' : (inscripcion.matricula || 'N/A')}</td>
                            <td>${inscripcion.nombre_completo}</td>
                            <td>${esExterno ? 'N/A' : (inscripcion.carrera || 'N/A')}</td>
                            <td>${inscripcion.taller}</td>
                            <td>${inscripcion.num_movil || 'N/A'}</td>
                            <td>${inscripcion.fecha_registro}</td>
                            <td>
                                <span class="badge bg-${getEstadoBadgeColor(inscripcion.id_estado)}">
                                    ${inscripcion.estado_taller || 'N/A'}
                                </span>
                                <button class="btn btn-sm btn-secondary btn-detalle" data-id="${inscripcion.id_inscrito}">
                                    <i class="bi bi-eye"></i> Detalles
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#tablaInscripciones').append(row);
                });

                actualizarPaginacion(response.total);
            } else {
                const mensaje = hasSearched
                    ? 'No se encontraron inscripciones con los filtros seleccionados'
                    : 'Ingrese criterios de búsqueda y presione "Buscar"';

                $('#tablaInscripciones').append(`
                    <tr>
                        <td colspan="7" class="text-center ${hasSearched ? '' : 'text-muted'}">${mensaje}</td>
                    </tr>
                `);
                $('#pagination').empty();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error al cargar inscripciones:', textStatus, errorThrown);
            $('#tablaInscripciones').append(`
                <tr>
                    <td colspan="7" class="text-center text-danger">Error al cargar los datos. Por favor intente nuevamente.</td>
                </tr>
            `);
            $('#pagination').empty();
        },
        complete: function () {
            // Ocultar loading y habilitar botón
            $('#searchText').removeClass('d-none');
            $('#searchLoading').addClass('d-none');
            $('#btnBuscar').prop('disabled', false);
        }
    });
}

// Función auxiliar para determinar el color del badge según el estado
function getEstadoBadgeColor(idEstado) {
    switch(idEstado) {
        case 1: return 'success'; // Activo
        case 2: return 'warning'; // Por renovar
        case 3: return 'danger';  // Baja
        default: return 'secondary';
    }
}

    // Función para actualizar paginación
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

        // Rango de páginas
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

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
            cargarInscripciones(page);
        }
    });

    // Mostrar detalles de inscripción
$(document).on('click', '.btn-detalle', function () {
    const id = $(this).data('id');
    const modal = new bootstrap.Modal(document.getElementById('detalleModal'));

    // Establecer el ID en el botón de cancelar antes de mostrar el modal
    $('#btnCancelarInscripcion').data('id', id);

    $.ajax({
        url: 'php/obtener_detalle_inscripcion.php',
        type: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function (response) {
            if (response.success) {
                const inscripcion = response.data;
                
                // Construir información del horario si existe
                let horarioInfo = 'No asignado';
                if (inscripcion.hora_inicio && inscripcion.hora_fin && inscripcion.dia) {
                    horarioInfo = `${inscripcion.dia} de ${inscripcion.hora_inicio} a ${inscripcion.hora_fin}`;
                }
                
                // Construir información de validación si existe
                let validacionInfo = 'No validado';
                if (inscripcion.linea_captura) {
                    validacionInfo = `Línea de captura: ${inscripcion.linea_captura}<br>Estado: ${inscripcion.estado_validacion || 'No especificado'}`;
                }
                
                let detalles = `
    <div class="row">
        <!-- Columna izquierda: DATOS PERSONALES + DOMICILIO + CONTACTO ALTERNO -->
        <div class="col-md-6">
            <h4><b>Datos Personales</b></h4>
            <p><strong>Nombre:</strong> ${inscripcion.nombre} ${inscripcion.paterno} ${inscripcion.materno}</p>
            <p><strong>Matrícula:</strong> ${inscripcion.matricula || 'N/A'}</p>
            <p><strong>Carrera:</strong> ${inscripcion.carrera}</p>
            <p><strong>Fecha Nacimiento:</strong> ${inscripcion.fecha_nacimiento_formatted}</p>
            <p><strong>Edad:</strong> ${inscripcion.edad}</p>
            <p><strong>Teléfono:</strong> ${inscripcion.num_movil || 'N/A'}</p>
            <p><strong>Correo:</strong> ${inscripcion.correo || 'N/A'}</p>

            <hr>

            <h4><b>Domicilio</b></h4>
            <p><strong>Calle:</strong> ${inscripcion.calle}</p>
            <p><strong>Colonia:</strong> ${inscripcion.colonia}</p>
            <p><strong>Número Exterior:</strong> ${inscripcion.num_exterior || 'N/A'}</p>
            <p><strong>Número Interior:</strong> ${inscripcion.num_interior || 'N/A'}</p>

            <hr>

            <h4><b>Contacto Alterno</b></h4>
            <p><strong>Nombre:</strong> ${inscripcion.contacto_alterno || 'N/A'}</p>
            <p><strong>Teléfono:</strong> ${inscripcion.movil_alt || 'N/A'}</p>
            <p><strong>Domicilio:</strong> ${inscripcion.domicilio_alterno || 'N/A'}</p>
        </div>

        <!-- Columna derecha: TALLER + VALIDACION + INFORMACIÓN DE SALUD -->
        <div class="col-md-6">
            <h4><b>Taller</b></h4>
            <p><strong>Nombre:</strong> ${inscripcion.taller}</p>
            <p><strong>Horario:</strong> ${horarioInfo}</p>
            <p><strong>Estado en Taller:</strong> ${inscripcion.estado_taller || 'N/A'}</p>
            <p><strong>Fecha Inscripción:</strong> ${inscripcion.fecha_registro}</p>
            <p><strong>Registrado por:</strong> ${inscripcion.usuario_registro}</p>
            <p><strong>Tipo de Usuario:</strong> ${inscripcion.tipo_usuario || 'N/A'}</p>
            <p><strong>Medio de Registro:</strong> ${inscripcion.medio_registro || 'N/A'}</p>

            <hr>

            <h4><b>Validación</b></h4>
            <p>${validacionInfo}</p>

            <hr>

            <h4><b>Información de Salud</b></h4>
            <p><strong>Sistema de Salud:</strong> ${inscripcion.seguro_social || 'N/A'}</p>
            <p><strong>Número Médico:</strong> ${inscripcion.num_medico || 'N/A'}</p>
            <p><strong>Padecimientos:</strong> ${inscripcion.padecimiento || 'Ninguno'}</p>
            <p><strong>Alergias:</strong> ${inscripcion.alergia || 'Ninguna'}</p>
        </div>
    </div>
`;

                $('#detalleInscripcion').html(detalles);
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudieron cargar los detalles',
                    confirmButtonColor: '#0d6efd'
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error al cargar detalles:', textStatus, errorThrown);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los detalles',
                confirmButtonColor: '#0d6efd'
            });
        }
    });
});

// Manejar clic en el botón de cancelar inscripción
$(document).on('click', '#btnCancelarInscripcion', function() {
    const id = $(this).data('id');
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción cancelará la inscripción del usuario en el taller",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, volver'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'php/cancelar_inscripcion.php',
                type: 'POST',
                dataType: 'json',
                data: { id_inscrito: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message,
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            // Cerrar el modal y recargar los datos
                            $('#detalleModal').modal('hide');
                            cargarInscripciones(currentPage);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al comunicarse con el servidor',
                        confirmButtonColor: '#0d6efd'
                    });
                }
            });
        }
    });
});

    // Permitir búsqueda con Enter
    $('#filtroBusqueda').keypress(function (e) {
        if (e.which === 13) {
            $('#formFiltros').submit();
        }
    });
});

// Agregar al final del documento ready, después de todo el código existente
$(document).on('click', '#btnGenerarPDF', function() {
    // Obtener los filtros actuales
    const filtros = {
        taller: $('#filtroTaller').val(),
        busqueda: $('#filtroBusqueda').val(),
        estado: $('#filtroEstado').val()
    };

    // Mostrar loading
    const btnPDF = $('#btnGenerarPDF');
    btnPDF.prop('disabled', true);
    btnPDF.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...');

    // Construir URL para el PDF (corregida la ruta)
    let url = 'fpdf/formato_inscritos.php?';
    
    // Solo agregar parámetros si tienen valor
    if (filtros.taller) url += `taller=${filtros.taller}&`;
    if (filtros.busqueda) url += `busqueda=${encodeURIComponent(filtros.busqueda)}&`;
    if (filtros.estado) url += `estado=${filtros.estado}`;
    
    // Eliminar el último & si existe
    if (url.endsWith('&')) {
        url = url.slice(0, -1);
    }

    // Solución alternativa usando window.open
    const pdfWindow = window.open(url, '_blank');
    
    // Verificar si la ventana se bloqueó
    if (!pdfWindow || pdfWindow.closed || typeof pdfWindow.closed == 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El navegador bloqueó la ventana emergente. Por favor, permite ventanas emergentes para este sitio.',
            confirmButtonColor: '#0d6efd'
        });
    }
    
    // Restaurar botón
    btnPDF.prop('disabled', false);
    btnPDF.html('<i class="bi bi-file-earmark-pdf"></i> Generar PDF');
});