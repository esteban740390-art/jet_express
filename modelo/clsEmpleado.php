<?php

require_once __DIR__ . '/../config/conexion.php';

class clsEmpleado
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Listar todos los empleados
    public function listar()
    {
        $sql = "SELECT
                    e.id_empleado,
                    e.idusuario,
                    e.telefono,
                    e.direccion,
                    e.cargo,
                    e.estado,
                    u.nombre_completo,
                    u.usuario,
                    p.nombre AS perfil
                FROM empleados e
                INNER JOIN usuarios u
                    ON e.idusuario = u.idusuario
                INNER JOIN perfiles p
                    ON u.idperfil = p.idperfil
                ORDER BY e.id_empleado DESC";

        return $this->conexion->query($sql);
    }

    // Buscar empleado por ID
    public function buscarPorId($id)
    {
        $sql = "SELECT
                    e.*,
                    u.nombre_completo,
                    u.usuario,
                    u.idperfil
                FROM empleados e
                INNER JOIN usuarios u
                    ON e.idusuario = u.idusuario
                WHERE e.id_empleado = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Listar usuarios que todavía no tienen un registro de empleado
    public function listarUsuariosDisponibles()
    {
        $sql = "SELECT
                    u.idusuario,
                    u.nombre_completo,
                    u.usuario
                FROM usuarios u
                WHERE u.estado = 1
                  AND NOT EXISTS (
                      SELECT 1
                      FROM empleados e
                      WHERE e.idusuario = u.idusuario
                  )
                ORDER BY u.nombre_completo ASC";

        return $this->conexion->query($sql);
    }

    // Registrar empleado
    public function registrar($idUsuario, $telefono, $direccion, $cargo)
    {
        $sql = "INSERT INTO empleados
                (idusuario, telefono, direccion, cargo, estado)
                VALUES
                (:idusuario, :telefono, :direccion, :cargo, 1)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':idusuario' => $idUsuario,
            ':telefono' => $telefono,
            ':direccion' => $direccion,
            ':cargo' => $cargo
        ]);
    }

    // Actualizar empleado
    public function actualizar($id, $telefono, $direccion, $cargo)
    {
        $sql = "UPDATE empleados
                SET telefono = :telefono,
                    direccion = :direccion,
                    cargo = :cargo
                WHERE id_empleado = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':telefono' => $telefono,
            ':direccion' => $direccion,
            ':cargo' => $cargo,
            ':id' => $id
        ]);
    }

    // Activar o desactivar empleado
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE empleados
                SET estado = :estado
                WHERE id_empleado = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}