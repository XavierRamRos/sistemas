$(document).ready(function () {
    // Variables para paginación
    let currentPage = 1;
    const itemsPerPage = 25;
    let totalItems = 0;
    let hasSearched = false;

    // Mensaje inicial
    $('#tablaInscripciones').html(`
        <tr>
            <td colspan="8" class="text-center text-muted">Ingrese criterios de búsqueda y presione "Buscar"</td>
        </tr>
    `);
    $('#pagination').empty();

    // Buscar al enviar el formulario
    $('#formFiltros').submit(function (e) {
        e.preventDefault();
        
        $('#searchText').addClass('d-none');
        $('#searchLoading').removeClass('d-none');
        $('#btnBuscar').prop('disabled', true);

        currentPage = 1;
        hasSearched = true;
        cargarInscripciones();
    });

    // Limpiar filtros
    $('#btnLimpiar').click(function () {
        $('#formFiltros')[0].reset();
        currentPage = 1;
        hasSearched = false;

        $('#tablaInscripciones').html(`
            <tr>
                <td colspan="8" class="text-center text-muted">Ingrese criterios de búsqueda y presione "Buscar"</td>
            </tr>
        `);
        $('#pagination').empty();
    });

    // Función para determinar el color según los días
    function getDiasColor(dias) {
        if (dias <= 75) return 'success';
        if (dias <= 90) return 'warning';
        if (dias <= 105) return 'orange';
        return 'danger';
    }

    // Función para cargar inscripciones
    function cargarInscripciones(page = 1) {
        currentPage = page;
    
        const filtros = {
            busqueda: $('#filtroBusqueda').val(),
            taller: $('#filtroTaller').val(),
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
                        // Calcular días desde última modificación o registro
                        let fechaRef;
                        let diffDays = 0;
                        
                        if (inscripcion.ultima_modificacion) {
                            const parts = inscripcion.ultima_modificacion.split(' ');
                            const dateParts = parts[0].split('/');
                            fechaRef = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                        } else if (inscripcion.fecha_registro) {
                            const parts = inscripcion.fecha_registro.split(' ');
                            const dateParts = parts[0].split('/');
                            fechaRef = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                        }
                        
                        if (fechaRef) {
                            const hoy = new Date();
                            hoy.setHours(0, 0, 0, 0);
                            fechaRef.setHours(0, 0, 0, 0);
                            
                            const diffTime = hoy - fechaRef;
                            diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                        }
                        
                        const colorClass = getDiasColor(diffDays);
                        
                        const row = `
                            <tr class="table-${colorClass}">
                                <td>${inscripcion.nombre_completo}</td>
                                <td>${inscripcion.taller}</td>
                                <td>${inscripcion.estado_validacion || 'No validado'}</td>
                                <td>${fechaRef ? diffDays + ' días' : 'Fecha no válida'}</td>
                                <td>${inscripcion.fecha_registro}</td>
                                <td>${inscripcion.ultima_modificacion || 'N/A'}</td>
                                <td>
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
                            <td colspan="8" class="text-center ${hasSearched ? '' : 'text-muted'}">${mensaje}</td>
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
                $('#searchText').removeClass('d-none');
                $('#searchLoading').addClass('d-none');
                $('#btnBuscar').prop('disabled', false);
            }
        });
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

        // Establecer el ID en los botones
        $('#btnCancelarInscripcion').data('id', id);
        $('#btnRenovarInscripcion').data('id', id);

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
                    
                    // Construir información de validación
                    let validacionInfo = 'No validado';
                    if (inscripcion.linea_captura) {
                        validacionInfo = `
                            <p><strong>Línea de captura:</strong> ${inscripcion.linea_captura}</p>
                            <p><strong>Estado:</strong> ${inscripcion.estado_validacion || 'No especificado'}</p>
                            <p><strong>Fecha validación:</strong> ${inscripcion.fecha_validacion_formatted || 'N/A'}</p>
                        `;
                    }
                    
                    let detalles = `
                        <div class="row">
                            <!-- Columna izquierda: DATOS PERSONALES + DOMICILIO + CONTACTO ALTERNO -->
                            <div class="col-md-6">
                                <h4><b>Datos Personales</b></h4>
                                <p><strong>Nombre:</strong> ${inscripcion.nombre} ${inscripcion.paterno} ${inscripcion.materno}</p>
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
                                ${validacionInfo}

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

// Manejar clic en el botón de renovar inscripción
$(document).on('click', '#btnRenovarInscripcion', function() {
    const id = $(this).data('id');
    const detalleModal = bootstrap.Modal.getInstance(document.getElementById('detalleModal'));
    
    // Limpiar el formulario
    $('#formLineaCaptura')[0].reset();
    $('#idInscritoRenovar').val(id);
    
    // Cerrar el modal de detalles
    detalleModal.hide();
    
    // Mostrar el modal de renovación después de un pequeño retraso
    setTimeout(() => {
        const lineaCapturaModal = new bootstrap.Modal(document.getElementById('lineaCapturaModal'));
        lineaCapturaModal.show();
        $('#lineaCaptura').focus();
    }, 300); // 300ms es suficiente para la transición
});


// Manejar confirmación de renovación
$(document).on('click', '#btnConfirmarRenovacion', function() {
    const lineaCaptura = $('#lineaCaptura').val();
    const idInscrito = $('#idInscritoRenovar').val();
    
    if (!lineaCaptura || lineaCaptura.length !== 27) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'La línea de captura debe tener exactamente 27 caracteres',
            confirmButtonColor: '#0d6efd'
        });
        return;
    }
    
    // Mostrar carga
    const btn = $(this);
    btn.prop('disabled', true).html(`
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Procesando...
    `);
    
    $.ajax({
        url: 'php/renovar_inscripcion.php',
        type: 'POST',
        dataType: 'json',
        data: {
            id_inscrito: idInscrito,
            linea_captura: lineaCaptura
        },
        success: function(response) {
            if (response.success) {
                // Limpiar el formulario después de éxito
                $('#formLineaCaptura')[0].reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    confirmButtonColor: '#0d6efd'
                }).then(() => {
                    // Cerrar el modal de línea de captura
                    const lineaCapturaModal = bootstrap.Modal.getInstance(document.getElementById('lineaCapturaModal'));
                    lineaCapturaModal.hide();
                    
                    // Recargar los datos
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
        },
        complete: function() {
            btn.prop('disabled', false).text('Confirmar Renovación');
        }
    });
});

// Limpiar el formulario cuando se cierra el modal
$('#lineaCapturaModal').on('hidden.bs.modal', function() {
    $('#formLineaCaptura')[0].reset();
});

    // Permitir búsqueda con Enter
    $('#filtroBusqueda').keypress(function (e) {
        if (e.which === 13) {
            $('#formFiltros').submit();
        }
    });
});