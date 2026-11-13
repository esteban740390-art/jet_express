
<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsDestino.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vista/tarifa_destino.php');
    exit;
}

$objDestino = new clsDestino();

$accion = $_POST['accion'] ?? '';

$nombre = trim($_POST['nombre'] ?? '');
$idDepartamento = (int)($_POST['id_departamento'] ?? 0);
$descripcion = trim($_POST['descripcion'] ?? '');

$id = (int)($_POST['id_destino'] ?? 0);

switch ($accion) {

    case 'REGISTRAR':

        if ($nombre === '' || $idDepartamento <= 0) {
            $_SESSION['mensaje_destino'] = 'Complete los datos obligatorios del destino.';
            break;
        }

        if ($objDestino->existeDestino($nombre, $idDepartamento)) {
            $_SESSION['mensaje_destino'] = 'Este destino ya está registrado en ese departamento.';
            break;
        }

        if ($objDestino->registrar($nombre, $idDepartamento, $descripcion)) {
            $_SESSION['mensaje_destino'] = 'Destino registrado correctamente.';
        } else {
            $_SESSION['mensaje_destino'] = 'No se pudo registrar el destino.';
        }

        break;

    case 'ACTUALIZAR':

        if ($id <= 0 || $nombre === '' || $idDepartamento <= 0) {
            $_SESSION['mensaje_destino'] = 'Los datos para actualizar el destino no son válidos.';
            break;
        }

        if ($objDestino->existeDestino($nombre, $idDepartamento, $id)) {
            $_SESSION['mensaje_destino'] = 'Ya existe ese destino en el departamento seleccionado.';
            break;
        }

        if ($objDestino->actualizar($id, $nombre, $idDepartamento, $descripcion)) {
            $_SESSION['mensaje_destino'] = 'Destino actualizado correctamente.';
        } else {
            $_SESSION['mensaje_destino'] = 'No se pudo actualizar el destino.';
        }

        break;

    case 'CAMBIAR_ESTADO':

        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_destino'] = 'Los datos para cambiar el estado no son válidos.';
            break;
        }

        if ($objDestino->cambiarEstado($id, $estado)) {
            $_SESSION['mensaje_destino'] = 'Estado del destino actualizado correctamente.';
        } else {
            $_SESSION['mensaje_destino'] = 'No se pudo cambiar el estado del destino.';
        }

        break;

    default:
        $_SESSION['mensaje_destino'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/tarifa_destino.php');
exit;
