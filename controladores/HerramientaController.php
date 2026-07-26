<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/Herramienta.php";
require_once "../modelos/UnidadHerramienta.php";


class HerramientaController
{

    private $auditoria;
    private $herramienta;
    private $unidad;



    public function __construct()
    {

        $this->auditoria = new Auditoria();

        $this->herramienta = new Herramienta();

        $this->unidad = new UnidadHerramienta();
    }




    /*
    ==================================
        AGREGAR HERRAMIENTA
    ==================================
    */


    public function agregar()
    {

        session_start();


        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../vistas/herramientas/");
            exit;
        }



        $datos = [


            "nombre" => trim($_POST["nombre"]),

            "tipo" => trim($_POST["tipo"]),

            "marca" => trim($_POST["marca"]),

            "modelo" => trim($_POST["modelo"]),

            "cantidad_total" => $_POST["cantidad_total"],

            "fecha_adquisicion" => $_POST["fecha_adquisicion"],

            "costo" => $_POST["costo"]


        ];




        if (
            empty($datos["nombre"]) ||
            empty($datos["cantidad_total"])
        ) {

            $_SESSION["error"] = "Complete los campos obligatorios.";

            header("Location: ../vistas/herramientas/agregar.php");

            exit;
        }





        if (!is_numeric($datos["cantidad_total"]) || $datos["cantidad_total"] <= 0) {

            $_SESSION["error"] = "La cantidad debe ser mayor a cero.";

            header("Location: ../vistas/herramientas/agregar.php");

            exit;
        }





        /*
        ==============================
        Guardar herramienta general
        ==============================
        */


        $id_herramienta = $this->herramienta->agregar($datos);



        if ($id_herramienta) {


            /*
            ==============================
            Crear unidades automáticamente
            ==============================
            */


            $this->unidad->crearUnidades(
                $id_herramienta,
                $datos["cantidad_total"]
            );





            $this->auditoria->registrar([


                "id_usuario" => $_SESSION["usuario"]["id"],

                "accion" => "INSERTAR",

                "tabla_afectada" => "herramienta",

                "id_registro" => $id_herramienta,

                "descripcion" =>
                "Se registró la herramienta "
                    . strtoupper($datos["nombre"])
                    . " con "
                    . $datos["cantidad_total"]
                    . " unidades."

            ]);



            $_SESSION["success"] =
                "Herramienta registrada correctamente.";
        } else {


            $_SESSION["error"] =
                "No se pudo registrar la herramienta.";
        }




        header("Location: ../vistas/herramientas/");

        exit;
    }






    /*
    ==================================
        MOSTRAR EDITAR
    ==================================
    */


    public function editar()
    {


        $id = $_GET["id"] ?? null;



        if (!$id) {

            header("Location: ../vistas/herramientas/");

            exit;
        }




        $herramienta = $this->herramienta->buscarPorId($id);



        if (!$herramienta) {

            echo "No se encontraron los datos de la herramienta.";

            exit;
        }




        require_once "../vistas/herramientas/editar.php";
    }






    /*
    ==================================
        ACTUALIZAR
    ==================================
    */


    public function actualizar()
    {


        session_start();



        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            header("Location: ../vistas/herramientas/");

            exit;
        }




        $datos = [


            "id_herramienta" => $_POST["id_herramienta"],

            "nombre" => trim($_POST["nombre"]),

            "tipo" => trim($_POST["tipo"]),

            "marca" => trim($_POST["marca"]),

            "modelo" => trim($_POST["modelo"]),

            "cantidad_total" => $_POST["cantidad_total"],

            "fecha_adquisicion" => $_POST["fecha_adquisicion"],

            "costo" => $_POST["costo"]

        ];





        if ($this->herramienta->editar($datos)) {


            $this->auditoria->registrar([


                "id_usuario" => $_SESSION["usuario"]["id"],

                "accion" => "EDITAR",

                "tabla_afectada" => "herramienta",

                "id_registro" => $datos["id_herramienta"],

                "descripcion" =>
                "Se actualizaron los datos de la herramienta "
                    . strtoupper($datos["nombre"])


            ]);



            $_SESSION["success"] =
                "Herramienta actualizada correctamente.";
        } else {

            $_SESSION["error"] =
                "No se pudo actualizar.";
        }



        header("Location: ../vistas/herramientas/");

        exit;
    }





    /*
    ==================================
        LISTAR
    ==================================
    */


    public function listar()
    {

        header("Location: ../vistas/herramientas/");

        exit;
    }

    public function ver()
    {

        $id = $_GET["id"] ?? 0;

        $herramienta = $this->herramienta->obtenerDetalle($id);

        if (!$herramienta) {

            header("Location: ../vistas/herramientas/");
            exit;
        }

        require_once "../vistas/herramientas/ver.php";
    }
}





$controller = new HerramientaController();



$accion = $_GET["accion"] ?? "";


switch($accion)
{

    case "agregar":

        $controller->agregar();

        break;

    case "editar":

        $controller->editar();

        break;

    case "actualizar":

        $controller->actualizar();

        break;

    case "listar":

        $controller->listar();

        break;

    case "ver":

        $controller->ver();

        break;

    default:

        header("Location: ../vistas/herramientas/");

        exit;

}
