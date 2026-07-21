<?php

require_once "../modelos/MovimientoMaterial.php";
require_once "../modelos/Material.php";
require_once "../modelos/Auditoria.php";

class MovimientoMaterialController
{

    private $movimiento;
    private $material;
    private $auditoria;

    public function __construct()
    {
        $this->movimiento = new MovimientoMaterial();
        $this->material = new Material();
        $this->auditoria = new Auditoria();
    }

    /*
    ==========================
        AGREGAR MOVIMIENTO
    ==========================
    */

    public function agregar()
    {

        session_start();

        if ($_SERVER["REQUEST_METHOD"] != "POST") {

            header("Location: ../vistas/materiales/");
            exit;

        }

        $id_material = $_POST["id_material"];
        $tipo = $_POST["tipo"];
        $cantidad = $_POST["cantidad"];
        $observacion = trim($_POST["observacion"]);

        if (empty($id_material) || empty($tipo) || empty($cantidad)) {

            $_SESSION["error"] = "Complete todos los campos.";

            header("Location: ../vistas/movimientos/agregar.php?id=" . $id_material);

            exit;

        }

        if (!is_numeric($cantidad) || $cantidad <= 0) {

            $_SESSION["error"] = "La cantidad es inválida.";

            header("Location: ../vistas/movimientos/agregar.php?id=" . $id_material);

            exit;

        }

        $material = $this->material->buscar($id_material);

        if (!$material) {

            $_SESSION["error"] = "Material inexistente.";

            header("Location: ../vistas/materiales/");

            exit;

        }

        $stockActual = $material["stock"];

        if ($tipo == "INGRESO") {

            $nuevoStock = $stockActual + $cantidad;

        } else {

            if ($cantidad > $stockActual) {

                $_SESSION["error"] = "No hay stock suficiente.";

                header("Location: ../vistas/movimientos/agregar.php?id=" . $id_material);

                exit;

            }

            $nuevoStock = $stockActual - $cantidad;

        }

        $datos = [

            "id_material" => $id_material,
            "id_usuario" => $_SESSION["usuario"]["id"],
            "tipo" => $tipo,
            "cantidad" => $cantidad,
            "observacion" => $observacion

        ];

        if ($this->movimiento->agregar($datos)) {

            $this->movimiento->actualizarStock($id_material, $nuevoStock);

            $this->auditoria->registrar([

                "id_usuario" => $_SESSION["usuario"]["id"],
                "accion" => "MOVIMIENTO",
                "tabla_afectada" => "MOVIMIENTO_MATERIAL",
                "id_registro" => null,
                "descripcion" =>
                    $tipo .
                    " de " .
                    $cantidad .
                    " unidades del material: " .
                    $material["nombre_material"]

            ]);

            $_SESSION["success"] = "Movimiento registrado correctamente.";

        } else {

            $_SESSION["error"] = "No se pudo registrar el movimiento.";

        }

        header("Location: ../vistas/movimientos/index.php?id=" . $id_material);

        exit;

    }

}


/*
==========================
    INSTANCIA
==========================
*/

$controller = new MovimientoMaterialController();


/*
==========================
    ACCIONES
==========================
*/

$accion = $_GET["accion"] ?? "";

switch ($accion)
{

    case "agregar":

        $controller->agregar();

        break;

    default:

        header("Location: ../vistas/materiales/");
        exit;

}