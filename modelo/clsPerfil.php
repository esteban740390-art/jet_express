<?php

require_once __DIR__ . '/../config/conexion.php';

class clsPerfil
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar todos los perfiles
    public function listar()
    {
        $sql = "SELECT *
                FROM perfiles
                ORDER BY idperfil DESC";

        return $this->conexion->query($sql);
    }

    // Buscar un perfil por su ID
    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM perfiles
                WHERE idperfil = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Registrar un perfil
    public function registrar($nombre, $descripcion)
    {
        $sql = "INSERT INTO perfiles
                (nombre, descripcion, estado)
                VALUES (:nombre, :descripcion, 1)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion
        ]);
    }

    // Actualizar un perfil
    public function actualizar($id, $nombre, $descripcion)
    {
        $sql = "UPDATE perfiles
                SET nombre = :nombre,
                    descripcion = :descripcion
                WHERE idperfil = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':id' => $id
        ]);
    }

    // Activar o desactivar un perfil
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE perfiles
                SET estado = :estado
                WHERE idperfil = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }

    // Listar las opciones permitidas para un perfil
    public function listarOpciones($idPerfil)
    {
        $sql = "SELECT o.*
                FROM acceso a
                INNER JOIN opcion o
                    ON a.idopcion = o.idopcion
                WHERE a.idperfil = :idperfil
                  AND a.estado = 1
                  AND o.estado = 1
                ORDER BY o.idopcion ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':idperfil', $idPerfil, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta;
    }
}