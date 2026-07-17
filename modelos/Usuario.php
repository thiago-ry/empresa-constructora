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



    public function buscarPorCorreo($correo)
    {

        $sql = "SELECT 
                    u.id_usuario,
                    u.id_rol,
                    u.nombre,
                    u.apellido,
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

            ":correo"=>$correo

        ]);


        return $consulta->fetch(PDO::FETCH_ASSOC);

    }





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


        $consulta=$this->conexion->prepare($sql);


        return $consulta->execute([

            ":id_usuario"=>$id_usuario

        ]);

    }






    /*
    ==========================
        CLIENTE
    ==========================
    */



  public function crearCliente($datos)
{
    $sql = "INSERT INTO cliente(

    nombre,
    apellido,
    telefono,
    direccion,
    correo,
    estado,
    id_usuario

)

            VALUES(?,?,?,?,?,?,?)";

    $stmt = $this->conexion->prepare($sql);

   return $stmt->execute([

    $datos["nombre"],
    $datos["apellido"],
    $datos["telefono"],
    $datos["direccion"],
    $datos["correo"],
    1,
    $datos["id_usuario"]

]);
}


public function actualizarCliente($datos)
{
    $sql = "UPDATE cliente SET

                nombre=?,
                apellido=?,
                telefono=?,
                direccion=?,
                correo=?

            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([

        $datos["nombre"],
        $datos["apellido"],
        $datos["telefono"],
        $datos["direccion"],
        $datos["correo"],
        $datos["id_usuario"]

    ]);
}


    public function existeCliente($id_usuario)
    {

        $sql="SELECT id_cliente
              FROM cliente
              WHERE id_usuario=:id_usuario";


        $consulta=$this->conexion->prepare($sql);


        $consulta->execute([

            ":id_usuario"=>$id_usuario

        ]);


        return $consulta->fetch(PDO::FETCH_ASSOC);

    }





    public function eliminarCliente($id_usuario)
    {

        $sql="DELETE FROM cliente
              WHERE id_usuario=:id_usuario";


        $consulta=$this->conexion->prepare($sql);


        return $consulta->execute([

            ":id_usuario"=>$id_usuario

        ]);

    }







    /*
    ==========================
        EMPLEADO
    ==========================
    */



    

public function activarEmpleado($id_usuario)
{
    $sql = "UPDATE empleado
            SET estado=1
            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([$id_usuario]);
}


public function crearEmpleado($datos)
{
    $sql = "INSERT INTO empleado(

                nombre,
                apellido,
                documento,
                telefono,
                direccion,
                salario,
                estado,
                id_usuario

            )VALUES(?,?,?,?,?,?,?,?)";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([

        $datos["nombre"],
        $datos["apellido"],
        $datos["documento"],
        $datos["telefono"],
        $datos["direccion"],
        $datos["salario"],
        1,
        $datos["id_usuario"]

    ]);
}





