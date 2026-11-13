
<?php

require_once __DIR__ . '/../config/conexion.php';

class clsGuia
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar guias registradas
    public function listar($buscar = '')
    {
        $sql = "SELECT
                    g.*,
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
                WHERE
                    g.codigo_guia LIKE :buscar
                    OR c.nombre LIKE :buscar
                    OR g.destinatario_nombre LIKE :buscar
                    OR d.nombre LIKE :buscar
                ORDER BY g.id_guia DESC";

        $consulta = $this->conexion->prepare($sql);
        $termino = '%' . $buscar . '%';
        $consulta->bindValue(':buscar', $termino);
        $consulta->execute();

        return $consulta;
    }

    public function obtenerEstadisticasHoy()
    {
        $sql = "SELECT
                    COUNT(*) AS guias_hoy,
                    COALESCE(SUM(
                        CASE WHEN estado = 1 THEN precio_total ELSE 0 END
                    ), 0) AS valor_hoy,
                    (SELECT COUNT(*)
                     FROM guia
                     WHERE estado = 1) AS guias_activas,
                    (SELECT COUNT(*)
                     FROM despacho
                     WHERE estado = 1
                       AND DATE(fecha_despacho) = CURRENT_DATE()) AS despachos_hoy
                FROM guia
                WHERE DATE(fecha_registro) = CURRENT_DATE()";

        return $this->conexion->query($sql)->fetch();
    }

    public function listarRecientes()
    {
        $sql = "SELECT
                    g.codigo_guia,
                    g.fecha_registro,
                    g.precio_total,
                    g.estado,
                    c.nombre AS cliente,
                    d.nombre AS destino
                FROM guia g
                INNER JOIN clientes c
                    ON g.id_cliente = c.id_cliente
                INNER JOIN destino d
                    ON g.id_destino = d.id_destino
                ORDER BY g.id_guia DESC
                LIMIT 5";

        return $this->conexion->query($sql);
    }

    // Buscar una guia por su identificador
    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM guia
                WHERE id_guia = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Obtener el origen Cobija
    public function obtenerOrigenCobija()
    {
        $sql = "SELECT
                    d.id_destino,
                    d.nombre,
                    dep.nombre AS departamento
                FROM destino d
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                WHERE d.nombre = 'Cobija'
                  AND dep.nombre = 'Pando'
                LIMIT 1";

        return $this->conexion->query($sql)->fetch();
    }

    // Listar clientes activos
    public function listarClientesActivos()
    {
        $sql = "SELECT id_cliente, nombre,
                       tipo_documento, numero_documento
                FROM clientes
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $this->conexion->query($sql);
    }

    // Listar destinos activos, excepto Cobija
    public function listarDestinosActivos()
    {
        $sql = "SELECT
                    d.id_destino,
                    d.nombre,
                    dep.nombre AS departamento
                FROM destino d
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                WHERE d.estado = 1
                  AND dep.estado = 1
                  AND NOT (
                      d.nombre = 'Cobija'
                      AND dep.nombre = 'Pando'
                  )
                ORDER BY dep.nombre ASC, d.nombre ASC";

        return $this->conexion->query($sql);
    }

    // Listar tipos de guia activos
    public function listarTiposGuiaActivos()
    {
        $sql = "SELECT id_tipo_guia, nombre
                FROM tipo_guia
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $this->conexion->query($sql);
    }

    // Buscar tarifa según destino, tipo de guia y peso
    public function buscarTarifa($idDestino, $idTipoGuia, $peso)
    {
        $sql = "SELECT
                    id_tarifa,
                    precio,
                    peso_minimo,
                    peso_maximo
                FROM tarifa
                WHERE id_destino = :destino
                  AND id_tipo_guia = :tipo
                  AND estado = 1
                  AND :peso BETWEEN peso_minimo AND peso_maximo
                ORDER BY peso_minimo ASC
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':destino', $idDestino, PDO::PARAM_INT);
        $consulta->bindValue(':tipo', $idTipoGuia, PDO::PARAM_INT);
        $consulta->bindValue(':peso', $peso);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Registrar una guia
    public function registrar(
        $idCliente,
        $idOrigen,
        $idDestino,
        $idTarifa,
        $destinatario,
        $telefono,
        $direccion,
        $peso,
        $descripcion,
        $precio
    ) {
        try {
            $this->conexion->beginTransaction();

            // Código temporal para obtener el ID generado
            $codigoTemporal = 'PENDIENTE-' . bin2hex(random_bytes(8));

            $sql = "INSERT INTO guia (
                        codigo_guia,
                        id_cliente,
                        id_origen,
                        id_destino,
                        id_tarifa,
                        destinatario_nombre,
                        destinatario_telefono,
                        destinatario_direccion,
                        peso,
                        descripcion,
                        precio_total,
                        estado
                    ) VALUES (
                        :codigo,
                        :cliente,
                        :origen,
                        :destino,
                        :tarifa,
                        :destinatario,
                        :telefono,
                        :direccion,
                        :peso,
                        :descripcion,
                        :precio,
                        1
                    )";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                ':codigo' => $codigoTemporal,
                ':cliente' => $idCliente,
                ':origen' => $idOrigen,
                ':destino' => $idDestino,
                ':tarifa' => $idTarifa,
                ':destinatario' => $destinatario,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':peso' => $peso,
                ':descripcion' => $descripcion,
                ':precio' => $precio
            ]);

            $idGuia = $this->conexion->lastInsertId();

            $codigoFinal = 'JE-' . str_pad(
                $idGuia,
                6,
                '0',
                STR_PAD_LEFT
            );

            $actualizar = $this->conexion->prepare(
                "UPDATE guia
                 SET codigo_guia = :codigo
                 WHERE id_guia = :id"
            );

            $actualizar->execute([
                ':codigo' => $codigoFinal,
                ':id' => $idGuia
            ]);

            $this->conexion->commit();

            return [
                'resultado' => true,
                'codigo' => $codigoFinal
            ];

        } catch (Exception $e) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return [
                'resultado' => false,
                'codigo' => '',
                'error' => $e->getMessage()
            ];
        }
    }

    // Cambiar estado de una guia
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE guia
                SET estado = :estado
                WHERE id_guia = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        return $consulta->execute();
    }
}
