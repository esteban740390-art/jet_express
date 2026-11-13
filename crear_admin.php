
<?php

require_once __DIR__ . '/config/conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre_completo'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre === '' || $usuario === '' || $password === '') {
        $mensaje = 'Todos los campos son obligatorios.';
    } elseif (strlen($password) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
    } else {

        try {
            $objConexion = new Conexion();
            $conexion = $objConexion->conectar();

            $conexion->beginTransaction();

            $consulta = $conexion->prepare(
                "SELECT idperfil FROM perfiles
                 WHERE nombre = 'Administrador'
                 LIMIT 1"
            );
            $consulta->execute();

            $perfil = $consulta->fetch();

            if (!$perfil) {
                $consulta = $conexion->prepare(
                    "INSERT INTO perfiles (nombre, descripcion)
                     VALUES ('Administrador', 'Acceso completo al sistema')"
                );
                $consulta->execute();

                $idPerfil = $conexion->lastInsertId();
            } else {
                $idPerfil = $perfil['idperfil'];
            }

            $consulta = $conexion->prepare(
                "SELECT idusuario FROM usuarios
                 WHERE idperfil = ?
                 LIMIT 1"
            );
            $consulta->execute([$idPerfil]);

            if ($consulta->fetch()) {
                throw new Exception(
                    'Ya existe un usuario administrador. No se puede crear otro desde este archivo.'
                );
            }

            $consulta = $conexion->prepare(
                "SELECT idusuario FROM usuarios
                 WHERE usuario = ?
                 LIMIT 1"
            );
            $consulta->execute([$usuario]);

            if ($consulta->fetch()) {
                throw new Exception('Ese nombre de usuario ya está registrado.');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $consulta = $conexion->prepare(
                "INSERT INTO usuarios
                (idperfil, nombre_completo, usuario, password)
                VALUES (?, ?, ?, ?)"
            );

            $consulta->execute([
                $idPerfil,
                $nombre,
                $usuario,
                $passwordHash
            ]);

            $conexion->commit();

            $mensaje = '¡Usuario administrador creado correctamente!';

        } catch (Exception $e) {

            if (isset($conexion) && $conexion->inTransaction()) {
                $conexion->rollBack();
            }

            $mensaje = 'No se pudo crear el administrador: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear administrador | Jet Express</title>
</head>
<body>

    <h2>Crear usuario administrador</h2>

    <?php if ($mensaje !== ''): ?>
        <p><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Nombre completo:</label><br>
        <input type="text" name="nombre_completo" required>
        <br><br>

        <label>Nombre de usuario:</label><br>
        <input type="text" name="usuario" required>
        <br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" minlength="8" required>
        <br><br>

        <button type="submit">Crear administrador</button>

    </form>

</body>
</html>
