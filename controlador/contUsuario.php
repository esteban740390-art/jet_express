<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsUsuario.php';

$objUsuario = new clsUsuario();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'REGISTRAR':

        $idPerfil = (int)($_POST['idperfil'] ?? 0);
        $nombre = trim($_POST['nombre_completo'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($idPerfil <= 0 || $nombre === '' || $usuario === '' || $password === '') {
            $_SESSION['mensaje_usuario'] = 'Complete todos los campos obligatorios.';
            break;
        }

        if ($objUsuario->existeUsuario($usuario)) {
            $_SESSION['mensaje_usuario'] = 'Ese nombre de usuario ya está registrado.';
            break;
        }

        $objUsuario->registrar($idPerfil, $nombre, $usuario, $password);

        $_SESSION['mensaje_usuario'] = 'Usuario registrado correctamente.';
        break;


    case 'ACTUALIZAR':

        $id = (int)($_POST['idusuario'] ?? 0);
        $idPerfil = (int)($_POST['idperfil'] ?? 0);
        $nombre = trim($_POST['nombre_completo'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');

        if ($id <= 0 || $idPerfil <= 0 || $nombre === '' || $usuario === '') {
            $_SESSION['mensaje_usuario'] = 'Complete correctamente los datos del usuario.';
            break;
        }

        if ($objUsuario->existeUsuario($usuario, $id)) {
            $_SESSION['mensaje_usuario'] = 'Ese nombre de usuario ya está registrado.';
            break;
        }

        $objUsuario->actualizar($id, $idPerfil, $nombre, $usuario);

        $_SESSION['mensaje_usuario'] = 'Usuario actualizado correctamente.';
        break;


    case 'CAMBIAR_PASSWORD':

        $id = (int)($_POST['idusuario'] ?? 0);
        $password = $_POST['password'] ?? '';

        if ($id <= 0 || $password === '') {
            $_SESSION['mensaje_usuario'] = 'Ingrese una contraseña válida.';
            break;
        }

        $objUsuario->cambiarPassword($id, $password);

        $_SESSION['mensaje_usuario'] = 'Contraseña actualizada correctamente.';
        break;


    case 'CAMBIAR_ESTADO':

        $id = (int)($_POST['idusuario'] ?? 0);
        $estado = (int)($_POST['estado'] ?? -1);

        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['mensaje_usuario'] = 'Datos incorrectos para cambiar el estado.';
            break;
        }

        // Evitar desactivar la cuenta que está utilizando el sistema.
        if ($id === (int)$_SESSION['idusuario'] && $estado === 0) {
            $_SESSION['mensaje_usuario'] = 'No puede desactivar su propia cuenta.';
            break;
        }

        $objUsuario->cambiarEstado($id, $estado);

        $_SESSION['mensaje_usuario'] = 'Estado del usuario actualizado correctamente.';
        break;


    default:

        $_SESSION['mensaje_usuario'] = 'La acción solicitada no es válida.';
        break;
}

header('Location: ../vista/usuarios.php');
exit;