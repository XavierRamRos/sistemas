<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="img/UNEVE.png">
</head>
<body>
    <div class="background">
        <div class="login-container">
            <div class="logo">
                <img src="img/UNEVE.png" alt="Logo" />
            </div>
            <h1>INICIAR SESIÓN PRUEBA DE GIT</h1>
            <form id="loginForm" onsubmit="return handleLogin(event)">
                <input type="text" id="num_empleado" placeholder="Número de Empleado" name="num_empleado" required />
                <input type="password" id="contraseña" placeholder="Contraseña" name="contraseña" required />
                <button type="submit" value="Ingresar">INGRESAR</button>
            </form>
        </div>
    </div>
    <footer>
        <div class="barra-bottom">
            <img src="tickets/imgs/logoedomex.png" alt="logoedomex" class="logoedomex">
            <p class="footertexto">DEPARTAMENTO DE INFORMÁTICA</p>
        </div>
    </footer>

    <script src="js/login.js"></script>
</body>
</html>
