
<?php

require_once __DIR__ . '/../config/conexion.php';

class clsDestino
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar todos los destinos
    public function listar($buscar = '')
    {
        $sql = "SELECT
                    d.id_destino,
                    d.nombre,
                    d.id_departamento,
                    d.descripcion,
                    d.estado,
                    dep.nombre AS departamento
                FROM destino d
                INNER JOIN departamento dep
                    ON d.id_departamento = dep.id_departamento
                WHERE d.nombre LIKE :buscar
                   OR dep.nombre LIKE :buscar
                ORDER BY d.id_destino DESC";

        $consulta = $this->conexion->prepare($sql);
        $termino = '%' . $buscar . '%';
        $consulta->bindValue(':buscar', $termino);
        $consulta->execute();

        return $consulta;
    }

    // Buscar destino por identificador
    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM destino
                WHERE id_destino = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Listar departamentos activos
    public function listarDepartamentosActivos()
    {
        $sql = "SELECT id_departamento, nombre
                FROM departamento
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $this->conexion->query($sql);
    }

    // Comprobar si el destino ya existe en el departamento
    public function existeDestino($nombre, $idDepartamento, $idExcluir = 0)
    {
        $sql = "SELECT id_destino
                FROM destino
                WHERE LOWER(nombre) = LOWER(:nombre)
                  AND id_departamento = :departamento
                  AND id_destino != :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ':nombre' => $nombre,
            ':departamento' => $idDepartamento,
            ':id' => $idExcluir
        ]);

        return $consulta->fetch() !== false;
    }

    // Registrar destino
    public function registrar($nombre, $idDepartamento, $descripcion)
    {
        $sql = "INSERT INTO destino
                (nombre, id_departamento, descripcion, estado)
                VALUES (:nombre, :departamento, :descripcion, 1)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':nombre' => $nombre,
            ':departamento' => $idDepartamento,
            ':descripcion' => $descripcion
        ]);
    }

    // Actualizar destino
    public function actualizar($id, $nombre, $idDepartamento, $descripcion)
    {
        $sql = "UPDATE destino
                SET nombre = :nombre,
                    id_departamento = :departamento,
                    descripcion = :descripcion
                WHERE id_destino = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':nombre' => $nombre,
            ':departamento' => $idDepartamento,
            ':descripcion' => $descripcion,
            ':id' => $id
        ]);
    }

    // Cambiar estado
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE destino
                SET estado = :estado
                WHERE id_destino = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}
