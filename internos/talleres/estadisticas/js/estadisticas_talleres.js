// Variables para los gráficos
let inscritosTallerChart;
let sexoChart;
let horariosChart;
let periodoChart;
let tipoUsuarioChart;
let estadoInscripcionChart;

// Variables para paginación
let currentPage = 1;
const itemsPerPage = 10;
let totalItems = 0;

document.addEventListener('DOMContentLoaded', function () {
    // Inicializar flatpickr para las fechas sin valores por defecto
    flatpickr("#fechaInicio", {
        dateFormat: "Y-m-d"
    });
    
    flatpickr("#fechaFin", {
        dateFormat: "Y-m-d"
    });

    // Event listeners
    document.getElementById('btnFiltrar').addEventListener('click', cargarEstadisticas);
});

function cargarEstadisticas() {
    const taller = document.getElementById('tallerFiltro').value;
    const estado = document.getElementById('estadoFiltro').value;
    const sexo = document.getElementById('sexoFiltro').value;
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    // Validar que las fechas sean obligatorias
    // if (!fechaInicio || !fechaFin) {
    //     Swal.fire({
    //         icon: 'error',
    //         title: 'Fechas requeridas',
    //         text: 'Debes seleccionar una fecha de inicio y una fecha de fin para filtrar los datos',
    //         confirmButtonColor: '#3085d6',
    //     });
    //     return; // Detener la ejecución si no hay fechas
    // }

    // Validar que la fecha de inicio no sea mayor a la fecha fin
    if (new Date(fechaInicio) > new Date(fechaFin)) {
        Swal.fire({
            icon: 'error',
            title: 'Rango de fechas inválido',
            text: 'La fecha de inicio no puede ser mayor a la fecha de fin',
            confirmButtonColor: '#3085d6',
        });
        return;
    }

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
            document.getElementById('totalBajasEstado').textContent = data.totalBajas || 0;
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

// Event listener para el botón de reestablecer filtros
document.getElementById('btnResetFilters').addEventListener('click', resetearFiltros);

// Función para resetear todos los filtros
function resetearFiltros() {
    // Restablecer los selectores a sus valores por defecto
    document.getElementById('tallerFiltro').value = '0'; // Todos los talleres
    document.getElementById('estadoFiltro').value = '0'; // Todos
    document.getElementById('sexoFiltro').value = '0'; // Todos
    
    // Limpiar los campos de fecha
    document.getElementById('fechaInicio').value = '';
    document.getElementById('fechaFin').value = '';
   
}