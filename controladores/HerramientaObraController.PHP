<?php


session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once "../modelos/HerramientaObra.php";
require_once "../modelos/Herramienta.php";
require_once "../modelos/Obra.php";
require_once "../modelos/Auditoria.php";



class HerramientaObraController
{


    private $herramientaObra;
    private $herramienta;
    private $obra;
    private $auditoria;



    public function __construct()
    {


        $this->herramientaObra = new HerramientaObra();

        $this->herramienta = new Herramienta();

        $this->obra = new Obra();

        $this->auditoria = new Auditoria();
    }







    /*
    ==========================
            LISTAR
    ==========================
    */


    public function listar()
    {


        $id_obra = $_GET["id_obra"] ?? 0;


        $herramientas = $this->herramientaObra->obtenerPorObra($id_obra);



        require_once "../vistas/obras/herramientas/index.php";
    }








    /*
    ==========================
            CREAR
    ==========================
    */


    public function crear()
    {


        $id_obra = $_GET["id_obra"] ?? 0;


        $herramientas = $this->herramienta->obtenerDisponibles();

        require_once "../vistas/obras/herramientas/agregar.php";
    }








    /*
    ==========================
            GUARDAR
    ==========================
    */
    public function guardar()
    {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $datos = [

                "id_herramienta" => $_POST["id_herramienta"],

                "id_obra" => $_POST["id_obra"],

                "cantidad" => $_POST["cantidad"],

                "fecha_asignacion" => $_POST["fecha_asignacion"],

                "id_estado_herramienta" => 2,

                "observaciones" => $_POST["observaciones"] ?? null

            ];


            $disponibles = $this->herramienta->obtenerCantidadDisponible(
                $datos["id_herramienta"]
            );


            if ($datos["cantidad"] > $disponibles) {

                $_SESSION["error"] =
                    "No hay suficientes herramientas disponibles. Quedan " .
                    $disponibles . ".";

                header(
                    "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=crear&id_obra=" .
                        $datos["id_obra"]
                );

                exit();
            }


            if ($this->herramientaObra->agregar($datos)) {

                $this->auditoria->registrar([

                    "id_usuario" => $_SESSION["usuario"]["id"],

                    "accion" => "INSERTAR",

                    "tabla_afectada" => "herramienta_obra",

                    "id_registro" => $datos["id_obra"],

                    "descripcion" => "Asignación de herramienta a obra"

                ]);
            }


            header(
                "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=" .
                    $datos["id_obra"]
            );

            exit();
        }
    }
    /*
    ==========================
            EDITAR
    ==========================
    */


    public function editar()
    {


        $id = $_GET["id"] ?? 0;


        $herramientaObra = $this->herramientaObra->obtenerPorId($id);



        require_once "../vistas/obras/herramientas/editar.php";
    }









    /*
    ==========================
            ACTUALIZAR
    ==========================
    */


    public function actualizar()
    {



        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $asignacion = $this->herramientaObra->obtenerAsignacion(
                $_POST["id_herramienta_obra"]
            );

            $datos = [

                "id_herramienta_obra" => $_POST["id_herramienta_obra"],

                "cantidad" => $_POST["cantidad"],

                "fecha_devolucion" => $_POST["fecha_devolucion"],

                "id_estado_herramienta" => !empty($_POST["fecha_devolucion"])
                    ? 5
                    : 2,

                "observaciones" => $_POST["observaciones"] ?? null

            ];
            if ($this->herramientaObra->editar($datos)) {

                $this->auditoria->registrar([

                    "id_usuario" => $_SESSION["usuario"]["id"],

                    "accion" => "EDITAR",

                    "tabla_afectada" => "herramienta_obra",

                    "id_registro" => $datos["id_herramienta_obra"],

                    "descripcion" => "Actualización de herramienta asignada"

                ]);





                header(

                    "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=" . $_POST["id_obra"]

                );


                exit();
            }
        }
    }









    /*
    ==========================
            ELIMINAR
    ==========================
    */


    public function eliminar()
    {



        $id = $_GET["id"] ?? 0;


        $id_obra = $_GET["id_obra"] ?? 0;





        if ($this->herramientaObra->eliminar($id)) {


            $this->auditoria->registrar([

                "id_usuario" => $_SESSION["usuario"]["id"],

                "accion" => "ELIMINAR",

                "tabla_afectada" => "herramienta_obra",

                "id_registro" => $id,

                "descripcion" => "Eliminación de herramienta asignada"

            ]);
        }





        header(

            "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=" . $id_obra

        );


        exit();
    }
}









/*
=================================
        EJECUCIÓN
=================================
*/


$controller = new HerramientaObraController();



$accion = $_GET["accion"] ?? "listar";



if (method_exists($controller, $accion)) {


    $controller->$accion();
} else {


    echo "Acción no válida";
}
