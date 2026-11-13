<?php

require_once __DIR__ . '/../config/conexion.php';

class clsUsuario
{
    private $conexion;

    public function __construct()
    {
        $objConexion = new Conexion();
        $this->conexion = $objConexion->conectar();
    }

    // Buscar usuario para iniciar sesión
    public function buscarPorUsuario($usuario)
    {
        $sql = "SELECT
                    u.idusuario,
                    u.idperfil,
                    u.nombre_completo,
                    u.usuario,
                    u.password,
                    u.estado,
                    p.nombre AS perfil
                FROM usuarios u
                INNER JOIN perfiles p
                    ON u.idperfil = p.idperfil
                WHERE u.usuario = :usuario
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':usuario', $usuario);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Listar todos los usuarios
    public function listar()
    {
        $sql = "SELECT
                    u.idusuario,
                    u.idperfil,
                    u.nombre_completo,
                    u.usuario,
                    u.estado,
                    p.nombre AS perfil
                FROM usuarios u
                INNER JOIN perfiles p
                    ON u.idperfil = p.idperfil
                ORDER BY u.idusuario DESC";

        return $this->conexion->query($sql);
    }

    // Buscar usuario por ID
    public function buscarPorId($id)
    {
        $sql = "SELECT
                    idusuario,
                    idperfil,
                    nombre_completo,
                    usuario,
                    estado
                FROM usuarios
                WHERE idusuario = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    // Verificar si el nombre de usuario ya existe
    public function existeUsuario($usuario, $idExcluir = 0)
    {
        $sql = "SELECT idusuario
                FROM usuarios
                WHERE usuario = :usuario
                  AND idusuario != :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            ':usuario' => $usuario,
            ':id' => $idExcluir
        ]);

        return $consulta->fetch() !== false;
    }

    // Registrar usuario
    public function registrar($idPerfil, $nombreCompleto, $usuario, $password)
    {
        $sql = "INSERT INTO usuarios
                (idperfil, nombre_completo, usuario, password, estado)
                VALUES
                (:idperfil, :nombre, :usuario, :password, 1)";

        $consulta = $this->conexion->prepare($sql);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        return $consulta->execute([
            ':idperfil' => $idPerfil,
            ':nombre' => $nombreCompleto,
            ':usuario' => $usuario,
            ':password' => $passwordHash
        ]);
    }

    // Actualizar datos del usuario
    public function actualizar($id, $idPerfil, $nombreCompleto, $usuario)
    {
        $sql = "UPDATE usuarios
                SET idperfil = :idperfil,
                    nombre_completo = :nombre,
                    usuario = :usuario
                WHERE idusuario = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':idperfil' => $idPerfil,
            ':nombre' => $nombreCompleto,
            ':usuario' => $usuario,
            ':id' => $id
        ]);
    }

    // Cambiar contraseña
    public function cambiarPassword($id, $password)
    {
        $sql = "UPDATE usuarios
                SET password = :password
                WHERE idusuario = :id";

        $consulta = $this->conexion->prepare($sql);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        return $consulta->execute([
            ':password' => $passwordHash,
            ':id' => $id
        ]);
    }

    // Activar o desactivar usuario
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE usuarios
                SET estado = :estado
                WHERE idusuario = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }

    // Listar perfiles activos
    public function listarPerfilesActivos()
    {
        $sql = "SELECT idperfil, nombre
                FROM perfiles
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $this->conexion->query($sql);
    }
}