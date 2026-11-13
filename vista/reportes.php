
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsReporte.php';

$objReporte = new clsReporte();

$destinos = $objReporte->listarDestinos()->fetchAll(PDO::FETCH_ASSOC);
$tiposGuia = $objReporte->listarTiposGuia()->fetchAll(PDO::FETCH_ASSOC);

$reporte = $_GET['reporte'] ?? '';

$guias = $_SESSION['reporte_guias'] ?? [];
$clientes = $_SESSION['reporte_clientes'] ?? [];
$despachos = $_SESSION['reporte_despachos'] ?? [];
$destinosReporte = $_SESSION['reporte_destinos'] ?? [];
$tiposReporte = $_SESSION['reporte_tipos'] ?? [];
$tarifas = $_SESSION['reporte_tarifas'] ?? [];

$filtros = $_SESSION['filtros_reporte'] ?? [];

$desde = $filtros['desde'] ?? '';
$hasta = $filtros['hasta'] ?? '';
$idDestinoSeleccionado = $filtros['id_destino'] ?? 0;
$idTipoSeleccionado = $filtros['id_tipo_guia'] ?? 0;

$buscar = $_GET['buscar'] ?? '';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Consultas y Reportes - Jet Express</title>

    <link rel="stylesheet" href="../assets/css/panel.css">
</head>

