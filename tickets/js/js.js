document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ticketForm');

    document.addEventListener('DOMContentLoaded', () => {
        cargarTabla();
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const nombre = sanitizeInput(form.nombre.value);
        const area = sanitizeInput(form.area.value);
        const asunto = sanitizeInput(form.asunto.value);
        const categoria = form.categoria.value;
        const medio_soli = form.medio_soli.value;
        const marca = sanitizeInput(form.marca.value);
        const descripcion = sanitizeInput(form.descripcion.value);
        const numero_inventario = sanitizeInput(form.numero_inventario.value);
        const modelo = sanitizeInput(form.modelo.value);

        // VALIDACIÓN BASICA
        if (!nombre || !area || !asunto || !categoria || !marca || !descripcion || !numero_inventario || !medio_soli || !modelo) {
            alert('Por favor, complete todos los campos');
            return;
        }

        // PREPARAR DATOS E INFORMACIÓN
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('area', area);
        formData.append('asunto', asunto);
        formData.append('categoria', categoria);
        formData.append('medio_soli', medio_soli);
        formData.append('marca', marca);
        formData.append('numero_inventario', numero_inventario);
        formData.append('descripcion', descripcion);
        formData.append('modelo', modelo);

        // ENVIAR DATOS AL SERVIDOR
        fetch('../tickets/php/procesar_ticket.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        text: "Ticket registrado exitosamente",
                        icon: "success"
                    }).then(() => {
                        if (document.location.pathname.includes('nuevo_ticket_admin.php')) {
                            window.location.href = '../tickets/mis_tickets_admin.php';
                        } else {
                            window.location.href = '../tickets/mis_tickets.php';
                        }
                    });
                    form.reset();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Ocurrió un error al enviar el ticket: " + data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Ocurrió un error al enviar el ticket: " + error.message
                });
            });
    });

    // Input sanitization function
    function sanitizeInput(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .trim();
    }
});



// BLOQUEAR U OCULTAR LOS BOTONES DEPENDIENDO DEL TIPO DE USUARIO
document.addEventListener('DOMContentLoaded', function () {
    const userRole = document.body.getAttribute('data-role');

    // ocultar boton ELIMINAR Y CAMBIAR DE ESTADO para el usuario 2 y 3 
    if (userRole === '2' || userRole === '3') {
        const eliminarBtn = document.getElementById('eliminarBtn');
        const cambiarEstadoBtn = document.getElementById('cambiarEstadoBtn');
        if (eliminarBtn, cambiarEstadoBtn) {
            eliminarBtn.style.display = 'none';
            cambiarEstadoBtn.style.display = 'none';

        }
    }

    // ocultar boton ELIMINAR TEMPORAL para el usuario 1 Y 3 

    if (userRole === '1' || userRole === '3') {
        const eliminarTemporalBtn = document.getElementById('eliminarTemporalBtn');
        if (eliminarTemporalBtn) {
            eliminarTemporalBtn.style.display = 'none';
        }
    }
    // ocultar boton ATENDER Y DESCARGAR para el usuario 3 

    if (userRole === '3') {
        const descargarBtn = document.getElementById('descargarBtn');
        if (descargarBtn) {
            descargarBtn.style.display = 'none';
        }
    }

});

