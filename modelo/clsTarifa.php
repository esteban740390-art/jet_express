
<?php

require_once __DIR__ . '/../config/conexion.php';

class clsTarifa
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar tarifas
    public function listar($buscar = '')
    {
        $sql = "SELECT
                    t.id_tarifa,
                    t.id_destino,
                    t.id_tipo_guia,
                    t.peso_minimo,
                    t.peso_maximo,
                    t.precio,
                    t.descripcion,
                    t.estado,
                    d.nombre AS destino,
                    dep.nombre AS departamento,
                    tg.nombre AS tipo_guia
                FROM tarifa t
                INNER JOIN destino d
                    ON t.id_destino = d.id_destino
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                INNER JOIN tipo_guia tg
                    ON t.id_tipo_guia = tg.id_tipo_guia
                WHERE d.nombre LIKE :buscar
                   OR dep.nombre LIKE :buscar
                   OR tg.nombre LIKE :buscar
                ORDER BY t.id_tarifa DESC";

        $consulta = $this->conexion->prepare($sql);
        $termino = '%' . $buscar . '%';
        $consulta->bindValue(':buscar', $termino);
        $consulta->execute();

        return $consulta;
    }

    // Buscar tarifa por identificador
    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM tarifa
                WHERE id_tarifa = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Listar destinos activos
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

    // Comprobar si ya existe el mismo rango de peso
    public function existeRango(
        $idDestino,
        $idTipoGuia,
        $pesoMinimo,
        $pesoMaximo,
        $idExcluir = 0
    ) {
        $sql = "SELECT id_tarifa
                FROM tarifa
                WHERE id_destino = :destino
                  AND id_tipo_guia = :tipo
                  AND id_tarifa != :id
                  AND NOT (
                      peso_maximo < :minimo
                      OR peso_minimo > :maximo
                  )
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ':destino' => $idDestino,
            ':tipo' => $idTipoGuia,
            ':id' => $idExcluir,
            ':minimo' => $pesoMinimo,
            ':maximo' => $pesoMaximo
        ]);

        return $consulta->fetch() !== false;
    }

    // Registrar tarifa
    public function registrar(
        $idDestino,
        $idTipoGuia,
        $pesoMinimo,
        $pesoMaximo,
        $precio,
        $descripcion
    ) {
        $sql = "INSERT INTO tarifa (
                    id_destino,
                    id_tipo_guia,
                    peso_minimo,
                    peso_maximo,
                    precio,
                    descripcion,
                    estado
                ) VALUES (
                    :destino,
                    :tipo,
                    :minimo,
                    :maximo,
                    :precio,
                    :descripcion,
                    1
                )";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':destino' => $idDestino,
            ':tipo' => $idTipoGuia,
            ':minimo' => $pesoMinimo,
            ':maximo' => $pesoMaximo,
            ':precio' => $precio,
            ':descripcion' => $descripcion
        ]);
    }

    // Actualizar tarifa
    public function actualizar(
        $id,
        $idDestino,
        $idTipoGuia,
        $pesoMinimo,
        $pesoMaximo,
        $precio,
        $descripcion
    ) {
        $sql = "UPDATE tarifa
                SET id_destino = :destino,
                    id_tipo_guia = :tipo,
                    peso_minimo = :minimo,
                    peso_maximo = :maximo,
                    precio = :precio,
                    descripcion = :descripcion
                WHERE id_tarifa = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':destino' => $idDestino,
            ':tipo' => $idTipoGuia,
            ':minimo' => $pesoMinimo,
            ':maximo' => $pesoMaximo,
            ':precio' => $precio,
            ':descripcion' => $descripcion,
            ':id' => $id
        ]);
    }

    // Cambiar estado
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE tarifa
                SET estado = :estado
                WHERE id_tarifa = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}
