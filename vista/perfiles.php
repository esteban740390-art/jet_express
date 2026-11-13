
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsPerfil.php';

$objPerfil = new clsPerfil();

$perfiles = $objPerfil->listar()->fetchAll(PDO::FETCH_ASSOC);

$editar = null;

if (isset($_GET['editar'])) {
    $editar = $objPerfil->buscarPorId((int)$_GET['editar']);
}

$mensaje = $_SESSION['mensaje_perfil'] ?? '';
unset($_SESSION['mensaje_perfil']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfiles - Jet Express</title>
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <h1>Administración de perfiles</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <h2><?php echo $editar ? 'Editar perfil' : 'Registrar perfil'; ?></h2>

    <form action="../controlador/contPerfil.php" method="POST">

        <input type="hidden" name="accion"
               value="<?php echo $editar ? 'ACTUALIZAR' : 'REGISTRAR'; ?>">

        <?php if ($editar): ?>
            <input type="hidden" name="idperfil"
                   value="<?php echo (int)$editar['idperfil']; ?>">
        <?php endif; ?>

        <label>Nombre del perfil:</label>
        <input type="text" name="nombre" maxlength="50" required
               value="<?php echo htmlspecialchars($editar['nombre'] ?? ''); ?>">

        <label>Descripción:</label>
        <input type="text" name="descripcion" maxlength="200"
               value="<?php echo htmlspecialchars($editar['descripcion'] ?? ''); ?>">

        <button type="submit">
            <?php echo $editar ? 'Actualizar perfil' : 'Registrar perfil'; ?>
        </button>

        <?php if ($editar): ?>
            <a href="perfiles.php">Cancelar edición</a>
        <?php endif; ?>

    </form>

    <h2>Perfiles registrados</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($perfiles as $perfil): ?>
                <tr>
                    <td><?php echo (int)$perfil['idperfil']; ?></td>
                    <td><?php echo htmlspecialchars($perfil['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($perfil['descripcion'] ?? ''); ?></td>
                    <td>
                        <?php echo (int)$perfil['estado'] === 1 ? 'Activo' : 'Inactivo'; ?>
                    </td>
                    <td>
                        <a href="perfiles.php?editar=<?php echo (int)$perfil['idperfil']; ?>">
                            Editar
                        </a>

                        <form action="../controlador/contPerfil.php" method="POST">
                            <input type="hidden" name="accion" value="CAMBIAR_ESTADO">
                            <input type="hidden" name="idperfil"
                                   value="<?php echo (int)$perfil['idperfil']; ?>">
                            <input type="hidden" name="estado"
                                   value="<?php echo (int)$perfil['estado'] === 1 ? 0 : 1; ?>">

                            <button type="submit">
                                <?php echo (int)$perfil['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>
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
