<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsEmpleado.php';

$objEmpleado = new clsEmpleado();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $idUsuario = (int)($_POST['idusuario'] ?? 0);
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');

        if ($idUsuario <= 0) {
            $_SESSION['mensaje_empleado'] = 'Seleccione un usuario válido.';
            break;
        }

        $objEmpleado->registrar(
            $idUsuario,
            $telefono,
            $direccion,
            $cargo
        );

        $_SESSION['mensaje_empleado'] = 'Empleado registrado correctamente.';
        break;


    case 'ACTUALIZAR':

        $id = (int)($_POST['id_empleado'] ?? 0);
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');

        if ($id <= 0) {
            $_SESSION['mensaje_empleado'] = 'Seleccione un empleado válido.';
            break;
        }

        $objEmpleado->actualizar(
            $id,
            $telefono,
            $direccion,
            $cargo
        );

        $_SESSION['mensaje_empleado'] = 'Empleado actualizado correctamente.';
        break;


    case 'CAMBIAR_ESTADO':

        $id = (int)($_POST['id_empleado'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_empleado'] = 'Datos incorrectos para cambiar el estado.';
            break;
        }

        $objEmpleado->cambiarEstado($id, $estado);

        $_SESSION['mensaje_empleado'] = 'Estado del empleado actualizado correctamente.';
        break;


    default:

        $_SESSION['mensaje_empleado'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/empleados.php');
exit;