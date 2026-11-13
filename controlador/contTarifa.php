
<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsTarifa.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vista/tarifa_destino.php');
    exit;
}

$objTarifa = new clsTarifa();

$accion = $_POST['accion'] ?? '';

$id = (int)($_POST['id_tarifa'] ?? 0);
$idDestino = (int)($_POST['id_destino'] ?? 0);
$idTipoGuia = (int)($_POST['id_tipo_guia'] ?? 0);

$pesoMinimo = (float)($_POST['peso_minimo'] ?? 0);
$pesoMaximo = (float)($_POST['peso_maximo'] ?? 0);
$precio = (float)($_POST['precio'] ?? 0);

$descripcion = trim($_POST['descripcion'] ?? '');

switch ($accion) {

    case 'REGISTRAR':

        if (
            $idDestino <= 0 ||
            $idTipoGuia <= 0 ||
            $pesoMinimo < 0 ||
            $pesoMaximo <= $pesoMinimo ||
            $precio <= 0
        ) {
            $_SESSION['mensaje_tarifa'] = 'Verifique los datos de la tarifa. El peso máximo debe ser mayor al mínimo y el precio debe ser positivo.';
            break;
        }

        if (
            $objTarifa->existeRango(
                $idDestino,
                $idTipoGuia,
                $pesoMinimo,
                $pesoMaximo
            )
        ) {
            $_SESSION['mensaje_tarifa'] = 'Ya existe una tarifa con un rango de peso que se superpone para ese destino y tipo de guía.';
            break;
        }

        if (
            $objTarifa->registrar(
                $idDestino,
                $idTipoGuia,
                $pesoMinimo,
                $pesoMaximo,
                $precio,
                $descripcion
            )
        ) {
            $_SESSION['mensaje_tarifa'] = 'Tarifa registrada correctamente.';
        } else {
            $_SESSION['mensaje_tarifa'] = 'No se pudo registrar la tarifa.';
        }

        break;

    case 'ACTUALIZAR':

        if (
            $id <= 0 ||
            $idDestino <= 0 ||
            $idTipoGuia <= 0 ||
            $pesoMinimo < 0 ||
            $pesoMaximo <= $pesoMinimo ||
            $precio <= 0
        ) {
            $_SESSION['mensaje_tarifa'] = 'Los datos para actualizar la tarifa no son válidos.';
            break;
        }

        if (
            $objTarifa->existeRango(
                $idDestino,
                $idTipoGuia,
                $pesoMinimo,
                $pesoMaximo,
                $id
            )
        ) {
            $_SESSION['mensaje_tarifa'] = 'El rango de peso se superpone con otra tarifa existente.';
            break;
        }

        if (
            $objTarifa->actualizar(
                $id,
                $idDestino,
                $idTipoGuia,
                $pesoMinimo,
                $pesoMaximo,
                $precio,
                $descripcion
            )
        ) {
            $_SESSION['mensaje_tarifa'] = 'Tarifa actualizada correctamente.';
        } else {
            $_SESSION['mensaje_tarifa'] = 'No se pudo actualizar la tarifa.';
        }

        break;

    case 'CAMBIAR_ESTADO':

        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_tarifa'] = 'Los datos para cambiar el estado no son válidos.';
            break;
        }

        if ($objTarifa->cambiarEstado($id, $estado)) {
            $_SESSION['mensaje_tarifa'] = 'Estado de la tarifa actualizado correctamente.';
        } else {
            $_SESSION['mensaje_tarifa'] = 'No se pudo cambiar el estado de la tarifa.';
        }

        break;

    default:
        $_SESSION['mensaje_tarifa'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/tarifa_destino.php');
exit;
