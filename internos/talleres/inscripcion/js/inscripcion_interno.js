$(document).ready(function () {
    console.log('Documento listo, jQuery funcionando');

    // Cargar días cuando se selecciona un taller
    $('#taller').change(function() {
        const idTaller = $(this).val();
        
        if (!idTaller) {
            $('#dia').prop('disabled', true).html('<option value="">Primero seleccione un taller</option>');
            $('#horario').prop('disabled', true).html('<option value="">Primero seleccione un día</option>');
            return;
        }

        // Mostrar loading
        $('#dia').prop('disabled', true).html('<option value="">Cargando días...</option>');
        $('#horario').prop('disabled', true).html('<option value="">Primero seleccione un día</option>');

        // Obtener días disponibles para el taller
        $.ajax({
            url: 'php/obtener_dias_taller.php',
            type: 'POST',
            dataType: 'json',
            data: { id_taller: idTaller },
            success: function(response) {
                if (response.success && response.dias.length > 0) {
                    let options = '<option value="">Seleccione un día</option>';
                    response.dias.forEach(dia => {
                        options += `<option value="${dia.id_dia}">${dia.nombre}</option>`;
                    });
                    $('#dia').html(options).prop('disabled', false);
                } else {
                    $('#dia').html('<option value="">No hay días disponibles</option>').prop('disabled', true);
                }
            },
            error: function() {
                $('#dia').html('<option value="">Error al cargar días</option>').prop('disabled', true);
            }
        });
    });

    // Cargar horarios cuando se selecciona un día
    $('#dia').change(function() {
        const idTaller = $('#taller').val();
        const idDia = $(this).val();
        
        if (!idDia) {
            $('#horario').prop('disabled', true).html('<option value="">Primero seleccione un día</option>');
            return;
        }

        // Mostrar loading
        $('#horario').prop('disabled', true).html('<option value="">Cargando horarios...</option>');

        // Obtener horarios disponibles para el taller y día
        $.ajax({
            url: 'php/obtener_horarios_taller.php',
            type: 'POST',
            dataType: 'json',
            data: { 
                id_taller: idTaller,
                id_dia: idDia 
            },
            success: function(response) {
                if (response.success && response.horarios.length > 0) {
                    let options = '<option value="">Seleccione un horario</option>';
                    response.horarios.forEach(horario => {
                        options += `<option value="${horario.id_horario_taller}">${horario.hora_inicio} - ${horario.hora_fin}</option>`;
                    });
                    $('#horario').html(options).prop('disabled', false);
                } else {
                    $('#horario').html('<option value="">No hay horarios disponibles</option>').prop('disabled', true);
                }
            },
            error: function() {
                $('#horario').html('<option value="">Error al cargar horarios</option>').prop('disabled', true);
            }
        });
    });

    // Validación del formulario antes de enviar
    $('#formInscripcion').submit(function (e) {
        e.preventDefault();

        // Validar campos requeridos
        let isValid = true;
        $('.required-field').each(function () {
            const fieldId = $(this).attr('for');
            const fieldValue = $('#' + fieldId).val();

            if (!fieldValue) {
                isValid = false;
                $('#' + fieldId).addClass('is-invalid');
            } else {
                $('#' + fieldId).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire('Error', 'Por favor complete todos los campos requeridos', 'error');
            return;
        }

        // Validar formato de correo
        const correo = $('#correo').val();
        if (!validateEmail(correo)) {
            Swal.fire('Error', 'Por favor ingrese un correo electrónico válido', 'error');
            $('#correo').addClass('is-invalid');
            return;
        }

        // Validar formato de teléfono
        const telefono = $('#num_movil').val();
        if (!validatePhone(telefono)) {
            Swal.fire('Error', 'Por favor ingrese un número de teléfono válido (10 dígitos)', 'error');
            $('#num_movil').addClass('is-invalid');
            return;
        }

        // Validar fecha de nacimiento
        const fechaNacimiento = new Date($('#fecha_nacimiento').val());
        const hoy = new Date();
        const edadMinima = new Date(hoy.getFullYear() - 18, hoy.getMonth(), hoy.getDate());

        if (fechaNacimiento > edadMinima) {
            Swal.fire('Error', 'Debe ser mayor de 18 años para inscribirse', 'error');
            $('#fecha_nacimiento').addClass('is-invalid');
            return;
        }

        // Si todo es válido, proceder con el envío
        enviarFormulario();
    });

    // Función para validar email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Función para validar teléfono (10 dígitos)
    function validatePhone(phone) {
        const re = /^\d{10}$/;
        return re.test(phone);
    }

    // Función para enviar el formulario
    function enviarFormulario() {
        const formData = {
            nombre: $('#nombre').val(),
            paterno: $('#paterno').val(),
            materno: $('#materno').val(),
            id_taller: $('#taller').val(),
            matricula: $('#matricula').val(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            carrera: $('#carrera').val(),
            num_movil: $('#num_movil').val(),
            correo: $('#correo').val(),
            calle: $('#calle').val(),
            colonia: $('#colonia').val(),
            num_interior: $('#num_interior').val(),
            num_exterior: $('#num_exterior').val(),
            id_salud: $('#id_salud').val(),
            num_medico: $('#num_medico').val(),
            padecimiento: $('#padecimiento').val(),
            alergia: $('#alergia').val(),
            id_sexo: $('#id_sexo').val(),
            nombre_alt: $('#nombre_alt').val(),
            paterno_alt: $('#paterno_alt').val(),
            movil_alt: $('#movil_alt').val(),
            calle_alt: $('#calle_alt').val(),
            colonia_alt: $('#colonia_alt').val(),
            num_interno_alt: $('#num_interno_alt').val(),
            num_externo_alt: $('#num_externo_alt').val(),
            fecha_registro: $('#fecha_registro').val(),
            id_usuario_registro: $('#id_usuario_registro').val(),
            id_tipo: $('#id_tipo').val(), // 1 = Interno
            id_dia: $('#dia').val(),
            id_horario_taller: $('#horario').val()
        };

        console.log('Enviando datos:', formData);

        // Mostrar spinner de carga
        Swal.fire({
            title: 'Procesando inscripción',
            html: 'Por favor espere...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar datos por AJAX
        $.ajax({
            url: 'php/guardar_inscripcion_interno.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function (response) {
                console.log('Respuesta recibida:', response);
                Swal.close();

                if (response.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Limpiar formulario después de guardar
                            $('#formInscripcion')[0].reset();
                        }
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud:', textStatus, errorThrown);
                Swal.close();

                let errorMsg = 'Ocurrió un error al procesar la solicitud';
                try {
                    const response = JSON.parse(jqXHR.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    errorMsg += '<br><br>' + jqXHR.responseText.substring(0, 200);
                }

                Swal.fire({
                    title: 'Error',
                    html: errorMsg,
                    icon: 'error'
                });
            }
        });
    }

    // Quitar clase de error al empezar a escribir en campos requeridos
    $('.required-field').each(function () {
        const fieldId = $(this).attr('for');
        $('#' + fieldId).on('input', function () {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
            }
        });
    });
});