
<?php

require_once __DIR__ . '/../config/conexion.php';

class clsReporte
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Reporte general de guías con filtros
    public function listarGuias(
        $desde = '',
        $hasta = '',
        $idDestino = 0,
        $idTipoGuia = 0
    ) {
        $sql = "SELECT
                    g.id_guia,
                    g.codigo_guia,
                    g.fecha_registro,
                    g.destinatario_nombre,
                    g.destinatario_telefono,
                    g.peso,
                    g.precio_total,
                    g.estado,
                    c.nombre AS cliente,
                    o.nombre AS origen,
                    d.nombre AS destino,
                    dep.nombre AS departamento,
                    tg.nombre AS tipo_guia
                FROM guia g
                INNER JOIN clientes c
                    ON g.id_cliente = c.id_cliente
                INNER JOIN destino o
                    ON g.id_origen = o.id_destino
                INNER JOIN destino d
                    ON g.id_destino = d.id_destino
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                INNER JOIN tarifa t
                    ON g.id_tarifa = t.id_tarifa
                INNER JOIN tipo_guia tg
                    ON t.id_tipo_guia = tg.id_tipo_guia
                WHERE 1 = 1";

        $parametros = [];

        if ($desde !== '') {
            $sql .= " AND DATE(g.fecha_registro) >= :desde";
            $parametros[':desde'] = $desde;
        }

        if ($hasta !== '') {
            $sql .= " AND DATE(g.fecha_registro) <= :hasta";
            $parametros[':hasta'] = $hasta;
        }

        if ($idDestino > 0) {
            $sql .= " AND g.id_destino = :destino";
            $parametros[':destino'] = $idDestino;
        }

        if ($idTipoGuia > 0) {
            $sql .= " AND t.id_tipo_guia = :tipo";
            $parametros[':tipo'] = $idTipoGuia;
        }

        $sql .= " ORDER BY g.fecha_registro DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta;
    }

    // Resumen de guías por tipo
    public function guiasPorTipo()
    {
        $sql = "SELECT
                    tg.id_tipo_guia,
                    tg.nombre AS tipo_guia,
                    COUNT(g.id_guia) AS cantidad,
                    COALESCE(SUM(g.precio_total), 0) AS total_recaudado
                FROM tipo_guia tg
                LEFT JOIN tarifa t
                    ON tg.id_tipo_guia = t.id_tipo_guia
                LEFT JOIN guia g
                    ON t.id_tarifa = g.id_tarifa
                GROUP BY tg.id_tipo_guia, tg.nombre
                ORDER BY tg.nombre ASC";

        return $this->conexion->query($sql);
    }

    // Resumen de guías por destino
    public function guiasPorDestino()
    {
        $sql = "SELECT
                    d.id_destino,
                    d.nombre AS destino,
                    dep.nombre AS departamento,
                    COUNT(g.id_guia) AS cantidad,
                    COALESCE(SUM(g.precio_total), 0) AS total_recaudado
                FROM destino d
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                LEFT JOIN guia g
                    ON d.id_destino = g.id_destino
                GROUP BY
                    d.id_destino,
                    d.nombre,
                    dep.nombre
                ORDER BY cantidad DESC, d.nombre ASC";

        return $this->conexion->query($sql);
    }

    // Listar clientes registrados
    public function listarClientes($buscar = '')
    {
        $sql = "SELECT
                    c.id_cliente,
                    c.nombre,
                    c.tipo_documento,
                    c.numero_documento,
                    c.telefono,
                    c.direccion,
                    c.estado,
                    COUNT(g.id_guia) AS cantidad_guias
                FROM clientes c
                LEFT JOIN guia g
                    ON c.id_cliente = g.id_cliente
                WHERE c.nombre LIKE :buscar
                   OR c.numero_documento LIKE :buscar
                GROUP BY
                    c.id_cliente,
                    c.nombre,
                    c.tipo_documento,
                    c.numero_documento,
                    c.telefono,
                    c.direccion,
                    c.estado
                ORDER BY c.nombre ASC";

        $consulta = $this->conexion->prepare($sql);
        $termino = '%' . $buscar . '%';
        $consulta->bindValue(':buscar', $termino);
        $consulta->execute();

        return $consulta;
    }

    // Reporte de despachos y empleados responsables
    public function listarDespachos($desde = '', $hasta = '')
    {
        $sql = "SELECT
                    d.id_despacho,
                    d.codigo_despacho,
                    d.fecha_despacho,
                    d.observaciones,
                    d.estado,
                    e.cargo,
                    u.nombre_completo AS empleado,
                    COUNT(dd.id_detalle_despacho) AS cantidad_guias
                FROM despacho d
                INNER JOIN empleados e
                    ON d.id_empleado = e.id_empleado
                INNER JOIN usuarios u
                    ON e.idusuario = u.idusuario
                LEFT JOIN detalle_despacho dd
                    ON d.id_despacho = dd.id_despacho
                WHERE 1 = 1";

        $parametros = [];

        if ($desde !== '') {
            $sql .= " AND DATE(d.fecha_despacho) >= :desde";
            $parametros[':desde'] = $desde;
        }

        if ($hasta !== '') {
            $sql .= " AND DATE(d.fecha_despacho) <= :hasta";
            $parametros[':hasta'] = $hasta;
        }

        $sql .= " GROUP BY
                    d.id_despacho,
                    d.codigo_despacho,
                    d.fecha_despacho,
                    d.observaciones,
                    d.estado,
                    e.cargo,
                    u.nombre_completo
                  ORDER BY d.fecha_despacho DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta;
    }

    // Reporte de tarifas
    public function listarTarifas()
    {
        $sql = "SELECT
                    t.id_tarifa,
                    d.nombre AS destino,
                    dep.nombre AS departamento,
                    tg.nombre AS tipo_guia,
                    t.peso_minimo,
                    t.peso_maximo,
                    t.precio,
                    t.descripcion,
                    t.estado
                FROM tarifa t
                INNER JOIN destino d
                    ON t.id_destino = d.id_destino
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                INNER JOIN tipo_guia tg
                    ON t.id_tipo_guia = tg.id_tipo_guia
                ORDER BY dep.nombre ASC, d.nombre ASC, tg.nombre ASC";

        return $this->conexion->query($sql);
    }

    // Listar destinos para filtros
    public function listarDestinos()
    {
        $sql = "SELECT
                    d.id_destino,
                    d.nombre,
                    dep.nombre AS departamento
                FROM destino d
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                ORDER BY dep.nombre ASC, d.nombre ASC";

        return $this->conexion->query($sql);
    }

    // Listar tipos de guía para filtros
    public function listarTiposGuia()
    {
        $sql = "SELECT id_tipo_guia, nombre
                FROM tipo_guia
                ORDER BY nombre ASC";

        return $this->conexion->query($sql);
    }
}