// Botón para filtrar resultados
document.getElementById('btnFiltrar').addEventListener('click', (event) => {
    event.preventDefault();

    const estado = document.getElementById('categoriaFiltro').value;
    const busqueda = document.getElementById('busquedaFiltro').value;

    // Construir la URL dinámica con todos los parámetros necesarios
    let url = `../tickets/php/filtrar_tickets.php?categoria=${encodeURIComponent(estado)}&busqueda=${encodeURIComponent(busqueda)}`;

    // Si la categoría es "ELIMINADOS", agregar el filtro de estado_eliminado
    if (estado === "4") {
        url += "&estado_eliminado=1";
    }

    // Realizar la solicitud a la API de filtrado
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('ticketsTableBody');
            tableBody.innerHTML = '';

            if (data.length > 0) {
                data.forEach(ticket => {
                    // Asignar colores y etiquetas según el estado
                    const statusValue = parseInt(ticket.status, 10) || 0;
                    let estadoColor = '';
                    let estadoLabel = '';

                    switch (statusValue) {
                        case 1:
                            estadoColor = 'bg-proceso';
                            estadoLabel = 'EN PROCESO';
                            break;
                        case 2:
                            estadoColor = 'bg-validar';
                            estadoLabel = 'POR VALIDAR';
                            break;
                        case 3:
                            estadoColor = 'bg-finalizado';
                            estadoLabel = 'FINALIZADO';
                            break;
                        default:
                            estadoColor = 'bg-atender';
                            estadoLabel = 'POR ATENDER';
                    }

                    // Crear las filas de la tabla dinámicamente
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${ticket.nticket}</td>
                        <td>${ticket.nombre}</td>
                        <td>${ticket.area}</td>
                        <td>${ticket.asunto}</td>
                        <td>
                            <button class="btn bg-detalles" onclick='mostrarDetalles(${JSON.stringify(ticket)})'>
                                Detalles
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-tamanos ${estadoColor}" onclick='confirmarAtencion(${ticket.id}, "${estadoLabel}")'>
                                ${estadoLabel}
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="6">No se encontraron resultados</td></tr>';
            }
        })
        .catch(error => console.error('Error al filtrar:', error));
});

document.addEventListener('DOMContentLoaded', function () {
    const categoriaFiltro = document.getElementById('categoriaFiltro');
    const userRole = parseInt(document.body.getAttribute('data-role'), 10);

    // Eliminar la opción "ELIMINADOS" si el usuario no es de tipo 1
    if (userRole !== 1) {
        const opcionEliminados = categoriaFiltro.querySelector('option[value="4"]');
        if (opcionEliminados) {
            opcionEliminados.remove();
        }
    }
});


// Restablecer filtros
document.getElementById('btnRestablecer').addEventListener('click', (event) => {
    event.preventDefault();

    document.getElementById('categoriaFiltro').value = '';
    document.getElementById('busquedaFiltro').value = '';

    cargarTabla();
});



// Catálogo de estados
const estados = {
    0: "POR ATENDER",
    1: "EN PROCESO",
    2: "POR VALIDAR",
    3: "FINALIZADO"
};

function mostrarModalEstado(ticket) {
    // Cerrar el modal de detalles si está abierto
    const modalDetalles = bootstrap.Modal.getInstance(document.getElementById('detallesModal'));
    if (modalDetalles) {
        modalDetalles.hide();
    }

    // Asignar el ID del ticket al campo oculto del modal
    document.getElementById('ticketId').value = ticket.id;

    // Establecer el estado actual del ticket en el selector de estado
    document.getElementById('estadoSelect').value = ticket.status || "0";

    // Mostrar el modal de cambiar de estado
    const modal = new bootstrap.Modal(document.getElementById('cambiarEstadoModal'));
    modal.show();
}


// Función para cambiar el estado del ticket
function cambiarEstadoTicket() {
    const ticketId = document.getElementById('ticketId').value;
    const nuevoEstado = document.getElementById('estadoSelect').value;

    // Verificar que ambos valores existan
    if (!ticketId || nuevoEstado === undefined) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se seleccionó un ticket o estado válido'
        });
        return;
    }

    // Crear FormData para enviar los datos
    const formData = new FormData();
    formData.append('ticketId', ticketId);
    formData.append('nuevoEstado', nuevoEstado);

    // Enviar la solicitud fetch para actualizar el estado
    fetch('../tickets/php/cambiar_estado.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            if (data === "Estado actualizado correctamente") {
                // Si la actualización es exitosa, ocultar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('cambiarEstadoModal'));
                modal.hide();

                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Estado actualizado correctamente'
                }).then(() => {
                    // Recargar la tabla sin recargar la página completa
                    cargarTabla();
                });
            } else {
                throw new Error(data);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cambiar el estado del ticket: ' + error.message
            });
        });
}

///Actualizar estados

