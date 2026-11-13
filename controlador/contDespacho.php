
<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsDespacho.php';

$objDespacho = new clsDespacho();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vista/despacho.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $idEmpleado = (int)($_POST['id_empleado'] ?? 0);
        $observaciones = trim($_POST['observaciones'] ?? '');

        $guias = $_POST['guias'] ?? [];

        if (!is_array($guias)) {
            $guias = [];
        }

        $guias = array_values(array_unique($guias));

        if ($idEmpleado <= 0) {
            $_SESSION['mensaje_despacho'] = 'Seleccione un empleado responsable.';
            header('Location: ../vista/despacho.php');
            exit;
        }

        if (count($guias) === 0) {
            $_SESSION['mensaje_despacho'] = 'Seleccione al menos una guía para el despacho.';
            header('Location: ../vista/despacho.php');
            exit;
        }

        if (strlen($observaciones) > 250) {
            $_SESSION['mensaje_despacho'] = 'Las observaciones no pueden superar los 250 caracteres.';
            header('Location: ../vista/despacho.php');
            exit;
        }

        $resultado = $objDespacho->registrar(
            $idEmpleado,
            $observaciones,
            $guias
        );

        if ($resultado['resultado']) {

            $_SESSION['mensaje_despacho'] =
                'Despacho registrado correctamente. Código: ' .
                $resultado['codigo'];

        } else {

            $_SESSION['mensaje_despacho'] =
                $resultado['error'] ??
                'No se pudo registrar el despacho.';
        }

        break;

    case 'CAMBIAR_ESTADO':

        $id = (int)($_POST['id_despacho'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_despacho'] = 'Datos inválidos para cambiar el estado.';
            header('Location: ../vista/despacho.php');
            exit;
        }

        $resultado = $objDespacho->cambiarEstado($id, $estado);

        $_SESSION['mensaje_despacho'] = $resultado
            ? 'Estado del despacho actualizado correctamente.'
            : 'No se pudo cambiar el estado del despacho.';

        break;

    default:

        $_SESSION['mensaje_despacho'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/despacho.php');
exit;
