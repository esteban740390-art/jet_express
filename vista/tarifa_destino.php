
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsDestino.php';
require_once __DIR__ . '/../modelo/clsTarifa.php';

$objDestino = new clsDestino();
$objTarifa = new clsTarifa();

$buscarDestino = trim($_GET['buscar_destino'] ?? '');
$buscarTarifa = trim($_GET['buscar_tarifa'] ?? '');

$destinos = $objDestino->listar($buscarDestino)->fetchAll(PDO::FETCH_ASSOC);
$tarifas = $objTarifa->listar($buscarTarifa)->fetchAll(PDO::FETCH_ASSOC);

$departamentos = $objDestino->listarDepartamentosActivos()->fetchAll(PDO::FETCH_ASSOC);
$destinosActivos = $objTarifa->listarDestinosActivos()->fetchAll(PDO::FETCH_ASSOC);
$tiposGuia = $objTarifa->listarTiposGuiaActivos()->fetchAll(PDO::FETCH_ASSOC);

$editarDestino = null;
$editarTarifa = null;

if (isset($_GET['editar_destino'])) {
    $editarDestino = $objDestino->buscarPorId((int)$_GET['editar_destino']);
}

if (isset($_GET['editar_tarifa'])) {
    $editarTarifa = $objTarifa->buscarPorId((int)$_GET['editar_tarifa']);
}

$mensajeDestino = $_SESSION['mensaje_destino'] ?? '';
$mensajeTarifa = $_SESSION['mensaje_tarifa'] ?? '';

