$(document).ready(function () {
    $('#usuariosTable').DataTable({
        pageLength: 50, // Mostrar 25 registros por defecto
        lengthMenu: [[50, 100, 150], [50, 100, 150]], // Submenús personalizados
        ordering: false, // Deshabilitar la ordenación

        // Personalización de idioma
        language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "infoFiltered": "(filtrado de _MAX_ entradas totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": activar para ordenar la columna ascendente",
                "sortDescending": ": activar para ordenar la columna descendente"
            }
        }
    });

    $('#usuariosTable').on('click', '.btn-editar', function () {
        var idUsuario = $(this).data('id');
        cargarUsuario(idUsuario);
    });

    $('#editarModal').on('shown.bs.modal', function () {
        $(this).removeAttr('aria-hidden');
        $('#edit_nombre').focus();
    });

    $('#editarModal').on('hidden.bs.modal', function () {
        $(this).attr('aria-hidden', 'true');
    });
});



function cargarUsuario(idUsuario) {
    $.ajax({
        url: 'php/cargar_usuario.php',
        type: 'POST',
        data: { id_usuario: idUsuario },
        success: function (response) {
            var usuario = JSON.parse(response);
            $('#usuario_id').val(usuario.id_usuario);
            $('#edit_nombre').val(usuario.nombre);
            $('#edit_apellido_paterno').val(usuario.apellido_paterno);
            $('#edit_apellido_materno').val(usuario.apellido_materno);
            $('#edit_usuario').val(usuario.usuario);
            $('#edit_area').val(usuario.id_area);
            $('#edit_puesto').val(usuario.puesto);
            $('#edit_num_empleado').val(usuario.num_empleado);
            $('#edit_extension').val(usuario.extension);
            $('#edit_correo').val(usuario.correo);
            $('#edit_tipo_usuario').val(usuario.id_tipo_usuario);
            $('#editarModal').modal('show');
        }
    });
}

function actualizarUsuario() {
    var formData = $('#editarForm').serialize();
    $.ajax({
        url: 'php/actualizar_usuario.php',
        type: 'POST',
        data: formData,
        success: function (response) {
            if (response == 'success') {
                Swal.fire('¡Éxito!', 'Usuario actualizado correctamente', 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', 'Hubo un problema al actualizar el usuario', 'error');
            }
        }
    });
}


// ELIMINAR USUARIO

function eliminarUsuario() {
    var idUsuario = $('#usuario_id').val(); // Obtener el ID del usuario desde el modal

    Swal.fire({
        title: '¿Deseas eliminarlo?',
        text: "Se eliminará el usuario",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'php/eliminar_usuario.php',
                type: 'POST',
                data: { id_usuario: idUsuario },
                success: function (response) {
                    if (response == 'success') {
                        Swal.fire('¡Éxito!', 'Usuario eliminado correctamente', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al eliminar el usuario', 'error');
                    }
                }
            });
        }
    });
}