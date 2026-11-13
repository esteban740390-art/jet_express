
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsDespacho.php';

$objDespacho = new clsDespacho();

$empleados = $objDespacho->listarEmpleadosActivos()->fetchAll(PDO::FETCH_ASSOC);

$guiasDisponibles = $objDespacho->listarGuiasDisponibles()->fetchAll(PDO::FETCH_ASSOC);

$buscar = trim($_GET['buscar'] ?? '');

$despachos = $objDespacho->listar($buscar)->fetchAll(PDO::FETCH_ASSOC);

$mensaje = $_SESSION['mensaje_despacho'] ?? '';
unset($_SESSION['mensaje_despacho']);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Despachos - Jet Express</title>

    <link rel="stylesheet" href="../assets/css/panel.css">
</head>

<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <h1>Despacho o Salida</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <section>

        <h2>Registrar nuevo despacho</h2>

        <form action="../controlador/contDespacho.php" method="POST">

            <input type="hidden" name="accion" value="REGISTRAR">

            <label>Empleado responsable:</label>

            <select name="id_empleado" required>

                <option value="">Seleccione un empleado</option>

                <?php foreach ($empleados as $empleado): ?>

                    <option value="<?php echo $empleado['id_empleado']; ?>">

                        <?php
                        echo htmlspecialchars(
                            $empleado['nombre_completo'] .
                            ($empleado['cargo'] ? ' - ' . $empleado['cargo'] : '')
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>

            <h3>Seleccionar guías para el despacho</h3>

            <?php if (count($guiasDisponibles) > 0): ?>

                <table>

                    <thead>
                        <tr>
                            <th>Seleccionar</th>
                            <th>Código</th>
                            <th>Remitente</th>
                            <th>Destinatario</th>
                            <th>Destino</th>
                            <th>Peso</th>
                            <th>Precio</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($guiasDisponibles as $guia): ?>

                            <tr>

                                <td>
                                    <input
                                        type="checkbox"
                                        name="guias[]"
                                        value="<?php echo $guia['id_guia']; ?>"
                                    >
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($guia['codigo_guia']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($guia['remitente']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($guia['destinatario_nombre']); ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $guia['destino'] . ' - ' .
                                        $guia['departamento']
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($guia['peso']); ?> kg
                                </td>

                                <td>
                                    Bs <?php echo number_format((float)$guia['precio_total'], 2); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <p>No hay guías disponibles para incluir en un despacho.</p>

            <?php endif; ?>

            <label>Observaciones:</label>

            <textarea
                name="observaciones"
                maxlength="250"
                rows="3"
            ></textarea>

            <button
                type="submit"
                <?php echo count($guiasDisponibles) === 0 ? 'disabled' : ''; ?>
            >
                Registrar despacho
            </button>

        </form>

    </section>

    <hr>

    <section>

        <h2>Listado de despachos</h2>

        <form method="GET" action="despacho.php">

            <input
                type="text"
                name="buscar"
                placeholder="Buscar por código o empleado"
                value="<?php echo htmlspecialchars($buscar); ?>"
            >

            <button type="submit">Buscar</button>

            <a href="despacho.php">Limpiar búsqueda</a>

        </form>

        <table>

            <thead>

                <tr>
                    <th>Código</th>
                    <th>Empleado responsable</th>
                    <th>Fecha</th>
                    <th>Cantidad de guías</th>
                    <th>Observaciones</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>

            </thead>

            <tbody>

                <?php if (count($despachos) > 0): ?>

                    <?php foreach ($despachos as $despacho): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($despacho['codigo_despacho']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($despacho['empleado']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($despacho['fecha_despacho']); ?>
                            </td>

                            <td>
                                <?php echo (int)$despacho['cantidad_guias']; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($despacho['observaciones'] ?? ''); ?>
                            </td>

                            <td>
                                <?php echo (int)$despacho['estado'] === 1 ? 'Activo' : 'Inactivo'; ?>
                            </td>

                            <td>

                                <form
                                    action="../controlador/contDespacho.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="CAMBIAR_ESTADO"
                                    >

                                    <input
                                        type="hidden"
                                        name="id_despacho"
                                        value="<?php echo $despacho['id_despacho']; ?>"
                                    >

                                    <?php if ((int)$despacho['estado'] === 1): ?>

                                        <input type="hidden" name="estado" value="0">

                                        <button type="submit">Anular</button>

                                    <?php else: ?>

                                        <input type="hidden" name="estado" value="1">

                                        <button type="submit">Activar</button>

                                    <?php endif; ?>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7">No se encontraron despachos registrados.</td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </section>

</div>

</body>
</html>
