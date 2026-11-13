<?php

require_once __DIR__ . '/../config/conexion.php';

class clsOpcion
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar todas las opciones
    public function listar()
    {
        $sql = "SELECT *
                FROM opcion
                ORDER BY idopcion ASC";

        return $this->conexion->query($sql);
    }

    // Buscar una opción por su ID
    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM opcion
                WHERE idopcion = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Registrar una opción
    public function registrar($descripcion, $icono, $url)
    {
        $sql = "INSERT INTO opcion
                (descripcion, icono, url, estado)
                VALUES (:descripcion, :icono, :url, 1)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':descripcion' => $descripcion,
            ':icono' => $icono,
            ':url' => $url
        ]);
    }

    // Actualizar una opción
    public function actualizar($id, $descripcion, $icono, $url)
    {
        $sql = "UPDATE opcion
                SET descripcion = :descripcion,
                    icono = :icono,
                    url = :url
                WHERE idopcion = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':descripcion' => $descripcion,
            ':icono' => $icono,
            ':url' => $url,
            ':id' => $id
        ]);
    }

    // Activar o desactivar una opción
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE opcion
                SET estado = :estado
                WHERE idopcion = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}