function actualizarEstadoSeleccionado() {
    const select = document.getElementById('categoriaFiltro');
    const estadoSeleccionado = select.options[select.selectedIndex].text;
    const estadoSeleccionadoElemento = document.getElementById('estadoSeleccionado');
    const ticketHeader = document.getElementById('ticketHeader');

    // Quitar las clases de color anteriores
    estadoSeleccionadoElemento.classList.remove('bg-atender', 'bg-proceso', 'bg-validar', 'bg-finalizado');
    ticketHeader.classList.remove('bg-atender', 'bg-proceso', 'bg-validar', 'bg-finalizado');

    // Asignar la clase de color según el valor seleccionado
    switch (select.value) {
        case "1":
            estadoSeleccionadoElemento.classList.add('bg-proceso');  // Azul
            ticketHeader.classList.add('bg-proceso');
            break;
        case "2":
            estadoSeleccionadoElemento.classList.add('bg-validar');   // Naranja
            ticketHeader.classList.add('bg-validar');
            break;
        case "3":
            estadoSeleccionadoElemento.classList.add('bg-finalizado');  // Verde
            ticketHeader.classList.add('bg-finalizado');
            break;
        default:
            estadoSeleccionadoElemento.classList.add('bg-atender');  // Verde
            ticketHeader.classList.add('bg-atender');
            break;
    }

    // Actualizar el texto del estado seleccionado
    estadoSeleccionadoElemento.textContent = estadoSeleccionado;
}

