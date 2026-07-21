<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/Material.php";


class MaterialController
{

    private $auditoria;
    private $material;

    public function __construct()
    {
        $this->auditoria = new Auditoria();
        $this->material = new Material();
    }

    public function agregar()
    {
        session_start();
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../vistas/materiales/");
            exit;
        }

        $nombre_material = trim($_POST["nombre_material"]);
        $descripcion = trim($_POST["descripcion"]);
        $stock = $_POST["stock"];
        $stock_minimo = $_POST["stock_minimo"];
        $unidad_medida = trim($_POST["unidad_medida"]);
        $estado = $_POST["estado"];

        if (
            empty($nombre_material) ||
            empty($unidad_medida) ||
            $stock === "" ||
            $stock_minimo === ""
        ) {
            $_SESSION["error"] = "Complete todos los campos obligatorios.";
            header("Location: ../vistas/materiales/create.php");
            exit;
        }

        if (!is_numeric($stock) || $stock < 0) {
            $_SESSION["error"] = "El stock es inválido.";
            header("Location: ../vistas/materiales/create.php");
            exit;
        }

        if (!is_numeric($stock_minimo) || $stock_minimo < 0) {
            $_SESSION["error"] = "El stock mínimo es inválido.";
            header("Location: ../vistas/materiales/create.php");
            exit;
        }

        $datos = [
            "nombre_material" => $nombre_material,
            "descripcion" => $descripcion,
            "stock" => $stock,
            "stock_minimo" => $stock_minimo,
            "unidad_medida" => $unidad_medida,
            "estado" => $estado
        ];

        if ($this->material->agregar($datos)) {
            $this->auditoria->registrar([
                "id_usuario" => $_SESSION["usuario"]["id"],
                "accion" => "INSERTAR",
                "tabla_afectada" => "material",
                "id_registro" => null,
                "descripcion" =>
                "Se registró el material: "
                    . strtoupper($nombre_material)
            ]);
            $_SESSION["success"] = "Material registrado correctamente.";
        } else {
            $_SESSION["error"] = "No se pudo registrar el material.";
        }

        header("Location: ../vistas/materiales/");
        exit;
    }

    public function editar()
    {
        session_start();
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../vistas/materiales/");
            exit;
        }

        $id_material = $_POST["id_material"] ?? null;
        $nombre_material = trim($_POST["nombre_material"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");
        $stock = $_POST["stock"] ?? "";
        $stock_minimo = $_POST["stock_minimo"] ?? "";
        $unidad_medida = trim($_POST["unidad_medida"] ?? "");
        $estado = $_POST["estado"] ?? 1;

        if (
            empty($id_material) ||
            empty($nombre_material) ||
            empty($unidad_medida) ||
            $stock === "" ||
            $stock_minimo === ""
        ) {
            $_SESSION["error"] = "Complete todos los campos obligatorios.";

            header(
                "Location: ../vistas/materiales/editar.php?id=" . $id_material
            );
            exit;
        }

        if (!is_numeric($stock) || $stock < 0) {
            $_SESSION["error"] = "El stock ingresado no es válido.";

            header(
                "Location: ../vistas/materiales/editar.php?id=" . $id_material
            );
            exit;
        }

        if (!is_numeric($stock_minimo) || $stock_minimo < 0) {
            $_SESSION["error"] = "El stock mínimo ingresado no es válido.";

            header(
                "Location: ../vistas/materiales/editar.php?id=" . $id_material
            );
            exit;
        }

        $datos = [
            "id_material" => $id_material,
            "nombre_material" => $nombre_material,
            "descripcion" => $descripcion,
            "stock" => $stock,
            "stock_minimo" => $stock_minimo,
            "unidad_medida" => $unidad_medida,
            "estado" => $estado
        ];

        if ($this->material->editar($datos)) {
            $this->auditoria->registrar([
                "id_usuario" => $_SESSION["usuario"]["id"],
                "accion" => "EDITAR",
                "tabla_afectada" => "MATERIAL",
                "id_registro" => $id_material,
                "descripcion" =>
                "Se editó el material: "
                    . strtoupper($nombre_material)
            ]);

            $_SESSION["success"] =
                "Material actualizado correctamente.";
        } else {
            $_SESSION["error"] =
                "No se pudo actualizar el material.";
        }

        header(
            "Location: ../vistas/materiales/"
        );
        exit;
    }

    public function eliminar()
    {
        session_start();
        $id_material = $_GET["id"] ?? 0;

        if (empty($id_material)) {
            $_SESSION["error"] = "Material inválido.";
            header("Location: ../vistas/materiales/");
            exit;
        }

        if ($this->material->eliminar($id_material)) {
            $this->auditoria->registrar([
                "id_usuario" => $_SESSION["usuario"]["id"],
                "accion" => "BAJA",
                "tabla_afectada" => "MATERIAL",
                "id_registro" => $id_material,
                "descripcion" =>
                "Se dio de baja el material ID: "
                    . $id_material
            ]);
            $_SESSION["success"] = "Material dado de baja correctamente.";
        } else {
            $_SESSION["error"] = "No se pudo dar de baja el material.";
        }
        header("Location: ../vistas/materiales/");
        exit;
    }

    public function activar()
    {
        session_start();
        $id_material = $_GET["id"] ?? 0;

        if (empty($id_material)) {
            $_SESSION["error"] = "Material inválido.";
            header("Location: ../vistas/materiales/");
            exit;
        }

        if ($this->material->activar($id_material)) {
            $this->auditoria->registrar([
                "id_usuario" => $_SESSION["usuario"]["id"],
                "accion" => "ACTIVAR",
                "tabla_afectada" => "MATERIAL",
                "id_registro" => $id_material,
                "descripcion" =>
                "Se activó el material ID: "
                    . $id_material
            ]);

            $_SESSION["success"] = "Material activado correctamente.";
        } else {
            $_SESSION["error"] = "No se pudo activar el material.";
        }

        header("Location: ../vistas/materiales/");
        exit;
    }
}

$controller = new MaterialController();

$accion = $_GET["accion"] ?? "";

switch ($accion) {

    case "agregar":
        $controller->agregar();
        break;

    case "editar":
        $controller->editar();
        break;

    case "eliminar":
        $controller->eliminar();
        break;

    case "activar":
        $controller->activar();
        break;

    default:
        header("Location: ../vistas/materiales/");
        exit;
}
