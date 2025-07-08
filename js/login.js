function handleLogin(event) {
    event.preventDefault();

    const num_empleado = document.getElementById('num_empleado').value;
    const contraseña = document.getElementById('contraseña').value;

    // Basic validation
    if (!num_empleado || !contraseña) {
        Swal.fire({
            icon: 'error',
            title: 'Campos requeridos',
            text: 'Por favor complete todos los campos.',
        });
        return false;
    }

    // Create FormData
    const formData = new FormData();
    formData.append('num_empleado', num_empleado);
    formData.append('contraseña', contraseña);

    // Show loading state
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'PROCESANDO...';

    // Send request
    fetch('login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                let errorMessage = data.message;
                if (data.debug) {
                    errorMessage += '\n\nDetalles de depuración:\n' + JSON.stringify(data.debug, null, 2);
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error en algún campo',
                    text: errorMessage,
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error del servidor',
                text: 'Error de conexión: ' + error.message,
            });
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = 'INGRESAR';
        });

    return false;
}