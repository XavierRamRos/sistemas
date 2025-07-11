$(document).ready(function () {
    // Variables para paginación
    let currentPage = 1;
    const itemsPerPage = 25;
    let totalItems = 0;
    let hasSearched = false;

    // Mensaje inicial
    $('#tablaMantenimientos').html(`
        <tr>
            <td colspan="10" class="text-center text-muted">Ingrese criterios de búsqueda y presione "Buscar"</td>
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
        cargarMantenimientos();
    });

    // Limpiar filtros
    $('#btnLimpiar').click(function () {
        $('#formFiltros')[0].reset();
        currentPage = 1;
        hasSearched = false;
    
        $('#tablaMantenimientos').html(`
            <tr>
                <td colspan="10" class="text-center text-muted">Ingrese criterios de búsqueda y presione "Buscar"</td>
            </tr>
        `);
        $('#pagination').empty();
    });

    // Función para cargar mantenimientos
    function cargarMantenimientos(page = 1) {
        currentPage = page;
    
        const filtros = {
            area: $('#filtroArea').val(),
            fecha_inicio: $('#filtroFechaInicio').val(),
            fecha_fin: $('#filtroFechaFin').val(),
            estado: $('#filtroEstado').val(),
            usuario_mtto: $('#filtroUsuarioMtto').val(),
            page: page,
            per_page: itemsPerPage
        };
    
        $.ajax({
            url: 'php/obtener_mantenimientos.php',
            type: 'GET',
            dataType: 'json',
            data: filtros,
            success: function (response) {
                $('#tablaMantenimientos').empty();
    
                if (response.success && response.data && response.data.length > 0) {
                    totalItems = response.total || response.data.length;
    
                    response.data.forEach(function (mantenimiento) {
                        // Determinar clase CSS según el estado
                        let estadoClass = mantenimiento.estado === 'VALIDADO' ? 'table-success' : '';
                        
                        const row = `
                            <tr class="${estadoClass}">
                                <td>${mantenimiento.nombre}</td>
                                <td>${mantenimiento.solicitante}</td>
                                <td>${mantenimiento.tecnico}</td>
                                <td>${mantenimiento.fecha_inicio}</td>
                                <td>${mantenimiento.fecha_termino || 'N/A'}</td>
                                <td>${mantenimiento.fecha_validacion}</td>
                                <td>
                                    <span class="badge bg-${mantenimiento.estado === 'VALIDADO' ? 'success' : 'warning'}">
                                        ${mantenimiento.estado}
                                    </span>
                                </td>
                                <td>${mantenimiento.num_equipos}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger btn-generar-pdf me-1" 
                                            data-id="${mantenimiento.id_mantenimientos}"
                                            title="Generar PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary btn-detalle-equipos" 
                                            data-id="${mantenimiento.id_mantenimientos}"
                                            title="Ver detalles">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#tablaMantenimientos').append(row);
                    });
    
                    actualizarPaginacion(response.total);
                } else {
                    const mensaje = hasSearched
                        ? 'No se encontraron mantenimientos con los filtros seleccionados'
                        : 'Ingrese criterios de búsqueda y presione "Buscar"';
    
                    $('#tablaMantenimientos').append(`
                        <tr>
                            <td colspan="9" class="text-center ${hasSearched ? '' : 'text-muted'}">${mensaje}</td>
                        </tr>
                    `);
                    $('#pagination').empty();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error al cargar mantenimientos:', textStatus, errorThrown);
                $('#tablaMantenimientos').append(`
                    <tr>
                        <td colspan="10" class="text-center text-danger">Error al cargar los datos. Por favor intente nuevamente.</td>
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
            cargarMantenimientos(page);
        }
    });

    // Mostrar detalles de equipos
    $(document).on('click', '.btn-detalle-equipos', function () {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('detalleEquiposModal'));

        $.ajax({
            url: 'php/obtener_detalle_equipos.php',
            type: 'GET',
            dataType: 'json',
            data: { id: id },
            success: function (response) {
                if (response.success) {
                    $('#detalleEquiposBody').empty();
                    
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function (equipo) {
                            const row = `
                                <tr>
                                    <td>${equipo.marca || 'N/A'}</td>
                                    <td>${equipo.modelo || 'N/A'}</td>
                                    <td>${equipo.inventario || 'N/A'}</td>
                                    <td>${equipo.descripcion || 'N/A'}</td>
                                </tr>
                            `;
                            $('#detalleEquiposBody').append(row);
                        });
                    } else {
                        $('#detalleEquiposBody').append(`
                            <tr>
                                <td colspan="4" class="text-center text-muted">No se encontraron equipos para este mantenimiento</td>
                            </tr>
                        `);
                    }
                    
                    modal.show();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudieron cargar los detalles de los equipos',
                        confirmButtonColor: '#0d6efd'
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error al cargar detalles:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los detalles de los equipos',
                    confirmButtonColor: '#0d6efd'
                });
            }
        });
    });

    // Botón Generar PDF (por ahora no hace nada)
    $('#btnGenerarPDF').click(function() {
        Swal.fire({
            icon: 'info',
            title: 'Generar PDF',
            text: 'Esta funcionalidad estará disponible próximamente',
            confirmButtonColor: '#0d6efd'
        });
    });

    // Permitir búsqueda con Enter en cualquier campo
    $('#formFiltros input, #formFiltros select').keypress(function (e) {
        if (e.which === 13) {
            $('#formFiltros').submit();
        }
    });
});


// Función para generar PDF individual
$(document).on('click', '.btn-generar-pdf', function() {
    const id = $(this).data('id');
    const btn = $(this);
    
    // Mostrar loading en el botón
    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    btn.prop('disabled', true);
    
    // Abrir ventana para el PDF
    const pdfWindow = window.open(`fpdf/formato_mantimientos.php?id=${id}`, '_blank');
    
    // Verificar si la ventana se bloqueó
    if (!pdfWindow || pdfWindow.closed || typeof pdfWindow.closed == 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El navegador bloqueó la ventana emergente. Por favor, permite ventanas emergentes para este sitio.',
            confirmButtonColor: '#0d6efd'
        });
    }
    
    // Restaurar botón después de 2 segundos (tiempo estimado para generar PDF)
    setTimeout(() => {
        btn.html('<i class="bi bi-file-earmark-pdf"></i>');
        btn.prop('disabled', false);
    }, 2000);
});

// Botón Generar PDF general (sin funcionalidad por ahora)
$('#btnGenerarPDF').click(function() {
    Swal.fire({
        icon: 'info',
        title: 'Generar PDF',
        text: 'Esta funcionalidad estará disponible próximamente',
        confirmButtonColor: '#0d6efd'
    });
});