// Asignar el estado inicial (opcional)
document.addEventListener('DOMContentLoaded', (event) => {
    actualizarEstadoSeleccionado();
});

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function atenderTicketModal(ticketId) {
    const userRole = document.body.getAttribute('data-role');

    // Verificar si el usuario tiene el rol 1 o 2
    if (userRole !== '1' && userRole !== '2') {
        return; // No hacer nada si el usuario no tiene el rol adecuado
    }

    // Obtener la fecha y hora actuales
    const ahora = new Date();
    const year = ahora.getFullYear();
    const month = (ahora.getMonth() + 1).toString().padStart(2, '0');
    const day = ahora.getDate().toString().padStart(2, '0');
    const hours = ahora.getHours().toString().padStart(2, '0');
    const minutes = ahora.getMinutes().toString().padStart(2, '0');
    const seconds = ahora.getSeconds().toString().padStart(2, '0');

    // Formatear la fecha y hora en el formato requerido para MySQL (YYYY-MM-DD HH:MM:SS)
    const fechaHoraActual = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    // Crear el modal HTML dinámicamente
    const modalHTML = `
        <div class="modal fade" id="atencionModal" tabindex="-1" aria-labelledby="atencionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="atencionModalLabel">Atención de Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAtencion">
                            <div class="mb-3">
                                <label for="fechaAtencion" class="form-label">Fecha de atención</label>
                                <input class="form-control" id="fechaAtencion" value="${fechaHoraActual}" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="personalAtendio" class="form-label">Personal quien atendió</label>
                                <input type="text" class="form-control" id="personalAtendio" value="${nombreAtendio}" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="tipoFalla" class="form-label">Tipo de falla</label>
                                <select class="form-select" id="tipoFalla" required>
                                    <option value="">Seleccione una opción</option>
                                    <option value="1">Hardware</option>
                                    <option value="2">Software</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="descripcionSolucion" class="form-label">Descripción detallada de la solución</label>
                                <textarea class="form-control" id="descripcionSolucion" rows="3" maxlength="165" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="usoMaterial" class="form-label">¿Utilizaste material o piezas?</label>
                                <select class="form-select" id="usoMaterial" required>
                                    <option value="">Seleccione una opción</option>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div id="materialFields" class="mb-3" style="display: none;">
                                <label for="cantidadMateriales" class="form-label">¿Cuántos materiales usaste?</label>
                                <select class="form-select" id="cantidadMateriales">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                                <div id="materialInputs"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="guardarAtencion(${ticketId})">ENVIAR</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Insertar el modal en el cuerpo del documento
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Mostrar el modal
    var modal = new bootstrap.Modal(document.getElementById('atencionModal'));
    modal.show();

    // Manejar el cambio en la selección de uso de material
    document.getElementById('usoMaterial').addEventListener('change', function () {
        const materialFields = document.getElementById('materialFields');
        if (this.value === "1") {
            materialFields.style.display = 'block';
            generarCamposMateriales(1); // Generar campos para 1 material por defecto
        } else {
            materialFields.style.display = 'none';
        }
    });

    // Manejar el cambio en la cantidad de materiales
    document.getElementById('cantidadMateriales').addEventListener('change', function () {
        generarCamposMateriales(parseInt(this.value));
    });

    // Eliminar el modal del DOM cuando se cierre
    document.getElementById('atencionModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

// Función para generar los campos de materiales
function generarCamposMateriales(cantidad) {
    const materialInputs = document.getElementById('materialInputs');
    materialInputs.innerHTML = '';

    for (let i = 1; i <= cantidad; i++) {
        materialInputs.innerHTML += `
            <div class="mb-3">
                <label for="material_${i}" class="form-label">Descripción del material ${i}</label>
                <input type="text" class="form-control" id="material_${i}" required>
            </div>
            <div class="mb-3">
                <label for="piezas_${i}" class="form-label">Cantidad de piezas ${i}</label>
                <input type="text" class="form-control" id="piezas_${i}" required>
            </div>
        `;
    }
}

// Función para guardar la atención
function guardarAtencion(ticketId) {
    const fechaAtencion = document.getElementById('fechaAtencion').value;
    const personalAtendio = document.getElementById('personalAtendio').value;
    const tipoFalla = document.getElementById('tipoFalla').value;
    const descripcionSolucion = document.getElementById('descripcionSolucion').value;
    const usoMaterial = document.getElementById('usoMaterial').value;

    // Validar que todos los campos estén llenos
    if (!fechaAtencion || !personalAtendio || !tipoFalla || !descripcionSolucion || !usoMaterial) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, complete todos los campos'
        });
        return;
    }

    // Crear FormData para enviar los datos
    const formData = new FormData();
    formData.append('ticketId', ticketId);
    formData.append('fechaAtencion', fechaAtencion);
    formData.append('personalAtendio', personalAtendio);
    formData.append('tipoFalla', tipoFalla);
    formData.append('descripcionSolucion', descripcionSolucion);
    formData.append('usoMaterial', usoMaterial);

    // Si se usó material, agregar los detalles de los materiales
    if (usoMaterial === "1") {
        const cantidadMateriales = parseInt(document.getElementById('cantidadMateriales').value);
        for (let i = 1; i <= 3; i++) {
            const material = document.getElementById(`material_${i}`) ? document.getElementById(`material_${i}`).value : "N/A";
            const piezas = document.getElementById(`piezas_${i}`) ? document.getElementById(`piezas_${i}`).value : "N/A";
            formData.append(`material_${i}`, material);
            formData.append(`piezas_${i}`, piezas);
        }
    } else {
        // Si no se usó material, llenar todos los campos con "N/A"
        for (let i = 1; i <= 3; i++) {
            formData.append(`material_${i}`, "N/A");
            formData.append(`piezas_${i}`, "N/A");
        }
    }

    // Enviar datos al servidor
    fetch('../tickets/php/guardar_atencion.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('atencionModal'));
                modal.hide();

                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'La información se ha guardado correctamente'
                }).then(() => {
                    // Recargar la tabla
                    cargarTabla();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar la información: ' + error.message
            });
        });
}

////////////////

// Mostrar Detalles
function mostrarDetalles(ticket) {
    // Obtener el tipo de usuario desde el atributo data-role del body
    const userRole = document.body.getAttribute('data-role');
    document.getElementById('modalTitulo').innerHTML = "<strong>#TICKET: " + ticket.nticket + "</strong>";

    // Configurar detalles en el modal
    document.getElementById('detalleNombre').innerHTML = ticket.nombre;
    document.getElementById('detalleArea').innerHTML = ticket.area;
    document.getElementById('detalleAsunto').innerHTML = ticket.asunto;
    document.getElementById('detalleMarca').innerHTML = ticket.marca;
    document.getElementById('detalleModelo').innerHTML = ticket.modelo;
    document.getElementById('detalleCategoria').innerHTML = ticket.categoria;
    document.getElementById('detalleInventario').innerHTML = ticket.numero_inventario;
    document.getElementById('detalleFechaCreacion').innerHTML = ticket.fecha_creacion;

    const medioSolicitud = {
        1: "Personal",
        2: "Teléfono",
        3: "Sistema"
    };

    document.getElementById('detalleMedioSoli').innerHTML = (medioSolicitud[ticket.medio_soli] || "Desconocido");

    document.getElementById('detalleDescripcion').innerHTML = ticket.descripcion || "Por determinar";
    document.getElementById('detalleFechaInicio').innerHTML = ticket.fecha_inicio || "Por determinar";
    document.getElementById('detalleFechaAtencion').innerHTML = ticket.fecha_atencion || "Por determinar";
    document.getElementById('detallePersonalAtendio').innerHTML = ticket.nombre_atendio || "Por determinar";
    document.getElementById('detalleNoResuelto').innerHTML = ticket.comentario || "Por determinar";

    const tipoFalla = {
        1: "Hardware",
        2: "Software"
    };

    document.getElementById('detalleTipoFalla').innerHTML = (tipoFalla[ticket.tipo_falla] || "Por determinar");

    document.getElementById('detalleDescSolucion').innerHTML = ticket.desc_solucion || "Por determinar";

    // Mostrar u ocultar detalles de eliminación según el tipo de usuario
    const eliminadoSection = document.getElementById('eliminadoSection');

    if (userRole == 1) { // Solo para usuario tipo 1
        document.getElementById('detalleEliminado').innerHTML = ticket.eliminado_por || "Por determinar";
        document.getElementById('detalleFechaEliminado').innerHTML = ticket.fecha_eliminacion || "Por determinar";
        eliminadoSection.style.display = 'block'; // Mostrar la sección
    } else {
        eliminadoSection.style.display = 'none'; // Ocultar la sección
    }

    // Configurar el ID del ticket en el botón "Eliminar"
    const eliminarBtn = document.getElementById('eliminarBtn');
    eliminarBtn.setAttribute('data-id', ticket.id);

    const eliminarTemporalBtn = document.getElementById('eliminarTemporalBtn');
    eliminarTemporalBtn.setAttribute('data-id', ticket.id);

    // Configurar el ticket en el botón de cambio de estado
    const cambiarEstadoBtn = document.getElementById('cambiarEstadoBtn');
    cambiarEstadoBtn.setAttribute('onclick', `mostrarModalEstado(${JSON.stringify(ticket)})`);


    // Configurar el ID del ticket en el botón de restauración
    const restaurarBtn = document.getElementById('restaurarBtn');
    restaurarBtn.setAttribute('data-id', ticket.id);

    // Mostrar u ocultar el botón de restauración según el estado del ticket
    if (ticket.estado_eliminado == 1) {
        restaurarBtn.style.display = 'inline-block'; // Mostrar el botón
    } else {
        restaurarBtn.style.display = 'none'; // Ocultar el botón
    }
    if (userRole === '2' || userRole === '3') {
        const restaurarBtn = document.getElementById('restaurarBtn');
        if (restaurarBtn) {
            restaurarBtn.style.display = 'none';
        }
    }


    // ASDASDSASDASDSASDASDSASDASDSASDASDSASDASDSASDASDSASDASDSASDASDSASDASDSASDASDSASDASDSASDASDSA

    // Mostrar el modal
    var modal = new bootstrap.Modal(document.getElementById('detallesModal'));
    modal.show();
}

// Manejar el clic del botón "Eliminar" en el modal
document.getElementById('eliminarBtn').addEventListener('click', () => {
    const id = document.getElementById('eliminarBtn').getAttribute('data-id');
    if (id) {
        eliminarTicket(id);
    } else {
        Swal.fire({
            title: 'Error',
            text: 'No se pudo obtener el ID del ticket.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
});

// Eliminar ticket con confirmación
function eliminarTicket(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará el ticket de forma permanente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamada al servidor para eliminar el ticket
            fetch('../tickets/php/eliminar_ticket.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Eliminado permanentemente!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });

                        // Cerrar el modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('detallesModal'));
                        if (modal) modal.hide();

                        // Actualizar la tabla
                        cargarTabla();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al intentar eliminar el ticket.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }
    });
}



///////////////////// cambiar estado del botón de validación /////////////////////

// Función para mostrar el modal de validación
function validarTicketModal(ticketId) {
    const userRole = document.body.getAttribute('data-role');

    // Verificar si el usuario tiene el rol 1 o 3
    if (userRole !== '1' && userRole !== '3') {
        return; // No hacer nada si el usuario no tiene el rol adecuado
    }


    // Cerrar el modal de detalles si está abierto
    const detallesModal = bootstrap.Modal.getInstance(document.getElementById('detallesModal'));
    if (detallesModal) {
        detallesModal.hide();
    }

    // Crear el modal HTML dinámicamente
    const modalHTML = `
        <div class="modal fade" id="validacionModal" tabindex="-1" aria-labelledby="validacionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="validacionModalLabel">Validación de Solución</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <h6>¿El problema fue resuelto?</h6>
                            <br>
                            <button class="btn btn-success mx-2" onclick="problemaResuelto(${ticketId})">Sí</button>
                            <button class="btn btn-danger mx-2" onclick="problemaPendiente(${ticketId})">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Agregar el modal al documento
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('validacionModal'));
    modal.show();

    // Eliminar el modal del DOM cuando se cierre
    document.getElementById('validacionModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}
// Función cuando el problema fue resuelto
function problemaResuelto(ticketId) {
    // Cerrar el modal de validación
    const validacionModal = bootstrap.Modal.getInstance(document.getElementById('validacionModal'));
    validacionModal.hide();

    // Crear modal de calificación
    const modalHTML = `
        <div class="modal fade" id="calificacionModal" tabindex="-1" aria-labelledby="calificacionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="calificacionModalLabel">Calificar Servicio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <h6>Por favor, califique el servicio recibido</h6>
                            <div class="rating">
                                ${Array.from({ length: 5 }, (_, i) => i + 1).map(num => `
                                    <span class="star" data-rating="${num}" 
                                        onmouseover="highlightStars(${num})"
                                        onmouseout="resetStars()"
                                        onclick="setRating(${num}, ${ticketId})">
                                        ★
                                    </span>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Agregar estilos para las estrellas
    const styleSheet = document.createElement("style");
    styleSheet.textContent = `
        .rating { font-size: 5em; }
        .star { cursor: pointer; color: #ddd; transition: color 0.2s ease-in-out; }
        .star.active { color: #ffd700; }
        .star.highlight { color: #ffd700; }
    `;
    document.head.appendChild(styleSheet);

    // Agregar el modal al documento
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('calificacionModal'));
    modal.show();

    // Eliminar el modal del DOM cuando se cierre
    document.getElementById('calificacionModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

// Función para establecer la calificación
function setRating(rating, ticketId) {
    // Actualizar visualización de estrellas
    document.querySelectorAll('.star').forEach(star => {
        star.classList.remove('active');
        if (parseInt(star.dataset.rating) <= rating) {
            star.classList.add('active');
        }
    });

    // Enviar calificación al servidor (puedes agregar aquí tu lógica de backend)
    const formData = new FormData();
    formData.append('ticketId', ticketId);
    formData.append('calificacion', rating);
    formData.append('accion', 'calificar');

    fetch('../tickets/php/validar_ticket.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('calificacionModal'));
                modal.hide();

                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Gracias por su calificación!',
                    text: 'El ticket ha sido finalizado correctamente'
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar la calificación: ' + error.message
            });
        });
}

