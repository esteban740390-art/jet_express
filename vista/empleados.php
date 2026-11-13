
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsEmpleado.php';

$objEmpleado = new clsEmpleado();

$empleados = $objEmpleado->listar()->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $objEmpleado->listarUsuariosDisponibles()->fetchAll(PDO::FETCH_ASSOC);

$editar = null;

if (isset($_GET['editar'])) {
    $editar = $objEmpleado->buscarPorId((int)$_GET['editar']);
}

$mensaje = $_SESSION['mensaje_empleado'] ?? '';
unset($_SESSION['mensaje_empleado']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados - Jet Express</title>
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <h1>Administración de empleados</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <h2><?php echo $editar ? 'Editar empleado' : 'Registrar empleado'; ?></h2>

    <form action="../controlador/contEmpleado.php" method="POST">

        <input type="hidden" name="accion"
               value="<?php echo $editar ? 'ACTUALIZAR' : 'REGISTRAR'; ?>">

        <?php if ($editar): ?>
            <input type="hidden" name="id_empleado"
                   value="<?php echo (int)$editar['id_empleado']; ?>">
        <?php else: ?>

            <label>Usuario:</label>
            <select name="idusuario" required>
                <option value="">Seleccione un usuario</option>

                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?php echo (int)$usuario['idusuario']; ?>">
                        <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                    </option>
                <?php endforeach; ?>

            </select>

        <?php endif; ?>

        <label>Teléfono:</label>
        <input type="text" name="telefono" maxlength="20"
               value="<?php echo htmlspecialchars($editar['telefono'] ?? ''); ?>">

        <label>Dirección:</label>
        <input type="text" name="direccion" maxlength="200"
               value="<?php echo htmlspecialchars($editar['direccion'] ?? ''); ?>">

        <label>Cargo:</label>
        <input type="text" name="cargo" maxlength="80"
               value="<?php echo htmlspecialchars($editar['cargo'] ?? ''); ?>">

        <button type="submit">
            <?php echo $editar ? 'Actualizar empleado' : 'Registrar empleado'; ?>
        </button>

        <?php if ($editar): ?>
            <a href="empleados.php">Cancelar edición</a>
        <?php endif; ?>

    </form>

    <h2>Empleados registrados</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Usuario</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($empleados as $empleado): ?>
                <tr>
                    <td><?php echo (int)$empleado['id_empleado']; ?></td>
                    <td><?php echo htmlspecialchars($empleado['nombre_completo']); ?></td>
                    <td><?php echo htmlspecialchars($empleado['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($empleado['telefono'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($empleado['direccion'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($empleado['cargo'] ?? ''); ?></td>
                    <td>
                        <?php echo (int)$empleado['estado'] === 1 ? 'Activo' : 'Inactivo'; ?>
                    </td>
                    <td>
                        <a href="empleados.php?editar=<?php echo (int)$empleado['id_empleado']; ?>">
                            Editar
                        </a>

                        <form action="../controlador/contEmpleado.php" method="POST">
                            <input type="hidden" name="accion" value="CAMBIAR_ESTADO">
                            <input type="hidden" name="id_empleado"
                                   value="<?php echo (int)$empleado['id_empleado']; ?>">
                            <input type="hidden" name="estado"
                                   value="<?php echo (int)$empleado['estado'] === 1 ? 0 : 1; ?>">

                            <button type="submit">
                                <?php echo (int)$empleado['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>
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
