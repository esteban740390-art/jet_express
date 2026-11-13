
<?php

require_once __DIR__ . '/../config/conexion.php';

class clsDespacho
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar empleados activos
    public function listarEmpleadosActivos()
    {
        $sql = "SELECT
                    e.id_empleado,
                    e.telefono,
                    e.cargo,
                    u.nombre_completo
                FROM empleados e
                INNER JOIN usuarios u
                    ON e.idusuario = u.idusuario
                WHERE e.estado = 1
                  AND u.estado = 1
                ORDER BY u.nombre_completo ASC";

        return $this->conexion->query($sql);
    }

    // Listar guias disponibles para despacho
    public function listarGuiasDisponibles()
    {
        $sql = "SELECT
                    g.id_guia,
                    g.codigo_guia,
                    g.peso,
                    g.precio_total,
                    g.destinatario_nombre,
                    d.nombre AS destino,
                    dep.nombre AS departamento,
                    c.nombre AS remitente
                FROM guia g
                INNER JOIN destino d
                    ON g.id_destino = d.id_destino
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                INNER JOIN clientes c
                    ON g.id_cliente = c.id_cliente
                WHERE g.estado = 1
                  AND d.estado = 1
                  AND dep.estado = 1
                  AND NOT EXISTS (
                      SELECT 1
                      FROM detalle_despacho dd
                      INNER JOIN despacho des
                          ON dd.id_despacho = des.id_despacho
                      WHERE dd.id_guia = g.id_guia
                        AND des.estado = 1
                  )
                ORDER BY g.id_guia DESC";

        return $this->conexion->query($sql);
    }

    // Listar despachos registrados
    public function listar($buscar = '')
    {
        $sql = "SELECT
                    d.id_despacho,
                    d.codigo_despacho,
                    d.fecha_despacho,
                    d.observaciones,
                    d.estado,
                    u.nombre_completo AS empleado,
                    COUNT(dd.id_detalle_despacho) AS cantidad_guias
                FROM despacho d
                INNER JOIN empleados e
                    ON d.id_empleado = e.id_empleado
                INNER JOIN usuarios u
                    ON e.idusuario = u.idusuario
                LEFT JOIN detalle_despacho dd
                    ON d.id_despacho = dd.id_despacho
                WHERE d.codigo_despacho LIKE :buscar
                   OR u.nombre_completo LIKE :buscar
                GROUP BY
                    d.id_despacho,
                    d.codigo_despacho,
                    d.fecha_despacho,
                    d.observaciones,
                    d.estado,
                    u.nombre_completo
                ORDER BY d.id_despacho DESC";

        $consulta = $this->conexion->prepare($sql);
        $termino = '%' . $buscar . '%';
        $consulta->bindValue(':buscar', $termino);
        $consulta->execute();

        return $consulta;
    }

    // Buscar despacho por identificador
    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM despacho
                WHERE id_despacho = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Listar guias pertenecientes a un despacho
    public function listarGuiasPorDespacho($idDespacho)
    {
        $sql = "SELECT
                    g.id_guia,
                    g.codigo_guia,
                    g.destinatario_nombre,
                    g.peso,
                    g.precio_total,
                    d.nombre AS destino
                FROM detalle_despacho dd
                INNER JOIN guia g
                    ON dd.id_guia = g.id_guia
                INNER JOIN destino d
                    ON g.id_destino = d.id_destino
                WHERE dd.id_despacho = :id
                ORDER BY g.codigo_guia ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $idDespacho, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta;
    }

    // Registrar despacho y asociar varias guias
    public function registrar($idEmpleado, $observaciones, $guias)
    {
        try {

            if (empty($guias)) {
                throw new Exception('Debe seleccionar al menos una guía.');
            }

            $this->conexion->beginTransaction();

            // Validar empleado
            $sqlEmpleado = "SELECT e.id_empleado
                            FROM empleados e
                            INNER JOIN usuarios u
                                ON e.idusuario = u.idusuario
                            WHERE e.id_empleado = :id
                              AND e.estado = 1
                              AND u.estado = 1";

            $consultaEmpleado = $this->conexion->prepare($sqlEmpleado);
            $consultaEmpleado->execute([':id' => $idEmpleado]);

            if (!$consultaEmpleado->fetch()) {
                throw new Exception('El empleado no está activo.');
            }

            // Validar que todas las guias estén disponibles
            $sqlGuia = "SELECT g.id_guia
                        FROM guia g
                        INNER JOIN destino d
                            ON g.id_destino = d.id_destino
                        INNER JOIN departamento dep
                            ON d.id_departamento = dep.id_departamento
                        WHERE g.id_guia = :id
                          AND g.estado = 1
                          AND d.estado = 1
                          AND dep.estado = 1
                          AND NOT EXISTS (
                              SELECT 1
                              FROM detalle_despacho dd
                              INNER JOIN despacho des
                                  ON dd.id_despacho = des.id_despacho
                              WHERE dd.id_guia = g.id_guia
                          )
                        FOR UPDATE";

            $consultaGuia = $this->conexion->prepare($sqlGuia);

            $guiasValidadas = [];

            foreach ($guias as $idGuia) {

                $idGuia = (int)$idGuia;

                if ($idGuia <= 0) {
                    throw new Exception('Se encontró una guía inválida.');
                }

                $consultaGuia->execute([':id' => $idGuia]);

                if (!$consultaGuia->fetch()) {
                    throw new Exception('Una o más guías no están disponibles.');
                }

                $guiasValidadas[] = $idGuia;
            }

            // Crear despacho con código temporal
            $codigoTemporal = 'PENDIENTE-' . bin2hex(random_bytes(8));

            $sqlDespacho = "INSERT INTO despacho
                            (codigo_despacho, id_empleado, observaciones, estado)
                            VALUES (:codigo, :empleado, :observaciones, 1)";

            $consultaDespacho = $this->conexion->prepare($sqlDespacho);

            $consultaDespacho->execute([
                ':codigo' => $codigoTemporal,
                ':empleado' => $idEmpleado,
                ':observaciones' => $observaciones
            ]);

            $idDespacho = $this->conexion->lastInsertId();

            // Generar código consecutivo
            $codigoFinal = 'DES-' . str_pad(
                $idDespacho,
                6,
                '0',
                STR_PAD_LEFT
            );

            $actualizar = $this->conexion->prepare(
                "UPDATE despacho
                 SET codigo_despacho = :codigo
                 WHERE id_despacho = :id"
            );

            $actualizar->execute([
                ':codigo' => $codigoFinal,
                ':id' => $idDespacho
            ]);

            // Asociar las guias
            $sqlDetalle = "INSERT INTO detalle_despacho
                           (id_despacho, id_guia)
                           VALUES (:despacho, :guia)";

            $consultaDetalle = $this->conexion->prepare($sqlDetalle);

            foreach ($guiasValidadas as $idGuia) {
                $consultaDetalle->execute([
                    ':despacho' => $idDespacho,
                    ':guia' => $idGuia
                ]);
            }

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

    // Cambiar estado del despacho
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE despacho
                SET estado = :estado
                WHERE id_despacho = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        return $consulta->execute();
    }
}
