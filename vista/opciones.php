
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsOpcion.php';

$objOpcion = new clsOpcion();

$opciones = $objOpcion->listar()->fetchAll(PDO::FETCH_ASSOC);

$editar = null;

if (isset($_GET['editar'])) {
    $editar = $objOpcion->buscarPorId((int)$_GET['editar']);
}

$mensaje = $_SESSION['mensaje_opcion'] ?? '';
unset($_SESSION['mensaje_opcion']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones del menú - Jet Express</title>
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <h1>Administración de opciones del menú</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <h2><?php echo $editar ? 'Editar opción' : 'Registrar opción'; ?></h2>

    <form action="../controlador/contOpcion.php" method="POST">

        <input type="hidden" name="accion"
               value="<?php echo $editar ? 'ACTUALIZAR' : 'REGISTRAR'; ?>">

        <?php if ($editar): ?>
            <input type="hidden" name="idopcion"
                   value="<?php echo (int)$editar['idopcion']; ?>">
        <?php endif; ?>

        <label>Descripción:</label>
        <input type="text" name="descripcion" maxlength="100" required
               value="<?php echo htmlspecialchars($editar['descripcion'] ?? ''); ?>">

        <label>Icono:</label>
        <input type="text" name="icono" maxlength="100"
               value="<?php echo htmlspecialchars($editar['icono'] ?? ''); ?>">

        <label>Dirección (URL):</label>
        <input type="text" name="url" maxlength="150"
               value="<?php echo htmlspecialchars($editar['url'] ?? ''); ?>">

        <button type="submit">
            <?php echo $editar ? 'Actualizar opción' : 'Registrar opción'; ?>
        </button>

        <?php if ($editar): ?>
            <a href="opciones.php">Cancelar edición</a>
        <?php endif; ?>

    </form>

    <h2>Opciones registradas</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Icono</th>
                <th>URL</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($opciones as $opcion): ?>
                <tr>
                    <td><?php echo (int)$opcion['idopcion']; ?></td>
                    <td><?php echo htmlspecialchars($opcion['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($opcion['icono'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($opcion['url'] ?? ''); ?></td>
                    <td>
                        <?php echo (int)$opcion['estado'] === 1 ? 'Activo' : 'Inactivo'; ?>
                    </td>
                    <td>
                        <a href="opciones.php?editar=<?php echo (int)$opcion['idopcion']; ?>">
                            Editar
                        </a>

                        <form action="../controlador/contOpcion.php" method="POST">
                            <input type="hidden" name="accion" value="CAMBIAR_ESTADO">
                            <input type="hidden" name="idopcion"
                                   value="<?php echo (int)$opcion['idopcion']; ?>">
                            <input type="hidden" name="estado"
                                   value="<?php echo (int)$opcion['estado'] === 1 ? 0 : 1; ?>">

                            <button type="submit">
                                <?php echo (int)$opcion['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>
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
