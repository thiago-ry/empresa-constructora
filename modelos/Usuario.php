<?php

require_once "Conexion.php";

class Usuario
{

    private $conexion;

    public function __construct()
    {

        $db = new Conexion();

        $this->conexion = $db->conectar();
    }

    /*
    ==========================
        LOGIN
    ==========================
    */

    public function buscarPorCorreo($correo)
    {

        $sql = "SELECT
                    u.id_usuario,
                    u.id_rol,
                    u.id_cargo,
                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono,
                    u.direccion,
                    u.salario,
                    u.correo,
                    u.contraseña,
                    u.estado,
                    r.nombre_rol
                FROM usuario u
                INNER JOIN roles r
                    ON u.id_rol = r.id_rol
                WHERE u.correo = :correo";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":correo" => $correo

        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    /*
    ==========================
        ACCESOS
    ==========================
    */

    public function registrarIngreso($id_usuario)
    {

        $sql = "INSERT INTO acceso_usuario
                (
                    id_usuario,
                    fecha_hora_ingreso
                )
                VALUES
                (
                    :id_usuario,
                    NOW()
                )";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id_usuario" => $id_usuario

        ]);
    }

    public function registrarSalida($id_usuario)
    {

        $sql = "UPDATE acceso_usuario
                SET fecha_hora_salida = NOW()
                WHERE id_usuario = ?
                ORDER BY id_acceso DESC
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            $id_usuario

        ]);
    }

    /*
    ==========================
        USUARIOS
    ==========================
    */

    public function obtenerTodos()
    {

        $sql = "SELECT

                u.id_usuario,
                u.id_rol,

                u.nombre,
                u.apellido,
                u.documento,

                u.telefono,
                u.direccion,

                u.salario,

                u.correo,
                u.estado,

                r.nombre_rol

            FROM usuario u

            INNER JOIN roles r
                ON u.id_rol = r.id_rol

            ORDER BY u.nombre";


        $consulta = $this->conexion->prepare($sql);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeCorreo($correo)
    {

        $sql = "SELECT id_usuario
                FROM usuario
                WHERE correo = :correo";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":correo" => $correo

        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function agregar($datos)
    {
        $idRolEmpleado = $this->obtenerIdRolEmpleado()["id_rol"];

        if ($datos["id_rol"] != $idRolEmpleado) {

            $datos["id_cargo"] = null;
            $datos["salario"] = null;
        }

        $sql = "INSERT INTO usuario
                (
                    id_rol,
                    id_cargo,
                    nombre,
                    apellido,
                    documento,
                    telefono,
                    direccion,
                    salario,
                    correo,
                    contraseña,
                    estado
                )
                VALUES
                (
                    :id_rol,
                    :id_cargo,
                    :nombre,
                    :apellido,
                    :documento,
                    :telefono,
                    :direccion,
                    :salario,
                    :correo,
                    :clave,
                    1

                )";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ":id_rol"      => $datos["id_rol"],
            ":id_cargo"    => empty($datos["id_cargo"]) ? null : $datos["id_cargo"],
            ":nombre"      => ucwords(strtolower($datos["nombre"])),
            ":apellido"    => ucwords(strtolower($datos["apellido"])),
            ":documento"   => $datos["documento"],
            ":telefono"    => $datos["telefono"],
            ":direccion"   => $datos["direccion"],
            ":salario"     => $datos["salario"] == "" ? null : $datos["salario"],
            ":correo"      => $datos["correo"],
            ":clave"       => $datos["contraseña"]
        ]);

        return $this->conexion->lastInsertId();
    }

    public function buscarPorId($id)
    {

        $sql = "SELECT

                u.*,

                r.nombre_rol

            FROM usuario u

            INNER JOIN roles r
                ON u.id_rol = r.id_rol

            WHERE u.id_usuario = :id";


        $consulta = $this->conexion->prepare($sql);


        $consulta->execute([

            ":id" => $id

        ]);


        return $consulta->fetch(PDO::FETCH_ASSOC);
    }





    public function obtenerRolActual($id_usuario)
    {

        $sql = "SELECT

                    id_rol,
                    id_cargo

                FROM usuario

                WHERE id_usuario = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":id" => $id_usuario

        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }






    public function tieneObras($id_usuario)
    {

        $sql = "SELECT COUNT(*) AS cantidad

                FROM obra

                WHERE id_usuario = :id_usuario";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":id_usuario" => $id_usuario

        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC)["cantidad"] > 0;
    }

