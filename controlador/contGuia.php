
<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsGuia.php';

$objGuia = new clsGuia();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vista/guia.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $idCliente = (int)($_POST['id_cliente'] ?? 0);
        $idDestino = (int)($_POST['id_destino'] ?? 0);
        $idTipoGuia = (int)($_POST['id_tipo_guia'] ?? 0);

        $destinatario = trim($_POST['destinatario_nombre'] ?? '');
        $telefono = trim($_POST['destinatario_telefono'] ?? '');
        $direccion = trim($_POST['destinatario_direccion'] ?? '');
        $peso = (float)($_POST['peso'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (
            $idCliente <= 0 ||
            $idDestino <= 0 ||
            $idTipoGuia <= 0 ||
            $destinatario === '' ||
            $peso <= 0
        ) {
            $_SESSION['mensaje_guia'] = 'Complete correctamente los datos obligatorios.';
            header('Location: ../vista/guia.php');
            exit;
        }

        $origen = $objGuia->obtenerOrigenCobija();

        if (!$origen) {
            $_SESSION['mensaje_guia'] = 'No se encontró Cobija, Pando como origen.';
            header('Location: ../vista/guia.php');
            exit;
        }

        $tarifa = $objGuia->buscarTarifa(
            $idDestino,
            $idTipoGuia,
            $peso
        );

        if (!$tarifa) {
            $_SESSION['mensaje_guia'] = 'No existe una tarifa activa para el destino, tipo de guía y peso seleccionados.';
            header('Location: ../vista/guia.php');
            exit;
        }

        $resultado = $objGuia->registrar(
            $idCliente,
            $origen['id_destino'],
            $idDestino,
            $tarifa['id_tarifa'],
            $destinatario,
            $telefono,
            $direccion,
            $peso,
            $descripcion,
            $tarifa['precio']
        );

        if ($resultado['resultado']) {
            $_SESSION['mensaje_guia'] = 'Guía registrada correctamente. Código: ' . $resultado['codigo'];
        } else {
            $_SESSION['mensaje_guia'] = 'No se pudo registrar la guía.';
        }

        break;

    case 'CAMBIAR_ESTADO':

        $id = (int)($_POST['id_guia'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_guia'] = 'Datos inválidos para cambiar el estado.';
            header('Location: ../vista/guia.php');
            exit;
        }

        $resultado = $objGuia->cambiarEstado($id, $estado);

        $_SESSION['mensaje_guia'] = $resultado
            ? 'Estado de la guía actualizado correctamente.'
            : 'No se pudo cambiar el estado de la guía.';

        break;

    default:

        $_SESSION['mensaje_guia'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/guia.php');
exit;

