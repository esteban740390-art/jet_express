
<?php

require_once __DIR__ . '/../config/conexion.php';

class clsTipoGuia
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    public function listar()
    {
        $sql = "SELECT * FROM tipo_guia
                ORDER BY id_tipo_guia DESC";

        return $this->conexion->query($sql);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM tipo_guia
                WHERE id_tipo_guia = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':id', $id);
        $consulta->execute();

        return $consulta->fetch();
    }

    public function registrar($nombre, $descripcion)
    {
        $sql = "INSERT INTO tipo_guia
                (nombre, descripcion, estado)
                VALUES (:nombre, :descripcion, 1)";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':nombre', $nombre);
        $consulta->bindParam(':descripcion', $descripcion);

        return $consulta->execute();
    }

    public function actualizar($id, $nombre, $descripcion)
    {
        $sql = "UPDATE tipo_guia
                SET nombre = :nombre,
                    descripcion = :descripcion
                WHERE id_tipo_guia = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':id', $id);
        $consulta->bindParam(':nombre', $nombre);
        $consulta->bindParam(':descripcion', $descripcion);

        return $consulta->execute();
    }

    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE tipo_guia
                SET estado = :estado
                WHERE id_tipo_guia = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':id', $id);
        $consulta->bindParam(':estado', $estado);

        return $consulta->execute();
    }
}