public function existeEmpleado($id_usuario)
{
    $sql = "SELECT id_empleado
            FROM empleado
            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$id_usuario]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}





    public function desactivarEmpleado($id_usuario)
{
    $sql = "UPDATE empleado
            SET estado=0
            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([$id_usuario]);
}






   public function actualizarEmpleado($datos)
{
    $sql = "UPDATE empleado SET

                nombre=?,
                apellido=?,
                documento=?,
                telefono=?,
                direccion=?,
                salario=?

            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([

        $datos["nombre"],
        $datos["apellido"],
        $datos["documento"],
        $datos["telefono"],
        $datos["direccion"],
        $datos["salario"],
        $datos["id_usuario"]

    ]);
}







    /*
    ==========================
        USUARIO
    ==========================
    */





    public function obtenerTodos()
    {

        $sql="SELECT
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.correo,
                u.estado,
                r.nombre_rol
              FROM usuario u
              INNER JOIN roles r
                ON u.id_rol=r.id_rol
              ORDER BY u.nombre";


        $consulta=$this->conexion->prepare($sql);


        $consulta->execute();


        return $consulta->fetchAll(PDO::FETCH_ASSOC);

    }





    public function existeCorreo($correo)
    {

        $sql="SELECT id_usuario
              FROM usuario
              WHERE correo=:correo";


        $consulta=$this->conexion->prepare($sql);


        $consulta->execute([

            ":correo"=>$correo

        ]);


        return $consulta->fetch(PDO::FETCH_ASSOC);

    }






    public function agregar($datos)
    {

        $sql="INSERT INTO usuario
        (
            id_rol,
            nombre,
            apellido,
            correo,
            contraseña,
            estado
        )
        VALUES
        (
            :id_rol,
            :nombre,
            :apellido,
            :correo,
            :clave,
            1
        )";


        $consulta=$this->conexion->prepare($sql);



        $consulta->execute([


            ":id_rol"=>$datos["id_rol"],

            ":nombre"=>ucwords(strtolower($datos["nombre"])),

            ":apellido"=>ucwords(strtolower($datos["apellido"])),

            ":correo"=>$datos["correo"],

            ":clave"=>$datos["contraseña"]


        ]);



        return $this->conexion->lastInsertId();

    }






    public function buscarPorId($id)
    {

        $sql="SELECT *
              FROM usuario
              WHERE id_usuario=:id";


        $consulta=$this->conexion->prepare($sql);


        $consulta->execute([

            ":id"=>$id

        ]);


        return $consulta->fetch(PDO::FETCH_ASSOC);

    }





    public function obtenerRolActual($id_usuario)
    {

        $sql="SELECT id_rol
              FROM usuario
              WHERE id_usuario=:id";


        $consulta=$this->conexion->prepare($sql);


        $consulta->execute([

            ":id"=>$id_usuario

        ]);


        return $consulta->fetch(PDO::FETCH_ASSOC);

    }






    public function tieneObras($id_usuario)
    {

        $sql="SELECT COUNT(*) cantidad
              FROM obra o
              INNER JOIN cliente c
                ON o.id_cliente=c.id_cliente
              WHERE c.id_usuario=:id_usuario";


        $consulta=$this->conexion->prepare($sql);


        $consulta->execute([

            ":id_usuario"=>$id_usuario

        ]);


        return $consulta->fetch(PDO::FETCH_ASSOC)["cantidad"]>0;

    }






    public function editar($datos)
    {

        $sql="UPDATE usuario
              SET
                id_rol=:id_rol,
                nombre=:nombre,
                apellido=:apellido,
                correo=:correo
              WHERE id_usuario=:id_usuario";



        $consulta=$this->conexion->prepare($sql);



        return $consulta->execute([


            ":id_rol"=>$datos["id_rol"],

            ":nombre"=>$datos["nombre"],

            ":apellido"=>$datos["apellido"],

            ":correo"=>$datos["correo"],

            ":id_usuario"=>$datos["id_usuario"]


        ]);

    }





    public function bajaLogica($id)
    {

        $sql="UPDATE usuario
              SET estado=0
              WHERE id_usuario=:id";


        $consulta=$this->conexion->prepare($sql);



        return $consulta->execute([

            ":id"=>$id

        ]);

    }


public function activarCliente($id_usuario)
{
    $sql = "UPDATE cliente
            SET estado=1
            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([$id_usuario]);
}

public function desactivarCliente($id_usuario)
{
    $sql = "UPDATE cliente
            SET estado=0
            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([$id_usuario]);
}


    public function activarUsuario($id)
    {

        $sql="UPDATE usuario
              SET estado=1
              WHERE id_usuario=:id";


        $consulta=$this->conexion->prepare($sql);


        return $consulta->execute([

            ":id"=>$id

        ]);

    }





    public function obtenerIdRolCliente()
    {

        $sql="SELECT id_rol
              FROM roles
              WHERE nombre_rol='Cliente'";


        $consulta=$this->conexion->prepare($sql);


        $consulta->execute();


        return $consulta->fetch(PDO::FETCH_ASSOC);

    }
public function obtenerIdRolEmpleado()
    {

        $sql="SELECT id_rol
              FROM roles
              WHERE nombre_rol='Empleado'";


        $consulta=$this->conexion->prepare($sql);


        $consulta->execute();


        return $consulta->fetch(PDO::FETCH_ASSOC);

    }
public function buscarEmpleadoPorUsuario($id_usuario)
{
    $sql = "SELECT *
            FROM empleado
            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$id_usuario]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function buscarClientePorUsuario($id_usuario)
{
    $sql = "SELECT *
            FROM cliente
            WHERE id_usuario=?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$id_usuario]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


}