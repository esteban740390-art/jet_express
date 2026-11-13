
<?php

session_start();

require_once __DIR__ . '/../modelo/clsUsuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vista/login.php');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if ($usuario === '' || $password === '') {
    $_SESSION['mensaje_login'] = 'Ingrese su usuario y contraseña.';
    header('Location: ../vista/login.php');
    exit;
}

$objUsuario = new clsUsuario();
$datosUsuario = $objUsuario->buscarPorUsuario($usuario);

if (
    $datosUsuario &&
    (int)$datosUsuario['estado'] === 1 &&
    password_verify($password, $datosUsuario['password'])
) {
    session_regenerate_id(true);

    $_SESSION['idusuario'] = $datosUsuario['idusuario'];
    $_SESSION['idperfil'] = $datosUsuario['idperfil'];
    $_SESSION['nombre_completo'] = $datosUsuario['nombre_completo'];
    $_SESSION['usuario'] = $datosUsuario['usuario'];
    $_SESSION['perfil'] = $datosUsuario['perfil'];

    header('Location: ../index.php');
    exit;
}

$_SESSION['mensaje_login'] = 'Usuario o contraseña incorrectos, o cuenta inactiva.';
header('Location: ../vista/login.php');
exit;
