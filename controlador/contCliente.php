
<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsCliente.php';

$objCliente = new clsCliente();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vista/cliente.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $nombre = trim($_POST['nombre'] ?? '');
        $tipoDocumento = trim($_POST['tipo_documento'] ?? '');
        $numeroDocumento = trim($_POST['numero_documento'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        if ($nombre === '' || $tipoDocumento === '' || $numeroDocumento === '') {
            $_SESSION['mensaje_cliente'] = 'Complete los campos obligatorios.';
            break;
        }

        if ($objCliente->existeDocumento($tipoDocumento, $numeroDocumento)) {
            $_SESSION['mensaje_cliente'] = 'Ya existe un cliente con ese tipo y número de documento.';
            break;
        }

        $resultado = $objCliente->registrar(
            $nombre,
            $tipoDocumento,
            $numeroDocumento,
            $telefono,
            $direccion
        );

        $_SESSION['mensaje_cliente'] = $resultado
            ? 'Cliente registrado correctamente.'
            : 'No se pudo registrar el cliente.';

        break;

    case 'ACTUALIZAR':

        $id = (int)($_POST['id_cliente'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $tipoDocumento = trim($_POST['tipo_documento'] ?? '');
        $numeroDocumento = trim($_POST['numero_documento'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        if ($id <= 0 || $nombre === '' || $tipoDocumento === '' || $numeroDocumento === '') {
            $_SESSION['mensaje_cliente'] = 'Complete los campos obligatorios para actualizar.';
            break;
        }

        if (!$objCliente->buscarPorId($id)) {
            $_SESSION['mensaje_cliente'] = 'El cliente seleccionado no existe.';
            break;
        }

        if ($objCliente->existeDocumento($tipoDocumento, $numeroDocumento, $id)) {
            $_SESSION['mensaje_cliente'] = 'Otro cliente ya tiene ese tipo y número de documento.';
            break;
        }

        $resultado = $objCliente->actualizar(
            $id,
            $nombre,
            $tipoDocumento,
            $numeroDocumento,
            $telefono,
            $direccion
        );

        $_SESSION['mensaje_cliente'] = $resultado
            ? 'Cliente actualizado correctamente.'
            : 'No se pudo actualizar el cliente.';

        break;

    case 'CAMBIAR_ESTADO':

        $id = (int)($_POST['id_cliente'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_cliente'] = 'Datos inválidos para cambiar el estado.';
            break;
        }

        if (!$objCliente->buscarPorId($id)) {
            $_SESSION['mensaje_cliente'] = 'El cliente seleccionado no existe.';
            break;
        }

        $resultado = $objCliente->cambiarEstado($id, $estado);

        $_SESSION['mensaje_cliente'] = $resultado
            ? 'Estado del cliente actualizado correctamente.'
            : 'No se pudo cambiar el estado del cliente.';

        break;

    default:

        $_SESSION['mensaje_cliente'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/cliente.php');
exit;