public function empleadoEnObra($id_usuario){
     $sql = "SELECT COUNT(*) AS cantidad

                FROM empleado_obra

                WHERE id_usuario = :id_usuario";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":id_usuario" => $id_usuario

        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC)["cantidad"] > 0;
}




    public function editar($datos)
    {
        $idRolEmpleado = $this->obtenerIdRolEmpleado()["id_rol"];

        if ($datos["id_rol"] != $idRolEmpleado) {

            $datos["id_cargo"] = null;
            $datos["salario"] = null;
        }
        $sql = "UPDATE usuario

                SET

                    id_rol = :id_rol,
                    id_cargo = :id_cargo,

                    nombre = :nombre,
                    apellido = :apellido,
                    documento = :documento,

                    telefono = :telefono,
                    direccion = :direccion,

                    salario = :salario,

                    correo = :correo

                WHERE id_usuario = :id_usuario";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id_rol"      => $datos["id_rol"],

            ":id_cargo"    => empty($datos["id_cargo"]) ? null : $datos["id_cargo"],

            ":nombre"      => ucwords(strtolower($datos["nombre"])),

            ":apellido"    => ucwords(strtolower($datos["apellido"])),

            ":documento"   => $datos["documento"],

            ":telefono"    => $datos["telefono"],

            ":direccion"   => $datos["direccion"],

            ":salario"     => $datos["salario"] == "" ? null : $datos["salario"],

            ":correo"      => $datos["correo"],

            ":id_usuario"  => $datos["id_usuario"]

        ]);
    }






    public function bajaLogica($id)
    {

        $sql = "UPDATE usuario

                SET estado = 0

                WHERE id_usuario = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id" => $id

        ]);
    }






    public function activarUsuario($id)
    {

        $sql = "UPDATE usuario

                SET estado = 1

                WHERE id_usuario = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id" => $id

        ]);
    }






    public function obtenerIdRolCliente()
    {

        $sql = "SELECT id_rol

                FROM roles

                WHERE nombre_rol = 'Cliente'";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }






    public function obtenerIdRolEmpleado()
    {

        $sql = "SELECT id_rol

                FROM roles

                WHERE nombre_rol = 'Empleado'";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }






    public function obtenerCargos()
    {

        $sql = "SELECT

                    id_cargo,
                    nombre

                FROM cargo

                ORDER BY nombre";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    ==========================
        CONSULTAS POR ROL
    ==========================
    */

    public function obtenerClientes($soloActivos = false)
    {
        $sql = "SELECT
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.correo,
                u.telefono,
                u.estado
            FROM usuario u
            INNER JOIN roles r
                ON u.id_rol = r.id_rol
            WHERE r.nombre_rol = 'Cliente'";

        if ($soloActivos) {
            $sql .= " AND u.estado = 1";
        }

        $sql .= " ORDER BY u.nombre";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }







    public function obtenerEmpleados()
    {

        $sql = "SELECT

                    u.id_usuario,
                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono,
                    u.salario,
                    u.estado,

                    c.nombre AS cargo

                FROM usuario u

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                LEFT JOIN cargo c
                    ON u.id_cargo = c.id_cargo

                WHERE r.nombre_rol = 'Empleado'

                ORDER BY u.nombre";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }






    public function obtenerPorRol($id_rol)
    {

        $sql = "SELECT

                    u.*,

                    r.nombre_rol,

                    c.nombre AS cargo

                FROM usuario u

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                LEFT JOIN cargo c
                    ON u.id_cargo = c.id_cargo

                WHERE u.id_rol = :id_rol

                ORDER BY u.nombre";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":id_rol" => $id_rol

        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }






    public function cambiarCargo($id_usuario, $id_cargo)
    {

        $sql = "UPDATE usuario

                SET id_cargo = :id_cargo

                WHERE id_usuario = :id_usuario";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id_cargo"   => empty($id_cargo) ? null : $id_cargo,

            ":id_usuario" => $id_usuario

        ]);
    }






    public function cambiarRol($id_usuario, $id_rol)
    {

        $sql = "UPDATE usuario

                SET id_rol = :id_rol

                WHERE id_usuario = :id_usuario";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id_rol"     => $id_rol,

            ":id_usuario" => $id_usuario

        ]);
    }






    public function obtenerNombreCargo($id_cargo)
    {

        $sql = "SELECT nombre

                FROM cargo

                WHERE id_cargo = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":id" => $id_cargo

        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerEmpleadosDisponiblesPorObra($id_obra)
    {

        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.documento
            FROM usuario u
            INNER JOIN roles r
                ON u.id_rol = r.id_rol
            WHERE r.nombre_rol = 'Empleado'
            AND u.estado = 1
            AND u.id_usuario NOT IN (
                SELECT eo.id_usuario
                FROM empleado_obra eo
                WHERE eo.id_obra = :id_obra
                AND eo.estado = 1
            )
            ORDER BY u.apellido, u.nombre";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            ":id_obra" => $id_obra
        ]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}
