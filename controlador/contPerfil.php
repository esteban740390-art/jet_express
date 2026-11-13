<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsPerfil.php';

$objPerfil = new clsPerfil();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($nombre === '') {
            $_SESSION['mensaje_perfil'] = 'Ingrese el nombre del perfil.';
            break;
        }

        $objPerfil->registrar($nombre, $descripcion);

        $_SESSION['mensaje_perfil'] = 'Perfil registrado correctamente.';
        break;


    case 'ACTUALIZAR':

        $id = (int)($_POST['idperfil'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($id <= 0 || $nombre === '') {
            $_SESSION['mensaje_perfil'] = 'Complete correctamente los datos del perfil.';
            break;
        }

        $objPerfil->actualizar($id, $nombre, $descripcion);

        $_SESSION['mensaje_perfil'] = 'Perfil actualizado correctamente.';
        break;


    case 'CAMBIAR_ESTADO':

        $id = (int)($_POST['idperfil'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_perfil'] = 'Datos incorrectos para cambiar el estado.';
            break;
        }

        // Evitar desactivar el perfil que utiliza la cuenta actual.
        if (
            $id === (int)$_SESSION['idperfil'] &&
            $estado === 0
        ) {
            $_SESSION['mensaje_perfil'] = 'No puede desactivar el perfil de su propia cuenta.';
            break;
        }

        $objPerfil->cambiarEstado($id, $estado);

        $_SESSION['mensaje_perfil'] = 'Estado del perfil actualizado correctamente.';
        break;


    default:

        $_SESSION['mensaje_perfil'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/perfiles.php');
exit;