
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsCliente.php';

$objCliente = new clsCliente();

$buscar = trim($_GET['buscar'] ?? '');
$listaClientes = $objCliente->listar($buscar);

$clienteEditar = null;

if (isset($_GET['editar'])) {
    $idEditar = (int)$_GET['editar'];
    $clienteEditar = $objCliente->buscarPorId($idEditar);
}

$mensaje = $_SESSION['mensaje_cliente'] ?? '';
unset($_SESSION['mensaje_cliente']);

function escapar($valor)
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Clientes | Jet Express</title>

    <link rel="stylesheet" href="../assets/css/panel.css">
</head>

<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <header>
        <h1>Clientes</h1>
        <p>Administración y registro de clientes de Jet Express</p>
    </header>

    <?php if ($mensaje !== ''): ?>
        <p class="mensaje">
            <?= escapar($mensaje) ?>
        </p>
    <?php endif; ?>


    <!-- FORMULARIO DE REGISTRO -->

    <section>

        <h2>
            <?= $clienteEditar ? 'Editar cliente' : 'Registrar nuevo cliente' ?>
        </h2>

        <form action="../controlador/contCliente.php" method="POST">

            <input type="hidden"
                   name="accion"
                   value="<?= $clienteEditar ? 'ACTUALIZAR' : 'REGISTRAR' ?>">

            <?php if ($clienteEditar): ?>
                <input type="hidden"
                       name="id_cliente"
                       value="<?= (int)$clienteEditar['id_cliente'] ?>">
            <?php endif; ?>

            <label>Nombre completo *</label>

            <input type="text"
                   name="nombre"
                   maxlength="100"
                   value="<?= escapar($clienteEditar['nombre'] ?? '') ?>"
                   placeholder="Ingrese el nombre completo"
                   required>


            <label>Tipo de documento *</label>

            <input type="text"
                   name="tipo_documento"
                   maxlength="30"
                   value="<?= escapar($clienteEditar['tipo_documento'] ?? '') ?>"
                   placeholder="Ejemplo: Cédula de identidad"
                   required>


            <label>Número de documento *</label>

            <input type="text"
                   name="numero_documento"
                   maxlength="30"
                   value="<?= escapar($clienteEditar['numero_documento'] ?? '') ?>"
                   placeholder="Ingrese el número de documento"
                   required>


            <label>Teléfono</label>

            <input type="text"
                   name="telefono"
                   maxlength="20"
                   value="<?= escapar($clienteEditar['telefono'] ?? '') ?>"
                   placeholder="Ingrese el teléfono">


            <label>Dirección</label>

            <textarea name="direccion"
                      maxlength="200"
                      placeholder="Ingrese la dirección"><?= escapar($clienteEditar['direccion'] ?? '') ?></textarea>


            <button type="submit">
                <?= $clienteEditar ? 'Guardar cambios' : 'Registrar cliente' ?>
            </button>

            <?php if ($clienteEditar): ?>
                <a href="cliente.php">Cancelar edición</a>
            <?php endif; ?>

        </form>

        <p style="font-size:12px; margin-top:15px; color:#64748b;">
            * Los campos marcados son obligatorios.
        </p>

    </section>


    <!-- BUSCADOR -->

    <section>

        <h2>Buscar clientes</h2>

        <form action="cliente.php" method="GET">

            <label>Nombre, documento o teléfono</label>

            <input type="text"
                   name="buscar"
                   value="<?= escapar($buscar) ?>"
                   placeholder="Escriba el dato que desea buscar">

            <button type="submit">Buscar</button>

            <a href="cliente.php">Mostrar todos</a>

        </form>

    </section>


    <!-- LISTADO DE CLIENTES -->

    <section>

        <h2>Listado de clientes</h2>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo documento</th>
                    <th>Número documento</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($listaClientes->rowCount() > 0): ?>

                    <?php while ($cliente = $listaClientes->fetch()): ?>

                        <tr>

                            <td><?= (int)$cliente['id_cliente'] ?></td>

                            <td><?= escapar($cliente['nombre']) ?></td>

                            <td><?= escapar($cliente['tipo_documento']) ?></td>

                            <td><?= escapar($cliente['numero_documento']) ?></td>

                            <td><?= escapar($cliente['telefono']) ?></td>

                            <td><?= escapar($cliente['direccion']) ?></td>

                            <td>
                                <?= (int)$cliente['estado'] === 1
                                    ? 'Activo'
                                    : 'Inactivo' ?>
                            </td>

                            <td>

                                <a href="cliente.php?editar=<?= (int)$cliente['id_cliente'] ?>">
                                    Editar
                                </a>

                                <form action="../controlador/contCliente.php"
                                      method="POST"
                                      style="display:inline;">

                                    <input type="hidden"
                                           name="accion"
                                           value="CAMBIAR_ESTADO">

                                    <input type="hidden"
                                           name="id_cliente"
                                           value="<?= (int)$cliente['id_cliente'] ?>">

                                    <input type="hidden"
                                           name="estado"
                                           value="<?= (int)$cliente['estado'] === 1 ? 0 : 1 ?>">

                                    <button type="submit">
                                        <?= (int)$cliente['estado'] === 1
                                            ? 'Desactivar'
                                            : 'Activar' ?>
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="8">
                            No se encontraron clientes.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </section>

    <p>
        <a href="../index.php">Volver al panel principal</a>
    </p>

</div>

</body>
</html>
