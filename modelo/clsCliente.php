
<?php

require_once __DIR__ . '/../config/conexion.php';

class clsCliente
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    public function listar($buscar = '')
    {
        $sql = "SELECT * FROM clientes";

        if ($buscar !== '') {
            $sql .= " WHERE nombre LIKE :buscar
                      OR numero_documento LIKE :buscar
                      OR telefono LIKE :buscar";
        }

        $sql .= " ORDER BY id_cliente DESC";

        $consulta = $this->conexion->prepare($sql);

        if ($buscar !== '') {
            $termino = '%' . $buscar . '%';
            $consulta->bindValue(':buscar', $termino);
        }

        $consulta->execute();

        return $consulta;
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM clientes
                WHERE id_cliente = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    public function existeDocumento($tipoDocumento, $numeroDocumento, $idExcluir = 0)
    {
        $sql = "SELECT COUNT(*) FROM clientes
                WHERE tipo_documento = :tipo
                AND numero_documento = :numero";

        if ($idExcluir > 0) {
            $sql .= " AND id_cliente != :id";
        }

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindValue(':tipo', $tipoDocumento);
        $consulta->bindValue(':numero', $numeroDocumento);

        if ($idExcluir > 0) {
            $consulta->bindValue(':id', $idExcluir, PDO::PARAM_INT);
        }

        $consulta->execute();

        return $consulta->fetchColumn() > 0;
    }

    public function registrar($nombre, $tipoDocumento, $numeroDocumento, $telefono, $direccion)
    {
        $sql = "INSERT INTO clientes
                (nombre, tipo_documento, numero_documento, telefono, direccion, estado)
                VALUES
                (:nombre, :tipo, :numero, :telefono, :direccion, 1)";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindValue(':nombre', $nombre);
        $consulta->bindValue(':tipo', $tipoDocumento);
        $consulta->bindValue(':numero', $numeroDocumento);
        $consulta->bindValue(':telefono', $telefono);
        $consulta->bindValue(':direccion', $direccion);

        return $consulta->execute();
    }

    public function actualizar($id, $nombre, $tipoDocumento, $numeroDocumento, $telefono, $direccion)
    {
        $sql = "UPDATE clientes
                SET nombre = :nombre,
                    tipo_documento = :tipo,
                    numero_documento = :numero,
                    telefono = :telefono,
                    direccion = :direccion
                WHERE id_cliente = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $nombre);
        $consulta->bindValue(':tipo', $tipoDocumento);
        $consulta->bindValue(':numero', $numeroDocumento);
        $consulta->bindValue(':telefono', $telefono);
        $consulta->bindValue(':direccion', $direccion);

        return $consulta->execute();
    }

    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE clientes
                SET estado = :estado
                WHERE id_cliente = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);

        return $consulta->execute();
    }
}