// Función para iluminar estrellas al pasar el mouse
function highlightStars(rating) {
    document.querySelectorAll('.star').forEach(star => {
        const starRating = parseInt(star.dataset.rating, 10);
        if (starRating <= rating) {
            star.classList.add('highlight');
        } else {
            star.classList.remove('highlight');
        }
    });
}

// Función para resetear las estrellas al quitar el mouse
function resetStars() {
    document.querySelectorAll('.star').forEach(star => {
        star.classList.remove('highlight');
    });
}

// Enviar calificación al servidor
const formData = new FormData();
formData.append('ticketId', ticketId);
formData.append('calificacion', rating);
formData.append('accion', 'calificar');

fetch('../tickets/php/validar_ticket.php', {
    method: 'POST',
    body: formData
})
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('calificacionModal'));
            modal.hide();

            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Gracias por su calificación!',
                text: 'El ticket ha sido finalizado correctamente'
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al guardar la calificación: ' + error.message
        });
    });

// Función cuando el problema no fue resuelto
function problemaPendiente(ticketId) {
    // Cerrar el modal de validación
    const validacionModal = bootstrap.Modal.getInstance(document.getElementById('validacionModal'));
    validacionModal.hide();

    // Crear modal para comentarios
    const modalHTML = `
        <div class="modal fade" id="comentarioModal" tabindex="-1" aria-labelledby="comentarioModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="comentarioModalLabel">Detalles especificos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comentario">Por favor, describa por qué el problema no fue resuelto:</label>
                            <textarea class="form-control" id="comentario" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="enviarComentario(${ticketId})">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Agregar el modal al documento
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('comentarioModal'));
    modal.show();

    // Eliminar el modal del DOM cuando se cierre
    document.getElementById('comentarioModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

// Función para enviar comentario
function enviarComentario(ticketId) {
    const comentario = document.getElementById('comentario').value;

    if (!comentario.trim()) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, ingrese un comentario'
        });
        return;
    }

    const formData = new FormData();
    formData.append('ticketId', ticketId);
    formData.append('comentario', comentario);
    formData.append('accion', 'comentar');

    fetch('../tickets/php/validar_ticket.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('comentarioModal'));
                modal.hide();

                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Comentario Enviado',
                    text: 'El ticket ha sido reabierto para su atención'
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al enviar el comentario: ' + error.message
            });
        });
}


// Función para cargar la tabla
function cargarTabla() {
    const url = `../tickets/php/filtrar_tickets.php?categoria=&busqueda=`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('ticketsTableBody');
            tableBody.innerHTML = '';

            if (data.length > 0) {
                data.forEach(ticket => {
                    // Determinar el estado y color basado en el status
                    const statusValue = parseInt(ticket.status, 10) || 0;
                    let estadoColor = '';
                    let estadoLabel = estados[statusValue] || "POR ATENDER";

                    // Asignar color según el status
                    switch (statusValue) {
                        case 1:
                            estadoColor = 'bg-proceso';
                            estadoLabel = 'EN PROCESO';
                            break;
                        case 2:
                            estadoColor = 'bg-validar';
                            estadoLabel = 'POR VALIDAR';
                            break;
                        case 3:
                            estadoColor = 'bg-finalizado';
                            estadoLabel = 'FINALIZADO';
                            break;
                        default:
                            estadoColor = 'bg-atender';
                            estadoLabel = 'POR ATENDER';
                    }

                    // Crear la fila de la tabla
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${ticket.nticket}</td>
                        <td>${ticket.nombre}</td>
                        <td>${ticket.area}</td>
                        <td>${ticket.asunto}</td>
                        <td>
                            <button class="btn bg-detalles" onclick='mostrarDetalles(${JSON.stringify(ticket)})'>
                                Detalles
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-tamanos ${estadoColor}" onclick='confirmarAtencion(${ticket.id}, "${estadoLabel}")'>
                                ${estadoLabel}
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="6">No se encontraron resultados</td></tr>';
            }
        })
        .catch(error => console.error('Error al cargar los datos:', error));
}

///Funcion eliminar boton temporal, cambio de estado

function eliminarTemporal() {
    const ticketId = document.getElementById('eliminarTemporalBtn').getAttribute('data-id');

    if (!ticketId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo obtener el ID del ticket.'
        });
        return;
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "El ticket será eliminado.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear FormData para enviar los datos
            const formData = new FormData();
            formData.append('ticketId', ticketId);

            // Realizar la petición al servidor
            fetch('../tickets/php/eliminar_temporal.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: 'Ticket eliminado con éxito',
                            icon: 'success'
                        }).then(() => {
                            // Cerrar el modal de detalles
                            const modal = bootstrap.Modal.getInstance(document.getElementById('detallesModal'));
                            if (modal) modal.hide();

                            // Recargar la tabla
                            cargarTabla();
                        });
                    } else {
                        throw new Error(data.message || 'Error al eliminar el ticket');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: `Error al eliminar el ticket: ${error.message}`,
                        icon: 'error'
                    });
                });
        }
    });
}

// ///Funcion restuarar ticket
function restaurarTicket() {
    const ticketId = document.getElementById('restaurarBtn').getAttribute('data-id');
    console.log("Ticket ID:", ticketId); // Verificar en la consola del navegador

    if (!ticketId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo obtener el ID del ticket.'
        });
        return;
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "El ticket será restaurado.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear FormData para enviar los datos
            const formData = new FormData();
            formData.append('ticketId', ticketId);

            // Realizar la petición al servidor
            fetch('../tickets/php/restaurar_ticket.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Respuesta del servidor:", data); // Verificar en la consola del navegador
                    if (data.success) {
                        Swal.fire({
                            title: '¡Restaurado!',
                            text: 'Ticket restaurado con éxito',
                            icon: 'success'
                        }).then(() => {
                            // Cerrar el modal de detalles
                            const modal = bootstrap.Modal.getInstance(document.getElementById('detallesModal'));
                            if (modal) modal.hide();

                            // Recargar la tabla
                            cargarTabla();
                        });
                    } else {
                        throw new Error(data.message || 'Error al restaurar el ticket');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: `Error al restaurar el ticket: ${error.message}`,
                        icon: 'error'
                    });
                });
        }
    });
}

/////descargar solo un ticket
function descargarTicket() {
    const ticketId = document.getElementById('eliminarBtn').getAttribute('data-id');
    if (ticketId) {
        window.open(`../tickets/fpdf/hoja_soporte_tecnico_individual.php?id=${ticketId}`, '_blank');
    } else {
        Swal.fire({
            title: 'Error',
            text: 'No se pudo obtener el ID del ticket.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}


/////////// atender ticket

function confirmarAtencion(ticketId, estadoActual) {
    const userRole = document.body.getAttribute('data-role');

    // Verificar si el usuario tiene el rol 1 o 2 para atender tickets
    if (estadoActual === "POR ATENDER" && (userRole !== '1' && userRole !== '2')) {
        return; // No hacer nada si el usuario no tiene el rol adecuado
    }

    // Verificar si el usuario tiene el rol 1 o 3 para validar tickets
    if (estadoActual === "POR VALIDAR" && (userRole !== '1' && userRole !== '3')) {
        return; // No hacer nada si el usuario no tiene el rol adecuado
    }

    if (estadoActual === "POR ATENDER") {
        // Si el estado es "POR ATENDER", mostrar la confirmación para atender el ticket
        Swal.fire({
            title: '¿Desea atender este ticket?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, update the ticket status
                actualizarEstadoTicket(ticketId);
            }
        });
    } else if (estadoActual === "POR VALIDAR") {
        // Si el estado es "POR VALIDAR", llamar directamente a la función de validación
        validarTicketModal(ticketId);
    } else if (estadoActual === "EN PROCESO") {
        // Si el estado es "POR PROCESO", llamar directamente a la función de ATENDER
        atenderTicketModal(ticketId);
    }
}

function actualizarEstadoTicket(ticketId) {
    // Create form data to send
    const formData = new FormData();
    formData.append('ticketId', ticketId);

    // Send request to update ticket status
    fetch('../tickets/php/atender_ticket.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Ticket asignado!',
                    text: 'El ticket ha sido asignado correctamente'
                }).then(() => {
                    // Reload table to show updated status
                    cargarTabla();
                });
            } else {
                throw new Error(data.message || 'Error al actualizar el ticket');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al actualizar el estado del ticket: ' + error.message
            });
        });
}


//