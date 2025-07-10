<?php
// sidebar.php
require_once 'config_permisos.php';
?>

<nav id="sidebar">
    <div class="sidebar-header">
        <h3>Talleres UNEVE</h3>
    </div>
    <ul class="list-unstyled components">
        <?php
        // Generar los elementos de la sidebar
        foreach ($modulos_permitidos as $modulo) {
            if (isset($modulos_con_rutas[$modulo])) {
                $ruta = $modulos_con_rutas[$modulo]["ruta"];
                $icono = $modulos_con_rutas[$modulo]["icono"];
                $nombre = $modulos_con_rutas[$modulo]["nombre"];
                echo '
                <li>
                    <a href="' . $ruta . '">
                        <img src="../../img/' . $icono . '" alt="' . $nombre . '" class="icono_sidebar">
                        <span class="sidebar-text">' . $nombre . '</span>
                    </a>
                </li>';
            }
        }
        ?>
    </ul>
</nav>