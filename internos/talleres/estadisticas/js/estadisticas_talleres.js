// Variables para los gráficos
let inscritosTallerChart;
let sexoChart;
let horariosChart;
let periodoChart;
let tipoUsuarioChart;
let estadoInscripcionChart;

document.addEventListener('DOMContentLoaded', function () {
    // Inicializar flatpickr para las fechas
    flatpickr("#fechaInicio", {
        dateFormat: "Y-m-d",
        defaultDate: new Date(new Date().setMonth(new Date().getMonth() - 1))
    });
    
    flatpickr("#fechaFin", {
        dateFormat: "Y-m-d",
        defaultDate: new Date()
    });

    // Cargar datos iniciales
    cargarEstadisticas();

    // Event listeners
    document.getElementById('btnFiltrar').addEventListener('click', cargarEstadisticas);
    document.getElementById('btnExportar').addEventListener('click', exportarReporte);
});

function cargarEstadisticas() {
    const taller = document.getElementById('tallerFiltro').value;
    const estado = document.getElementById('estadoFiltro').value;
    const sexo = document.getElementById('sexoFiltro').value;
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    // Mostrar loading
    Swal.fire({
        title: 'Cargando datos',
        html: 'Por favor espere...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Realizar petición AJAX para estadísticas
    fetch(`php/obtener_estadisticas_talleres.php?taller=${taller}&estado=${estado}&sexo=${sexo}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire('Error', data.error, 'error');
                return;
            }

            // Actualizar estadísticas generales
            document.getElementById('totalInscritos').textContent = data.totalInscritos || 0;
            document.getElementById('totalHombres').textContent = data.totalHombres || 0;
            document.getElementById('totalMujeres').textContent = data.totalMujeres || 0;
            document.getElementById('totalBajas').textContent = data.totalBajas || 0;
            document.getElementById('totalActivos').textContent = data.totalActivos || 0;
            document.getElementById('totalInternos').textContent = data.totalInternos || 0;
            document.getElementById('totalExternos').textContent = data.totalExternos || 0;

            // Actualizar gráficos
            actualizarGraficos(data);

            Swal.close();
        })
        .catch(error => {
            Swal.close();
            Swal.fire('Error', 'Ocurrió un error al cargar los datos', 'error');
            console.error('Error:', error);
        });
}

function actualizarGraficos(data) {
    // Destruir gráficos existentes si existen
    if (inscritosTallerChart) inscritosTallerChart.destroy();
    if (sexoChart) sexoChart.destroy();
    if (horariosChart) horariosChart.destroy();
    if (periodoChart) periodoChart.destroy();
    if (tipoUsuarioChart) tipoUsuarioChart.destroy();
    if (estadoInscripcionChart) estadoInscripcionChart.destroy();

    // Gráfico de inscritos por taller (corregido)
    const ctxInscritosTaller = document.getElementById('inscritosTallerChart').getContext('2d');
    inscritosTallerChart = new Chart(ctxInscritosTaller, {
        type: 'bar',
        data: {
            labels: data.talleres || [],
            datasets: [{
                label: 'Inscritos',
                data: data.inscritosPorTaller || [],
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de distribución por sexo
    const ctxSexo = document.getElementById('sexoChart').getContext('2d');
    sexoChart = new Chart(ctxSexo, {
        type: 'pie',
        data: {
            labels: ['Hombres', 'Mujeres'],
            datasets: [{
                data: [data.totalHombres || 0, data.totalMujeres || 0],
                backgroundColor: [
                    '#007bff', // Azul para hombres
                    '#ff66b3'  // Rosa para mujeres
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de horarios más frecuentes
    const ctxHorarios = document.getElementById('horariosChart').getContext('2d');
    horariosChart = new Chart(ctxHorarios, {
        type: 'bar',
        data: {
            labels: data.horarios || [],
            datasets: [{
                label: 'Inscritos',
                data: data.inscritosPorHorario || [],
                backgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de inscritos por periodo
    const ctxPeriodo = document.getElementById('periodoChart').getContext('2d');
    periodoChart = new Chart(ctxPeriodo, {
        type: 'line',
        data: {
            labels: data.periodos || [],
            datasets: [{
                label: 'Inscritos',
                data: data.inscritosPorPeriodo || [],
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111, 66, 193, 0.1)',
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de tipo de usuario (nuevo)
    const ctxTipoUsuario = document.getElementById('tipoUsuarioChart').getContext('2d');
    tipoUsuarioChart = new Chart(ctxTipoUsuario, {
        type: 'doughnut',
        data: {
            labels: ['Internos', 'Externos'],
            datasets: [{
                data: [data.totalInternos || 0, data.totalExternos || 0],
                backgroundColor: [
                    '#17a2b8', // Turquesa para internos
                    '#6c757d'  // Gris para externos
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de estado de inscripción (nuevo)
    const ctxEstadoInscripcion = document.getElementById('estadoInscripcionChart').getContext('2d');
    estadoInscripcionChart = new Chart(ctxEstadoInscripcion, {
        type: 'doughnut',
        data: {
            labels: ['Activos', 'De baja'],
            datasets: [{
                data: [data.totalActivos || 0, data.totalBajas || 0],
                backgroundColor: [
                    '#28a745', // Verde para activos
                    '#dc3545'  // Rojo para bajas
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function actualizarTablaInscritos(inscritos) {
    const tabla = document.getElementById('tablaInscritos');
    tabla.innerHTML = '';

    // Calcular índices para la paginación
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, inscritos.length);

    for (let i = startIndex; i < endIndex; i++) {
        const inscrito = inscritos[i];
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>${inscrito.nombre_taller}</td>
            <td>${inscrito.nombre} ${inscrito.paterno} ${inscrito.materno}</td>
            <td>${inscrito.sexo}</td>
            <td>${inscrito.matricula || 'N/A'}</td>
            <td>${inscrito.carrera || 'N/A'}</td>
            <td>${inscrito.horario || 'N/A'}</td>
            <td><span class="badge ${inscrito.estado === 'Activo' ? 'bg-success' : 'bg-danger'}">${inscrito.estado}</span></td>
            <td>${inscrito.fecha_registro}</td>
        `;
        
        tabla.appendChild(row);
    }
}

function actualizarPaginacion() {
    const paginacion = document.getElementById('paginacion');
    paginacion.innerHTML = '';

    const totalPages = Math.ceil(totalItems / itemsPerPage);

    // Botón Anterior
    const liPrev = document.createElement('li');
    liPrev.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    liPrev.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
    liPrev.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            cargarEstadisticas();
        }
    });
    paginacion.appendChild(liPrev);

    // Números de página
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = i;
            cargarEstadisticas();
        });
        paginacion.appendChild(li);
    }

    // Botón Siguiente
    const liNext = document.createElement('li');
    liNext.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    liNext.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
    liNext.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            currentPage++;
            cargarEstadisticas();
        }
    });
    paginacion.appendChild(liNext);
}

function exportarReporte() {
    const formato = document.getElementById('formatoExportar').value;
    const taller = document.getElementById('tallerFiltro').value;
    const estado = document.getElementById('estadoFiltro').value;
    const sexo = document.getElementById('sexoFiltro').value;
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    // Mostrar loading
    Swal.fire({
        title: 'Generando reporte',
        html: 'Por favor espere...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Redirigir a la página de exportación
    window.location.href = `php/exportar_estadisticas.php?formato=${formato}&taller=${taller}&estado=${estado}&sexo=${sexo}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
    
    // Cerrar el modal y el loading (aunque la redirección lo hará)
    $('#exportarModal').modal('hide');
    Swal.close();
}