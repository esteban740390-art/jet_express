<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsOpcion.php';

$objOpcion = new clsOpcion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $descripcion = trim($_POST['descripcion'] ?? '');
        $icono = trim($_POST['icono'] ?? '');
        $url = trim($_POST['url'] ?? '');

        if ($descripcion === '') {
            $_SESSION['mensaje_opcion'] = 'Ingrese la descripción de la opción.';
            break;
        }

        $objOpcion->registrar($descripcion, $icono, $url);

        $_SESSION['mensaje_opcion'] = 'Opción registrada correctamente.';
        break;


    case 'ACTUALIZAR':

        $id = (int)($_POST['idopcion'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $icono = trim($_POST['icono'] ?? '');
        $url = trim($_POST['url'] ?? '');

        if ($id <= 0 || $descripcion === '') {
            $_SESSION['mensaje_opcion'] = 'Complete correctamente los datos de la opción.';
            break;
        }

        $objOpcion->actualizar($id, $descripcion, $icono, $url);

        $_SESSION['mensaje_opcion'] = 'Opción actualizada correctamente.';
        break;


    case 'CAMBIAR_ESTADO':

        $id = (int)($_POST['idopcion'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_opcion'] = 'Datos incorrectos para cambiar el estado.';
            break;
        }

        $objOpcion->cambiarEstado($id, $estado);

        $_SESSION['mensaje_opcion'] = 'Estado de la opción actualizado correctamente.';
        break;


    default:

        $_SESSION['mensaje_opcion'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/opciones.php');
exit;