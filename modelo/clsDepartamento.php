<?php

require_once __DIR__ . '/../config/conexion.php';

class clsDepartamento
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar todos los departamentos
    public function listar()
    {
        $sql = "SELECT *
                FROM departamento
                ORDER BY nombre ASC";

        return $this->conexion->query($sql);
    }

    // Buscar departamento por ID
    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM departamento
                WHERE id_departamento = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Verificar si el nombre ya existe
    public function existeNombre($nombre, $idExcluir = 0)
    {
        $sql = "SELECT id_departamento
                FROM departamento
                WHERE LOWER(nombre) = LOWER(:nombre)
                  AND id_departamento != :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            ':nombre' => $nombre,
            ':id' => $idExcluir
        ]);

        return $consulta->fetch() !== false;
    }

    // Registrar departamento
    public function registrar($nombre)
    {
        $sql = "INSERT INTO departamento
                (nombre, estado)
                VALUES (:nombre, 1)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':nombre' => $nombre
        ]);
    }

    // Actualizar departamento
    public function actualizar($id, $nombre)
    {
        $sql = "UPDATE departamento
                SET nombre = :nombre
                WHERE id_departamento = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':nombre' => $nombre,
            ':id' => $id
        ]);
    }

    // Activar o desactivar departamento
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE departamento
                SET estado = :estado
                WHERE id_departamento = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}