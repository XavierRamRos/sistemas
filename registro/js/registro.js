document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('registroForm').addEventListener('submit', handleSubmit);
});

function handleSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);

    $.ajax({
        url: 'php/registro.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Usuario registrado correctamente'
                }).then(() => {
                    document.getElementById('registroForm').reset();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al registrar usuario'
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en la petición:', xhr.responseText);

            let errorMessage = 'Error en la conexión con el servidor';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMessage = response.message;
                }
            } catch (e) {
                console.error('Error al parsear la respuesta:', e);
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        }
    });
}