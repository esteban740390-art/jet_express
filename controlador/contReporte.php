
<?php

session_start();

require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../modelo/clsReporte.php';

$objReporte = new clsReporte();

$accion = $_GET['accion'] ?? '';

$desde = trim($_GET['desde'] ?? '');
$hasta = trim($_GET['hasta'] ?? '');

$idDestino = (int)($_GET['id_destino'] ?? 0);
$idTipoGuia = (int)($_GET['id_tipo_guia'] ?? 0);

$buscar = trim($_GET['buscar'] ?? '');

switch ($accion) {

    case 'GUIAS':

        $resultado = $objReporte->listarGuias(
            $desde,
            $hasta,
            $idDestino,
            $idTipoGuia
        );

        $_SESSION['reporte_guias'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['filtros_reporte'] = [
            'desde' => $desde,
            'hasta' => $hasta,
            'id_destino' => $idDestino,
            'id_tipo_guia' => $idTipoGuia
        ];

        header('Location: ../vista/reportes.php?reporte=guias');
        exit;

    case 'CLIENTES':

        $resultado = $objReporte->listarClientes($buscar);

        $_SESSION['reporte_clientes'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

        header('Location: ../vista/reportes.php?reporte=clientes');
        exit;

    case 'DESPACHOS':

        $resultado = $objReporte->listarDespachos($desde, $hasta);

        $_SESSION['reporte_despachos'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['filtros_reporte'] = [
            'desde' => $desde,
            'hasta' => $hasta
        ];

        header('Location: ../vista/reportes.php?reporte=despachos');
        exit;

    case 'DESTINOS':

        $resultado = $objReporte->guiasPorDestino();

        $_SESSION['reporte_destinos'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

        header('Location: ../vista/reportes.php?reporte=destinos');
        exit;

    case 'TIPOS':

        $resultado = $objReporte->guiasPorTipo();

        $_SESSION['reporte_tipos'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

        header('Location: ../vista/reportes.php?reporte=tipos');
        exit;

    case 'TARIFAS':

        $resultado = $objReporte->listarTarifas();

        $_SESSION['reporte_tarifas'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

        header('Location: ../vista/reportes.php?reporte=tarifas');
        exit;

    default:

        header('Location: ../vista/reportes.php');
        exit;
}
