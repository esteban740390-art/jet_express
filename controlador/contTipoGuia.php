
<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsTipoGuia.php';

$objTipoGuia = new clsTipoGuia();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vista/tipo_guia.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($nombre === '') {
            $_SESSION['mensaje_tipo_guia'] = 'Ingrese el nombre del tipo de guía.';
            header('Location: ../vista/tipo_guia.php');
            exit;
        }

        $resultado = $objTipoGuia->registrar($nombre, $descripcion);

        $_SESSION['mensaje_tipo_guia'] = $resultado
            ? 'Tipo de guía registrado correctamente.'
            : 'No se pudo registrar el tipo de guía.';

        break;

    case 'ACTUALIZAR':

        $id = (int)($_POST['id_tipo_guia'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($id <= 0 || $nombre === '') {
            $_SESSION['mensaje_tipo_guia'] = 'Datos incompletos para actualizar.';
            header('Location: ../vista/tipo_guia.php');
            exit;
        }

        $resultado = $objTipoGuia->actualizar(
            $id,
            $nombre,
            $descripcion
        );

        $_SESSION['mensaje_tipo_guia'] = $resultado
            ? 'Tipo de guía actualizado correctamente.'
            : 'No se pudo actualizar el tipo de guía.';

        break;

    case 'CAMBIAR_ESTADO':

        $id = (int)($_POST['id_tipo_guia'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_tipo_guia'] = 'Datos inválidos para cambiar el estado.';
            header('Location: ../vista/tipo_guia.php');
            exit;
        }

        $resultado = $objTipoGuia->cambiarEstado($id, $estado);

        $_SESSION['mensaje_tipo_guia'] = $resultado
            ? 'Estado del tipo de guía actualizado correctamente.'
            : 'No se pudo cambiar el estado del tipo de guía.';

        break;

    default:

        $_SESSION['mensaje_tipo_guia'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/tipo_guia.php');
exit;
