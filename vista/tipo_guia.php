
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsTipoGuia.php';

$objTipoGuia = new clsTipoGuia();
$listaTipos = $objTipoGuia->listar();

$tipoEditar = null;

if (isset($_GET['editar'])) {
    $idEditar = (int)$_GET['editar'];
    $tipoEditar = $objTipoGuia->buscarPorId($idEditar);
}

$mensaje = $_SESSION['mensaje_tipo_guia'] ?? '';
unset($_SESSION['mensaje_tipo_guia']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipo de Guía | Jet Express</title>
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <header>
        <h1>Tipo de Guía</h1>
        <p>Administración de tipos de envío</p>
    </header>

    <?php if ($mensaje !== ''): ?>
        <p class="mensaje">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php endif; ?>

    <section>
        <h2>Registrar tipo de guía</h2>

        <form action="../controlador/contTipoGuia.php" method="POST">

            <input type="hidden" name="accion" value="REGISTRAR">

            <label>Nombre:</label>
            <input type="text" name="nombre" maxlength="50" required>

            <label>Descripción:</label>
            <textarea name="descripcion" maxlength="200"></textarea>

            <button type="submit">Registrar</button>

        </form>
    </section>

    <?php if ($tipoEditar): ?>

        <section>
            <h2>Editar tipo de guía</h2>

            <form action="../controlador/contTipoGuia.php" method="POST">

                <input type="hidden" name="accion" value="ACTUALIZAR">

                <input type="hidden" name="id_tipo_guia"
                       value="<?= (int)$tipoEditar['id_tipo_guia'] ?>">

                <label>Nombre:</label>
                <input type="text" name="nombre"
                       maxlength="50"
                       value="<?= htmlspecialchars($tipoEditar['nombre']) ?>"
                       required>

                <label>Descripción:</label>
                <textarea name="descripcion"
                          maxlength="200"><?= htmlspecialchars($tipoEditar['descripcion'] ?? '') ?></textarea>

                <button type="submit">Guardar cambios</button>
                <a href="tipo_guia.php">Cancelar</a>

            </form>
        </section>

    <?php endif; ?>

    <section>
        <h2>Listado de tipos de guía</h2>

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

                <?php while ($tipo = $listaTipos->fetch()): ?>

                    <tr>
                        <td><?= (int)$tipo['id_tipo_guia'] ?></td>

                        <td><?= htmlspecialchars($tipo['nombre']) ?></td>

                        <td><?= htmlspecialchars($tipo['descripcion'] ?? '') ?></td>

                        <td>
                            <?= (int)$tipo['estado'] === 1 ? 'Activo' : 'Inactivo' ?>
                        </td>

                        <td>
                            <a href="tipo_guia.php?editar=<?= (int)$tipo['id_tipo_guia'] ?>">
                                Editar
                            </a>

                            <form action="../controlador/contTipoGuia.php"
                                  method="POST"
                                  style="display:inline;">

                                <input type="hidden" name="accion"
                                       value="CAMBIAR_ESTADO">

                                <input type="hidden" name="id_tipo_guia"
                                       value="<?= (int)$tipo['id_tipo_guia'] ?>">

                                <input type="hidden" name="estado"
                                       value="<?= (int)$tipo['estado'] === 1 ? 0 : 1 ?>">

                                <button type="submit">
                                    <?= (int)$tipo['estado'] === 1
                                        ? 'Desactivar'
                                        : 'Activar' ?>
                                </button>

                            </form>
                        </td>
                    </tr>

                <?php endwhile; ?>

            </tbody>
        </table>
    </section>

    <p><a href="../index.php">Volver al panel principal</a></p>

</div>

</body>
</html>
