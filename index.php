<?php

require_once __DIR__ . '/config/seguridad.php';
require_once __DIR__ . '/modelo/clsGuia.php';

$nombre = $_SESSION['nombre_completo'] ?? 'Usuario';
$perfil = $_SESSION['perfil'] ?? '';

$objGuia = new clsGuia();
$estadisticas = $objGuia->obtenerEstadisticasHoy();
$guiasRecientes = $objGuia->listarRecientes()->fetchAll(PDO::FETCH_ASSOC);

function escaparPanel($valor)
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#102b46">
    <title>Inicio | Jet Express</title>
    <link rel="stylesheet" href="assets/css/panel.css">
</head>
<body>

<?php require __DIR__ . '/vista/partials/navegacion.php'; ?>

<main class="contenedor dashboard">
    <header class="dashboard-encabezado">
        <div>
            <p class="sobrelinea">JET EXPRESS · COBIJA, PANDO</p>
            <h1>Hola, <?= escaparPanel($nombre) ?></h1>
            <p>Este es el resumen de actividad de tu operación.</p>
        </div>
        <a class="boton boton-principal" href="vista/guia.php">
            <svg class="icono" aria-hidden="true"><use href="#i-paquete"></use></svg>
            Registrar una guía
        </a>
    </header>

    <section class="indicadores" aria-label="Estadísticas de hoy">
        <article class="indicador indicador-azul">
            <span class="indicador-icono">
                <svg class="icono" aria-hidden="true"><use href="#i-paquete"></use></svg>
            </span>
            <p>Guías registradas hoy</p>
            <strong><?= number_format((int)$estadisticas['guias_hoy']) ?></strong>
            <small>Registros de <?= date('d/m/Y') ?></small>
        </article>
        <article class="indicador indicador-verde">
            <span class="indicador-icono">
                <svg class="icono" aria-hidden="true"><use href="#i-dinero"></use></svg>
            </span>
            <p>Valor de guías activas hoy</p>
            <strong>Bs <?= number_format((float)$estadisticas['valor_hoy'], 2) ?></strong>
            <small>Según las guías activas del día</small>
        </article>
        <article class="indicador indicador-violeta">
            <span class="indicador-icono">
                <svg class="icono" aria-hidden="true"><use href="#i-lista"></use></svg>
            </span>
            <p>Guías activas</p>
            <strong><?= number_format((int)$estadisticas['guias_activas']) ?></strong>
            <small>En el sistema</small>
        </article>
        <article class="indicador indicador-naranja">
            <span class="indicador-icono">
                <svg class="icono" aria-hidden="true"><use href="#i-camion"></use></svg>
            </span>
            <p>Despachos de hoy</p>
            <strong><?= number_format((int)$estadisticas['despachos_hoy']) ?></strong>
            <small>Despachos activos registrados hoy</small>
        </article>
    </section>

    <section class="panel-recientes">
        <div class="panel-titulo">
            <div>
                <p class="sobrelinea">MOVIMIENTO RECIENTE</p>
                <h2>Últimas guías</h2>
            </div>
            <a class="enlace-panel" href="vista/guia.php">Ver todas <span aria-hidden="true">→</span></a>
        </div>
        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Remitente</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Valor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($guiasRecientes): ?>
                        <?php foreach ($guiasRecientes as $guia): ?>
                            <tr>
                                <td><strong><?= escaparPanel($guia['codigo_guia']) ?></strong></td>
                                <td><?= escaparPanel($guia['cliente']) ?></td>
                                <td><?= escaparPanel($guia['destino']) ?></td>
                                <td><?= escaparPanel($guia['fecha_registro']) ?></td>
                                <td>Bs <?= number_format((float)$guia['precio_total'], 2) ?></td>
                                <td>
                                    <span class="estado <?= (int)$guia['estado'] === 1 ? 'estado-activo' : 'estado-inactivo' ?>">
                                        <?= (int)$guia['estado'] === 1 ? 'Activa' : 'Anulada' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="tabla-vacia" colspan="6">Todavía no hay guías registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="accesos-rapidos">
        <div class="panel-titulo">
            <div>
                <p class="sobrelinea">ACCESOS DIRECTOS</p>
                <h2>¿Qué necesitas hacer?</h2>
            </div>
        </div>
        <div class="accesos-grid">
            <a class="acceso-card" href="vista/guia.php">
                <span class="acceso-icono"><svg class="icono" aria-hidden="true"><use href="#i-paquete"></use></svg></span>
                <span><strong>Gestionar guías</strong><small>Registrar y consultar envíos</small></span>
                <span class="acceso-flecha" aria-hidden="true">→</span>
            </a>
            <a class="acceso-card" href="vista/despacho.php">
                <span class="acceso-icono"><svg class="icono" aria-hidden="true"><use href="#i-camion"></use></svg></span>
                <span><strong>Crear despacho</strong><small>Organizar guías para salida</small></span>
                <span class="acceso-flecha" aria-hidden="true">→</span>
            </a>
            <a class="acceso-card" href="vista/reportes.php">
                <span class="acceso-icono"><svg class="icono" aria-hidden="true"><use href="#i-reporte"></use></svg></span>
                <span><strong>Ver reportes</strong><small>Consultar actividad y resultados</small></span>
                <span class="acceso-flecha" aria-hidden="true">→</span>
            </a>
        </div>
    </section>

    <footer class="pie-panel">Jet Express <span>·</span> Sistema de gestión de envíos</footer>
</main>

</body>
</html>
