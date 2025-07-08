function validarYRestablecer() {
    const passwordActual = $('#password_actual').val();
    const nuevaPassword = $('#nueva_password').val();
    const confirmarPassword = $('#confirmar_password').val();

    // Validar que los campos no estén vacíos
    if (!passwordActual || !nuevaPassword || !confirmarPassword) {
        Swal.fire('Error', 'Todos los campos son obligatorios', 'error');
        return;
    }

    // Validar que la nueva contraseña y la confirmación coincidan
    if (nuevaPassword !== confirmarPassword) {
        Swal.fire('Error', 'La nueva contraseña y la confirmación no coinciden', 'error');
        return;
    }

    // Enviar la solicitud AJAX
    $.ajax({
        url: 'php/restablecer_password.php',
        type: 'POST',
        data: {
            password_actual: passwordActual,
            nueva_password: nuevaPassword,
            confirmar_password: confirmarPassword
        },
        success: function (response) {
            if (response === 'success') {
                Swal.fire('Éxito', 'Contraseña actualizada correctamente', 'success').then(() => {
                    window.location.href = '../subsistemas/subsistemas.php'; // Redirigir después de éxito
                });
            } else {
                Swal.fire('Error', response, 'error');
            }
        },
        error: function () {
            Swal.fire('Error', 'Hubo un problema al procesar la solicitud', 'error');
        }
    });
}