unset($_SESSION['mensaje_destino'], $_SESSION['mensaje_tarifa']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarifas y Destinos - Jet Express</title>
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor-principal">

    <h1>Tarifas y Destinos</h1>

    <p>Administración de destinos y tarifas de envío de Jet Express.</p>

    <hr>

    <!-- ================================= -->
    <!-- APARTADO DE DESTINOS -->
    <!-- ================================= -->

    <h2>Administración de Destinos</h2>

    <?php if ($mensajeDestino !== ''): ?>
        <p><?php echo htmlspecialchars($mensajeDestino); ?></p>
    <?php endif; ?>

    <h3>
        <?php echo $editarDestino ? 'Editar destino' : 'Registrar nuevo destino'; ?>
    </h3>

    <form action="../controlador/contDestino.php" method="POST">

        <input
            type="hidden"
            name="accion"
            value="<?php echo $editarDestino ? 'ACTUALIZAR' : 'REGISTRAR'; ?>"
        >

        <?php if ($editarDestino): ?>
            <input
                type="hidden"
                name="id_destino"
                value="<?php echo $editarDestino['id_destino']; ?>"
            >
        <?php endif; ?>

        <label>Nombre del destino:</label>
        <input
            type="text"
            name="nombre"
            maxlength="100"
            required
            value="<?php echo htmlspecialchars($editarDestino['nombre'] ?? ''); ?>"
        >

        <label>Departamento:</label>
        <select name="id_departamento" required>
            <option value="">Seleccione un departamento</option>

            <?php foreach ($departamentos as $departamento): ?>
                <option
                    value="<?php echo $departamento['id_departamento']; ?>"
                    <?php
                    if (
                        $editarDestino &&
                        $editarDestino['id_departamento'] == $departamento['id_departamento']
                    ) {
                        echo 'selected';
                    }
                    ?>
                >
                    <?php echo htmlspecialchars($departamento['nombre']); ?>
                </option>
            <?php endforeach; ?>

        </select>

        <label>Descripción:</label>
        <textarea name="descripcion" maxlength="250"><?php
            echo htmlspecialchars($editarDestino['descripcion'] ?? '');
        ?></textarea>

        <button type="submit">
            <?php echo $editarDestino ? 'Actualizar destino' : 'Registrar destino'; ?>
        </button>

        <?php if ($editarDestino): ?>
            <a href="tarifa_destino.php">Cancelar edición</a>
        <?php endif; ?>

    </form>

    <h3>Lista de destinos</h3>

    <form method="GET" action="tarifa_destino.php">

        <input
            type="text"
            name="buscar_destino"
            placeholder="Buscar destino o departamento"
            value="<?php echo htmlspecialchars($buscarDestino); ?>"
        >

        <button type="submit">Buscar</button>
        <a href="tarifa_destino.php">Limpiar</a>

    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Destino</th>
                <th>Departamento</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($destinos as $destino): ?>

                <tr>
                    <td><?php echo $destino['id_destino']; ?></td>

                    <td><?php echo htmlspecialchars($destino['nombre']); ?></td>

                    <td><?php echo htmlspecialchars($destino['departamento']); ?></td>

                    <td><?php echo htmlspecialchars($destino['descripcion'] ?? ''); ?></td>

                    <td>
                        <?php echo $destino['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                    </td>

                    <td>

                        <a href="tarifa_destino.php?editar_destino=<?php echo $destino['id_destino']; ?>">
                            Editar
                        </a>

                        <form action="../controlador/contDestino.php" method="POST">

                            <input type="hidden" name="accion" value="CAMBIAR_ESTADO">

                            <input
                                type="hidden"
                                name="id_destino"
                                value="<?php echo $destino['id_destino']; ?>"
                            >

                            <input
                                type="hidden"
                                name="estado"
                                value="<?php echo $destino['estado'] == 1 ? 0 : 1; ?>"
                            >

                            <button type="submit">
                                <?php echo $destino['estado'] == 1 ? 'Desactivar' : 'Activar'; ?>
                            </button>

                        </form>

                    </td>
                </tr>

            <?php endforeach; ?>

            <?php if (count($destinos) === 0): ?>
                <tr>
                    <td colspan="6">No se encontraron destinos.</td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>

    <hr>

    <!-- ================================= -->
    <!-- APARTADO DE TARIFAS -->
    <!-- ================================= -->

    <h2>Administración de Tarifas</h2>

    <?php if ($mensajeTarifa !== ''): ?>
        <p><?php echo htmlspecialchars($mensajeTarifa); ?></p>
    <?php endif; ?>

    <h3>
        <?php echo $editarTarifa ? 'Editar tarifa' : 'Registrar nueva tarifa'; ?>
    </h3>

    <form action="../controlador/contTarifa.php" method="POST">

        <input
            type="hidden"
            name="accion"
            value="<?php echo $editarTarifa ? 'ACTUALIZAR' : 'REGISTRAR'; ?>"
        >

        <?php if ($editarTarifa): ?>
            <input
                type="hidden"
                name="id_tarifa"
                value="<?php echo $editarTarifa['id_tarifa']; ?>"
            >
        <?php endif; ?>

        <label>Destino:</label>

        <select name="id_destino" required>
            <option value="">Seleccione un destino</option>

            <?php foreach ($destinosActivos as $destino): ?>

                <option
                    value="<?php echo $destino['id_destino']; ?>"
                    <?php
                    if (
                        $editarTarifa &&
                        $editarTarifa['id_destino'] == $destino['id_destino']
                    ) {
                        echo 'selected';
                    }
                    ?>
                >
                    <?php
                    echo htmlspecialchars(
                        $destino['nombre'] . ' - ' . $destino['departamento']
                    );
                    ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Tipo de guía:</label>

        <select name="id_tipo_guia" required>
            <option value="">Seleccione el tipo de guía</option>

            <?php foreach ($tiposGuia as $tipo): ?>

                <option
                    value="<?php echo $tipo['id_tipo_guia']; ?>"
                    <?php
                    if (
                        $editarTarifa &&
                        $editarTarifa['id_tipo_guia'] == $tipo['id_tipo_guia']
                    ) {
                        echo 'selected';
                    }
                    ?>
                >
                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Peso mínimo (kg):</label>

        <input
            type="number"
            name="peso_minimo"
            min="0"
            step="0.01"
            required
            value="<?php echo htmlspecialchars($editarTarifa['peso_minimo'] ?? '0'); ?>"
        >

        <label>Peso máximo (kg):</label>

        <input
            type="number"
            name="peso_maximo"
            min="0.01"
            step="0.01"
            required
            value="<?php echo htmlspecialchars($editarTarifa['peso_maximo'] ?? '1'); ?>"
        >

        <label>Precio (Bs):</label>

        <input
            type="number"
            name="precio"
            min="0.01"
            step="0.01"
            required
            value="<?php echo htmlspecialchars($editarTarifa['precio'] ?? ''); ?>"
        >

        <label>Descripción:</label>

        <textarea name="descripcion" maxlength="200"><?php
            echo htmlspecialchars($editarTarifa['descripcion'] ?? '');
        ?></textarea>

        <button type="submit">
            <?php echo $editarTarifa ? 'Actualizar tarifa' : 'Registrar tarifa'; ?>
        </button>

        <?php if ($editarTarifa): ?>
            <a href="tarifa_destino.php">Cancelar edición</a>
        <?php endif; ?>

    </form>

    <h3>Lista de tarifas</h3>

    <form method="GET" action="tarifa_destino.php">

        <input
            type="text"
            name="buscar_tarifa"
            placeholder="Buscar por destino, departamento o tipo"
            value="<?php echo htmlspecialchars($buscarTarifa); ?>"
        >

        <button type="submit">Buscar</button>
        <a href="tarifa_destino.php">Limpiar</a>

    </form>

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Destino</th>
                <th>Departamento</th>
                <th>Tipo de guía</th>
                <th>Peso mínimo</th>
                <th>Peso máximo</th>
                <th>Precio (Bs)</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($tarifas as $tarifa): ?>

                <tr>

                    <td><?php echo $tarifa['id_tarifa']; ?></td>

                    <td><?php echo htmlspecialchars($tarifa['destino']); ?></td>

                    <td><?php echo htmlspecialchars($tarifa['departamento']); ?></td>

                    <td><?php echo htmlspecialchars($tarifa['tipo_guia']); ?></td>

                    <td><?php echo $tarifa['peso_minimo']; ?> kg</td>

                    <td><?php echo $tarifa['peso_maximo']; ?> kg</td>

                    <td>Bs <?php echo $tarifa['precio']; ?></td>

                    <td><?php echo htmlspecialchars($tarifa['descripcion'] ?? ''); ?></td>

                    <td>
                        <?php echo $tarifa['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                    </td>

                    <td>

                        <a href="tarifa_destino.php?editar_tarifa=<?php echo $tarifa['id_tarifa']; ?>">
                            Editar
                        </a>

                        <form action="../controlador/contTarifa.php" method="POST">

                            <input type="hidden" name="accion" value="CAMBIAR_ESTADO">

                            <input
                                type="hidden"
                                name="id_tarifa"
                                value="<?php echo $tarifa['id_tarifa']; ?>"
                            >

                            <input
                                type="hidden"
                                name="estado"
                                value="<?php echo $tarifa['estado'] == 1 ? 0 : 1; ?>"
                            >

                            <button type="submit">
                                <?php echo $tarifa['estado'] == 1 ? 'Desactivar' : 'Activar'; ?>
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

            <?php if (count($tarifas) === 0): ?>
                <tr>
                    <td colspan="10">No se encontraron tarifas.</td>
                </tr>
            <?php endif; ?>

        </tbody>

    </table>

</div>

</body>
</html>
