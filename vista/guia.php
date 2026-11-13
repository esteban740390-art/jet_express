
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsGuia.php';

$objGuia = new clsGuia();

$clientes = $objGuia->listarClientesActivos()->fetchAll(PDO::FETCH_ASSOC);
$destinos = $objGuia->listarDestinosActivos()->fetchAll(PDO::FETCH_ASSOC);
$tiposGuia = $objGuia->listarTiposGuiaActivos()->fetchAll(PDO::FETCH_ASSOC);

$buscar = trim($_GET['buscar'] ?? '');
$guias = $objGuia->listar($buscar)->fetchAll(PDO::FETCH_ASSOC);

$origen = $objGuia->obtenerOrigenCobija();

$mensaje = $_SESSION['mensaje_guia'] ?? '';
unset($_SESSION['mensaje_guia']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Guías - Jet Express</title>

    <link rel="stylesheet" href="../assets/css/panel.css">
</head>

<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <h1>Registro de Guías</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <section>

        <h2>Registrar nueva guía</h2>

        <form action="../controlador/contGuia.php" method="POST">

            <input type="hidden" name="accion" value="REGISTRAR">

            <label>Cliente remitente:</label>
            <select name="id_cliente" required>
                <option value="">Seleccione un cliente</option>

                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?php echo $cliente['id_cliente']; ?>">
                        <?php
                        echo htmlspecialchars(
                            $cliente['nombre'] . ' - ' .
                            $cliente['tipo_documento'] . ': ' .
                            $cliente['numero_documento']
                        );
                        ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <label>Origen:</label>
            <input
                type="text"
                value="<?php
                    echo $origen
                        ? htmlspecialchars($origen['nombre'] . ' - ' . $origen['departamento'])
                        : 'No se encontró Cobija';
                ?>"
                readonly
            >

            <label>Destino:</label>
            <select name="id_destino" required>
                <option value="">Seleccione un destino</option>

                <?php foreach ($destinos as $destino): ?>
                    <option value="<?php echo $destino['id_destino']; ?>">
                        <?php
                        echo htmlspecialchars(
                            $destino['nombre'] . ' - ' .
                            $destino['departamento']
                        );
                        ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <label>Tipo de guía:</label>
            <select name="id_tipo_guia" required>
                <option value="">Seleccione el tipo de guía</option>

                <?php foreach ($tiposGuia as $tipo): ?>
                    <option value="<?php echo $tipo['id_tipo_guia']; ?>">
                        <?php echo htmlspecialchars($tipo['nombre']); ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <label>Nombre del destinatario:</label>
            <input
                type="text"
                name="destinatario_nombre"
                maxlength="100"
                required
            >

            <label>Teléfono del destinatario:</label>
            <input
                type="text"
                name="destinatario_telefono"
                maxlength="20"
            >

            <label>Dirección del destinatario:</label>
            <input
                type="text"
                name="destinatario_direccion"
                maxlength="200"
            >

            <label>Peso del paquete (kg):</label>
            <input
                type="number"
                name="peso"
                min="0.01"
                step="0.01"
                required
            >

            <label>Descripción del contenido:</label>
            <textarea
                name="descripcion"
                maxlength="250"
                rows="3"
            ></textarea>

            <button type="submit">Registrar guía</button>

        </form>

    </section>

    <hr>

    <section>

        <h2>Listado de guías</h2>

        <form method="GET" action="guia.php">

            <input
                type="text"
                name="buscar"
                placeholder="Buscar por código, cliente, destinatario o destino"
                value="<?php echo htmlspecialchars($buscar); ?>"
            >

            <button type="submit">Buscar</button>

            <a href="guia.php">Limpiar búsqueda</a>

        </form>

        <table>

            <thead>
                <tr>
                    <th>Código</th>
                    <th>Remitente</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Tipo</th>
                    <th>Destinatario</th>
                    <th>Peso</th>
                    <th>Precio</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>

                <?php if (count($guias) > 0): ?>

                    <?php foreach ($guias as $guia): ?>

                        <tr>

                            <td><?php echo htmlspecialchars($guia['codigo_guia']); ?></td>

                            <td><?php echo htmlspecialchars($guia['cliente']); ?></td>

                            <td><?php echo htmlspecialchars($guia['origen']); ?></td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $guia['destino'] . ' - ' .
                                    $guia['departamento']
                                );
                                ?>
                            </td>

                            <td><?php echo htmlspecialchars($guia['tipo_guia']); ?></td>

                            <td><?php echo htmlspecialchars($guia['destinatario_nombre']); ?></td>

                            <td><?php echo htmlspecialchars($guia['peso']); ?> kg</td>

                            <td>Bs <?php echo number_format((float)$guia['precio_total'], 2); ?></td>

                            <td><?php echo htmlspecialchars($guia['fecha_registro']); ?></td>

                            <td>
                                <?php echo (int)$guia['estado'] === 1 ? 'Activa' : 'Inactiva'; ?>
                            </td>

                            <td>

                                <form
                                    action="../controlador/contGuia.php"
                                    method="POST"
                                >

                                    <input type="hidden" name="accion" value="CAMBIAR_ESTADO">

                                    <input
                                        type="hidden"
                                        name="id_guia"
                                        value="<?php echo $guia['id_guia']; ?>"
                                    >

                                    <?php if ((int)$guia['estado'] === 1): ?>

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
                        <td colspan="11">No se encontraron guías registradas.</td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </section>

</div>

</body>
</html>