<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor-principal">

    <h1>Consultas y Reportes</h1>

    <p>Consulta la información registrada en el sistema Jet Express.</p>

    <hr>

    <!-- REPORTE DE GUIAS -->

    <h2>Reporte de guías</h2>

    <form action="../controlador/contReporte.php" method="GET">

        <input type="hidden" name="accion" value="GUIAS">

        <label>Fecha desde:</label>
        <input type="date" name="desde"
               value="<?php echo htmlspecialchars($desde); ?>">

        <label>Fecha hasta:</label>
        <input type="date" name="hasta"
               value="<?php echo htmlspecialchars($hasta); ?>">

        <label>Destino:</label>

        <select name="id_destino">
            <option value="0">Todos los destinos</option>

            <?php foreach ($destinos as $destino): ?>

                <option
                    value="<?php echo $destino['id_destino']; ?>"
                    <?php echo $idDestinoSeleccionado == $destino['id_destino'] ? 'selected' : ''; ?>
                >
                    <?php echo htmlspecialchars($destino['nombre'] . ' - ' . $destino['departamento']); ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Tipo de guía:</label>

        <select name="id_tipo_guia">
            <option value="0">Todos los tipos</option>

            <?php foreach ($tiposGuia as $tipo): ?>

                <option
                    value="<?php echo $tipo['id_tipo_guia']; ?>"
                    <?php echo $idTipoSeleccionado == $tipo['id_tipo_guia'] ? 'selected' : ''; ?>
                >
                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                </option>

            <?php endforeach; ?>

        </select>

        <button type="submit">Consultar guías</button>

    </form>

    <?php if ($reporte === 'guias'): ?>

        <h3>Resultados de guías</h3>

        <p>Total de registros: <?php echo count($guias); ?></p>

        <table>

            <thead>
                <tr>
                    <th>Código</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Tipo</th>
                    <th>Destinatario</th>
                    <th>Peso</th>
                    <th>Precio</th>
                    <th>Estado</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($guias as $guia): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($guia['codigo_guia']); ?></td>
                        <td><?php echo htmlspecialchars($guia['fecha_registro']); ?></td>
                        <td><?php echo htmlspecialchars($guia['cliente']); ?></td>
                        <td><?php echo htmlspecialchars($guia['origen']); ?></td>
                        <td><?php echo htmlspecialchars($guia['destino']); ?></td>
                        <td><?php echo htmlspecialchars($guia['tipo_guia']); ?></td>
                        <td><?php echo htmlspecialchars($guia['destinatario_nombre']); ?></td>
                        <td><?php echo $guia['peso']; ?> kg</td>
                        <td>Bs <?php echo $guia['precio_total']; ?></td>
                        <td><?php echo $guia['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                    </tr>

                <?php endforeach; ?>

                <?php if (count($guias) === 0): ?>
                    <tr>
                        <td colspan="10">No se encontraron guías.</td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    <?php endif; ?>

    <hr>

    <!-- REPORTE DE CLIENTES -->

    <h2>Reporte de clientes</h2>

    <form action="../controlador/contReporte.php" method="GET">

        <input type="hidden" name="accion" value="CLIENTES">

        <label>Buscar cliente:</label>

        <input
            type="text"
            name="buscar"
            placeholder="Nombre o documento"
            value="<?php echo htmlspecialchars($buscar); ?>"
        >

        <button type="submit">Consultar clientes</button>

    </form>

    <?php if ($reporte === 'clientes'): ?>

        <h3>Resultados de clientes</h3>

        <p>Total de registros: <?php echo count($clientes); ?></p>

        <table>

            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo documento</th>
                    <th>Número documento</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Cantidad de guías</th>
                    <th>Estado</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($clientes as $cliente): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['tipo_documento']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['numero_documento']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></td>
                        <td><?php echo $cliente['cantidad_guias']; ?></td>
                        <td><?php echo $cliente['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                    </tr>

                <?php endforeach; ?>

                <?php if (count($clientes) === 0): ?>
                    <tr>
                        <td colspan="7">No se encontraron clientes.</td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    <?php endif; ?>

    <hr>

    <!-- REPORTE DE DESPACHOS -->

    <h2>Reporte de despachos</h2>

    <form action="../controlador/contReporte.php" method="GET">

        <input type="hidden" name="accion" value="DESPACHOS">

        <label>Fecha desde:</label>
        <input type="date" name="desde"
               value="<?php echo htmlspecialchars($desde); ?>">

        <label>Fecha hasta:</label>
        <input type="date" name="hasta"
               value="<?php echo htmlspecialchars($hasta); ?>">

        <button type="submit">Consultar despachos</button>

    </form>

    <?php if ($reporte === 'despachos'): ?>

        <h3>Resultados de despachos</h3>

        <p>Total de registros: <?php echo count($despachos); ?></p>

        <table>

            <thead>
                <tr>
                    <th>Código</th>
                    <th>Fecha</th>
                    <th>Empleado responsable</th>
                    <th>Cargo</th>
                    <th>Cantidad de guías</th>
                    <th>Observaciones</th>
                    <th>Estado</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($despachos as $despacho): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($despacho['codigo_despacho']); ?></td>
                        <td><?php echo htmlspecialchars($despacho['fecha_despacho']); ?></td>
                        <td><?php echo htmlspecialchars($despacho['empleado']); ?></td>
                        <td><?php echo htmlspecialchars($despacho['cargo'] ?? ''); ?></td>
                        <td><?php echo $despacho['cantidad_guias']; ?></td>
                        <td><?php echo htmlspecialchars($despacho['observaciones'] ?? ''); ?></td>
                        <td><?php echo $despacho['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                    </tr>

                <?php endforeach; ?>

                <?php if (count($despachos) === 0): ?>
                    <tr>
                        <td colspan="7">No se encontraron despachos.</td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    <?php endif; ?>

    <hr>

    <!-- REPORTE POR DESTINO -->

    <h2>Resumen de guías por destino</h2>

    <form action="../controlador/contReporte.php" method="GET">
        <input type="hidden" name="accion" value="DESTINOS">
        <button type="submit">Consultar por destino</button>
    </form>

    <?php if ($reporte === 'destinos'): ?>

        <table>

            <thead>
                <tr>
                    <th>Destino</th>
                    <th>Departamento</th>
                    <th>Cantidad de guías</th>
                    <th>Total recaudado</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($destinosReporte as $fila): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($fila['destino']); ?></td>
                        <td><?php echo htmlspecialchars($fila['departamento']); ?></td>
                        <td><?php echo $fila['cantidad']; ?></td>
                        <td>Bs <?php echo $fila['total_recaudado']; ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

    <hr>

    <!-- REPORTE POR TIPO -->

    <h2>Resumen de guías por tipo</h2>

    <form action="../controlador/contReporte.php" method="GET">
        <input type="hidden" name="accion" value="TIPOS">
        <button type="submit">Consultar por tipo</button>
    </form>

    <?php if ($reporte === 'tipos'): ?>

        <table>

            <thead>
                <tr>
                    <th>Tipo de guía</th>
                    <th>Cantidad de guías</th>
                    <th>Total recaudado</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($tiposReporte as $fila): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($fila['tipo_guia']); ?></td>
                        <td><?php echo $fila['cantidad']; ?></td>
                        <td>Bs <?php echo $fila['total_recaudado']; ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

    <hr>

    <!-- REPORTE DE TARIFAS -->

    <h2>Reporte de tarifas</h2>

    <form action="../controlador/contReporte.php" method="GET">
        <input type="hidden" name="accion" value="TARIFAS">
        <button type="submit">Consultar tarifas</button>
    </form>

    <?php if ($reporte === 'tarifas'): ?>

        <table>

            <thead>
                <tr>
                    <th>Destino</th>
                    <th>Departamento</th>
                    <th>Tipo de guía</th>
                    <th>Peso mínimo</th>
                    <th>Peso máximo</th>
                    <th>Precio</th>
                    <th>Estado</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($tarifas as $tarifa): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($tarifa['destino']); ?></td>
                        <td><?php echo htmlspecialchars($tarifa['departamento']); ?></td>
                        <td><?php echo htmlspecialchars($tarifa['tipo_guia']); ?></td>
                        <td><?php echo $tarifa['peso_minimo']; ?> kg</td>
                        <td><?php echo $tarifa['peso_maximo']; ?> kg</td>
                        <td>Bs <?php echo $tarifa['precio']; ?></td>
                        <td><?php echo $tarifa['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>

</body>
</html>
