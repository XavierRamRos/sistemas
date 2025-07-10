<?php
// Definir los permisos por tipo de usuario según la nueva estructura
$permisos_por_tipo = [
    1 => [ // Administrador (suponiendo que id_tipo_usuario=1 es admin)
        "CONSULTAR_INSCRITOS", 
        "GESTION_TALLERES",
        "GESTION_HORARIOS",
        "REPORTES",
        "GESTION_USUARIOS",
        "CONFIGURACION"
    ],
    2 => [ // Usuario estándar
        "CONSULTAR_INSCRITOS",
        "GESTION_TALLERES",
        "REPORTES"
    ],
    3 => [ // Usuario limitado
        "CONSULTAR_INSCRITOS"
    ]
];

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION['id_tipo_usuario'] ?? 3; // Valor por defecto si no está definido

// Obtener los módulos permitidos para el tipo de usuario
$modulos_permitidos = $permisos_por_tipo[$tipo_usuario] ?? [];

// Mapear los módulos a sus rutas e íconos
$modulos_con_rutas = [
    "CONSULTAR_INSCRITOS" => [
        "ruta" => "../consulta_inscritos/consulta.php", 
        "icono" => "buscarPaciente.png",
        "nombre" => "Consultar Inscritos"
    ],
    "GESTION_TALLERES" => [
        "ruta" => "../gestion_talleres/talleres.php", 
        "icono" => "nuevoPaciente.png",
        "nombre" => "Gestión de Talleres"
    ],
    "GESTION_HORARIOS" => [
        "ruta" => "../gestion_horarios/horarios.php", 
        "icono" => "horarios.png",
        "nombre" => "Gestión de Horarios"
    ],
    "REPORTES" => [
        "ruta" => "../reportes/reportes.php", 
        "icono" => "indicadores.png",
        "nombre" => "Reportes"
    ],
    "GESTION_USUARIOS" => [
        "ruta" => "../gestion_usuarios/usuarios.php", 
        "icono" => "editarPaciente.png",
        "nombre" => "Gestión de Usuarios"
    ],
    "CONFIGURACION" => [
        "ruta" => "../configuracion/config.php", 
        "icono" => "edit_diagnostico.png",
        "nombre" => "Configuración"
    ]
];
?>