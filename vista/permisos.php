
<?php

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsAcceso.php';
require_once __DIR__ . '/../modelo/clsPerfil.php';

$objAcceso = new clsAcceso();
$objPerfil = new clsPerfil();

$perfiles = $objPerfil->listar()->fetchAll(PDO::FETCH_ASSOC);

$idPerfil = isset($_GET['idperfil']) ? (int)$_GET['idperfil'] : 0;

$opciones = [];
$accesos = [];

if ($idPerfil > 0) {
    $opciones = $objAcceso->listarOpciones()->fetchAll(PDO::FETCH_ASSOC);
    $accesos = $objAcceso->listarPorPerfil($idPerfil)->fetchAll(PDO::FETCH_ASSOC);
}

$mensaje = $_SESSION['mensaje_acceso'] ?? '';
unset($_SESSION['mensaje_acceso']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permisos - Jet Express</title>
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>

<?php require __DIR__ . '/partials/navegacion.php'; ?>

<div class="contenedor">

    <h1>Administración de permisos</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <h2>Seleccionar perfil</h2>

    <form action="permisos.php" method="GET">
        <label>Perfil:</label>

        <select name="idperfil" required>
            <option value="">Seleccione un perfil</option>

            <?php foreach ($perfiles as $perfil): ?>
                <?php if ((int)$perfil['estado'] === 1): ?>
                    <option value="<?php echo (int)$perfil['idperfil']; ?>"
                        <?php echo $idPerfil === (int)$perfil['idperfil'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($perfil['nombre']); ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>

        </select>

        <button type="submit">Consultar permisos</button>
    </form>

    <?php if ($idPerfil > 0): ?>

        <h2>Opciones disponibles</h2>

        <?php if (count($opciones) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>Opción</th>
                        <th>URL</th>
                        <th>Permiso</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($opciones as $opcion): ?>

                        <?php
                        $estadoAcceso = null;

                        foreach ($accesos as $acceso) {
                            if ((int)$acceso['idopcion'] === (int)$opcion['idopcion']) {
                                $estadoAcceso = (int)$acceso['estado'];
                                break;
                            }
                        }
                        ?>

                        <tr>
                            <td><?php echo htmlspecialchars($opcion['descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($opcion['url'] ?? ''); ?></td>

                            <td>
                                <?php
                                if ($estadoAcceso === 1) {
                                    echo 'Con acceso';
                                } elseif ($estadoAcceso === 0) {
                                    echo 'Sin acceso';
                                } else {
                                    echo 'No asignado';
                                }
                                ?>
                            </td>

                            <td>
                                <form action="../controlador/contAcceso.php" method="POST">

                                    <input type="hidden" name="idperfil"
                                           value="<?php echo $idPerfil; ?>">

                                    <input type="hidden" name="idopcion"
                                           value="<?php echo (int)$opcion['idopcion']; ?>">

                                    <?php if ($estadoAcceso === null): ?>

                                        <input type="hidden" name="accion" value="REGISTRAR">
                                        <button type="submit">Asignar acceso</button>

                                    <?php else: ?>

                                        <input type="hidden" name="accion" value="CAMBIAR_ESTADO">
                                        <input type="hidden" name="estado"
                                               value="<?php echo $estadoAcceso === 1 ? 0 : 1; ?>">

                                        <button type="submit">
                                            <?php echo $estadoAcceso === 1 ? 'Retirar acceso' : 'Dar acceso'; ?>
                                        </button>

                                    <?php endif; ?>

                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>

            <p>No hay opciones activas registradas.</p>

        <?php endif; ?>

    <?php endif; ?>

    <p><a href="../index.php">Volver al inicio</a></p>

</div>

</body>
</html>
