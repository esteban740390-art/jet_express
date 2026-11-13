<?php

require_once __DIR__ . '/../config/conexion.php';

class clsAcceso
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar todos los permisos de un perfil
    public function listarPorPerfil($idPerfil)
    {
        $sql = "SELECT
                    a.idacceso,
                    a.idperfil,
                    a.idopcion,
                    a.estado,
                    o.descripcion,
                    o.icono,
                    o.url
                FROM acceso a
                INNER JOIN opcion o
                    ON a.idopcion = o.idopcion
                WHERE a.idperfil = :idperfil
                ORDER BY o.idopcion ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':idperfil', $idPerfil, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta;
    }

    // Consultar las opciones disponibles
    public function listarOpciones()
    {
        $sql = "SELECT idopcion, descripcion, icono, url
                FROM opcion
                WHERE estado = 1
                ORDER BY idopcion ASC";

        return $this->conexion->query($sql);
    }

    // Verificar si existe un permiso
    public function existeAcceso($idPerfil, $idOpcion)
    {
        $sql = "SELECT idacceso
                FROM acceso
                WHERE idperfil = :idperfil
                  AND idopcion = :idopcion";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            ':idperfil' => $idPerfil,
            ':idopcion' => $idOpcion
        ]);

        return $consulta->fetch() !== false;
    }

    // Registrar un permiso
    public function registrar($idPerfil, $idOpcion)
    {
        $sql = "INSERT INTO acceso
                (idperfil, idopcion, estado)
                VALUES (:idperfil, :idopcion, 1)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':idperfil' => $idPerfil,
            ':idopcion' => $idOpcion
        ]);
    }

    // Activar o desactivar un permiso
    public function cambiarEstado($idPerfil, $idOpcion, $estado)
    {
        $sql = "UPDATE acceso
                SET estado = :estado
                WHERE idperfil = :idperfil
                  AND idopcion = :idopcion";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':idperfil' => $idPerfil,
            ':idopcion' => $idOpcion
        ]);
    }
}