<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsAcceso.php';

$objAcceso = new clsAcceso();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $idPerfil = (int)($_POST['idperfil'] ?? 0);
        $idOpcion = (int)($_POST['idopcion'] ?? 0);

        if ($idPerfil <= 0 || $idOpcion <= 0) {
            $_SESSION['mensaje_acceso'] = 'Seleccione un perfil y una opción válidos.';
            break;
        }

        if ($objAcceso->existeAcceso($idPerfil, $idOpcion)) {
            $_SESSION['mensaje_acceso'] = 'Ese permiso ya está registrado.';
            break;
        }

        $objAcceso->registrar($idPerfil, $idOpcion);

        $_SESSION['mensaje_acceso'] = 'Permiso asignado correctamente.';
        break;


    case 'CAMBIAR_ESTADO':

        $idPerfil = (int)($_POST['idperfil'] ?? 0);
        $idOpcion = (int)($_POST['idopcion'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if (
            $idPerfil <= 0 ||
            $idOpcion <= 0 ||
            !in_array($estado, [0, 1], true)
        ) {
            $_SESSION['mensaje_acceso'] = 'Los datos del permiso no son válidos.';
            break;
        }

        $objAcceso->cambiarEstado($idPerfil, $idOpcion, $estado);

        $_SESSION['mensaje_acceso'] = 'Permiso actualizado correctamente.';
        break;


    default:

        $_SESSION['mensaje_acceso'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/permisos.php');
exit;