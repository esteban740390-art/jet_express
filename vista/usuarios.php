
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsUsuario.php';

$objUsuario = new clsUsuario();

$usuarios = $objUsuario->listar()->fetchAll(PDO::FETCH_ASSOC);
$perfiles = $objUsuario->listarPerfilesActivos()->fetchAll(PDO::FETCH_ASSOC);

$editar = null;

if (isset($_GET['editar'])) {
    $editar = $objUsuario->buscarPorId((int)$_GET['editar']);
}

$mensaje = $_SESSION['mensaje_usuario'] ?? '';
unset($_SESSION['mensaje_usuario']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Jet Express</title>
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <h1>Administración de usuarios</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <h2><?php echo $editar ? 'Editar usuario' : 'Registrar usuario'; ?></h2>

    <form action="../controlador/contUsuario.php" method="POST">

        <input type="hidden" name="accion"
               value="<?php echo $editar ? 'ACTUALIZAR' : 'REGISTRAR'; ?>">

        <?php if ($editar): ?>
            <input type="hidden" name="idusuario"
                   value="<?php echo (int)$editar['idusuario']; ?>">
        <?php endif; ?>

        <label>Nombre completo:</label>
        <input type="text" name="nombre_completo"
               maxlength="100" required
               value="<?php echo htmlspecialchars($editar['nombre_completo'] ?? ''); ?>">

        <label>Nombre de usuario:</label>
        <input type="text" name="usuario"
               maxlength="50" required
               value="<?php echo htmlspecialchars($editar['usuario'] ?? ''); ?>">

        <label>Perfil:</label>
        <select name="idperfil" required>
            <option value="">Seleccione un perfil</option>

            <?php foreach ($perfiles as $perfil): ?>
                <option value="<?php echo (int)$perfil['idperfil']; ?>"
                    <?php echo ($editar && (int)$editar['idperfil'] === (int)$perfil['idperfil']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($perfil['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php if (!$editar): ?>
            <label>Contraseña:</label>
            <input type="password" name="password" required minlength="6">
        <?php endif; ?>

        <button type="submit">
            <?php echo $editar ? 'Actualizar usuario' : 'Registrar usuario'; ?>
        </button>

        <?php if ($editar): ?>
            <a href="usuarios.php">Cancelar edición</a>
        <?php endif; ?>

    </form>

    <h2>Cambiar contraseña</h2>

    <?php if ($editar): ?>
        <form action="../controlador/contUsuario.php" method="POST">

            <input type="hidden" name="accion" value="CAMBIAR_PASSWORD">
            <input type="hidden" name="idusuario"
                   value="<?php echo (int)$editar['idusuario']; ?>">

            <label>Nueva contraseña:</label>
            <input type="password" name="password" required minlength="6">

            <button type="submit">Actualizar contraseña</button>

        </form>
    <?php else: ?>
        <p>Para cambiar una contraseña, primero seleccione un usuario en la lista.</p>
    <?php endif; ?>

    <h2>Usuarios registrados</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Usuario</th>
                <th>Perfil</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo (int)$usuario['idusuario']; ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['perfil']); ?></td>
                    <td>
                        <?php echo (int)$usuario['estado'] === 1 ? 'Activo' : 'Inactivo'; ?>
                    </td>
                    <td>
                        <a href="usuarios.php?editar=<?php echo (int)$usuario['idusuario']; ?>">
                            Editar
                        </a>

                        <form action="../controlador/contUsuario.php" method="POST">
                            <input type="hidden" name="accion" value="CAMBIAR_ESTADO">
                            <input type="hidden" name="idusuario"
                                   value="<?php echo (int)$usuario['idusuario']; ?>">
                            <input type="hidden" name="estado"
                                   value="<?php echo (int)$usuario['estado'] === 1 ? 0 : 1; ?>">

                            <button type="submit">
                                <?php echo (int)$usuario['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="../index.php">Volver al inicio</a></p>

</div>

</body>
</html>
