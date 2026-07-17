<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/EmpleadoObra.php";
require_once "../modelos/Empleado.php";

class EmpleadoObraController
{
    private $auditoria;
    private $empleadoObra;

    public function __construct()
    {
        $this->auditoria = new Auditoria();
        $this->empleadoObra = new EmpleadoObra();
    }

    /*
==========================
    LISTAR
==========================
*/

public function listar()
{
    session_start();

    $id_obra = $_GET["id_obra"] ?? 0;


    $empleados = $this->empleadoObra->obtenerPorObra($id_obra);


    require "../vistas/obras/empleados/index.php";
}
/*
==========================
    CREAR
==========================
*/

public function crear()
{
    session_start();


    require_once "../modelos/Empleado.php";


    $empleado = new Empleado();


    $empleadosDisponibles = $empleado->obtenerDisponiblesPorObra($_GET['id_obra']);


    require "../vistas/obras/empleados/crear.php";
}

    /*
    ==========================
        AGREGAR
    ==========================
    */

    public function agregar()
    {
        session_start();

        if ($this->empleadoObra->existeEmpleadoActivo($_POST["id_empleado"], $_POST["id_obra"])) {

            header("Location: ../vistas/obras/empleados/index.php?id_obra=" . $_POST["id_obra"] . "&error=duplicado");
            exit();
        }

        $datos = [

            "id_empleado" => $_POST["id_empleado"],
            "id_obra" => $_POST["id_obra"],
            "fecha_ingreso" => $_POST["fecha_ingreso"],
            "observaciones" => $_POST["observaciones"],
            "id_usuario" => $_SESSION["usuario"]["id"]

        ];

        $this->empleadoObra->asignar($datos);

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "INSERTAR",

            "tabla_afectada" => "empleado_obra",

            "id_registro" => $_POST["id_empleado"],

            "descripcion" => "Asignó un empleado a una obra"

        ]);

        header("Location: ../vistas/obras/empleados/index.php?id_obra=" . $_POST["id_obra"]);
        exit();
    }

    /*
    ==========================
        EDITAR
    ==========================
    */

    public function editar()
    {
        session_start();

        $datos = [

            "id_empleado_obra" => $_POST["id_empleado_obra"],
            "fecha_ingreso" => $_POST["fecha_ingreso"],
            "observaciones" => $_POST["observaciones"]

        ];

        $this->empleadoObra->editar($datos);

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "EDITAR",

            "tabla_afectada" => "empleado_obra",

            "id_registro" => $_POST["id_empleado_obra"],

            "descripcion" => "Editó una asignación de empleado"

        ]);

        header("Location: ../vistas/obras/empleados/index.php?id_obra=" . $_POST["id_obra"]);
        exit();
    }

    /*
    ==========================
        RETIRAR
    ==========================
    */

    public function retirar()
    {
        session_start();

        $datos = [

            "id_empleado_obra" => $_POST["id_empleado_obra"],
            "fecha_egreso" => $_POST["fecha_egreso"],
            "motivo_egreso" => $_POST["motivo_egreso"],
            "observaciones" => $_POST["observaciones"]

        ];

        $this->empleadoObra->retirar($datos);

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "EDITAR",

            "tabla_afectada" => "empleado_obra",

            "id_registro" => $_POST["id_empleado_obra"],

            "descripcion" => "Retiró un empleado de una obra"

        ]);

        header("Location: ../vistas/obras/empleados/index.php?id_obra=" . $_POST["id_obra"]);
        exit();
    }
}

$controlador = new EmpleadoObraController();



if(isset($_GET["accion"])) {


    switch($_GET["accion"]) {


        case "listar":

            $controlador->listar();

            break;

case "crear":

    $controlador->crear();

break;
    }

}





if(isset($_POST["accion"])) {


    switch($_POST["accion"]) {


        case "agregar":

            $controlador->agregar();

            break;



        case "editar":

            $controlador->editar();

            break;



        case "retirar":

            $controlador->retirar();

            break;


    }

}