<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/EmpleadoObra.php";
require_once "../modelos/Usuario.php";

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
    ==========================================================
        LISTAR
    ==========================================================
    */

    public function listar()
    {
        session_start();

        $id_obra = $_GET["id_obra"] ?? 0;

        $empleados =
            $this->empleadoObra->obtenerPorObra($id_obra);

        require "../vistas/obras/empleados/index.php";
    }


    /*
    ==========================================================
        CREAR
    ==========================================================
    */

    public function crear()
    {
        session_start();

        $id_obra = $_GET["id_obra"] ?? 0;

        require "../vistas/obras/empleados/crear.php";
    }


    /*
    ==========================================================
        BUSCAR EMPLEADOS
    ==========================================================
    */

    public function buscarEmpleados()
    {
        session_start();

        header(
            "Content-Type: application/json; charset=UTF-8"
        );

        $id_obra = $_GET["id_obra"] ?? 0;
        $busqueda = $_GET["busqueda"] ?? "";

        if (empty($id_obra)) {

            echo json_encode([
                "success" => false,
                "mensaje" => "No se especificó la obra."
            ]);

            exit();
        }


        $usuario = new Usuario();

        $empleados =
            $usuario->obtenerEmpleadosDisponiblesPorObra(
                $id_obra,
                $busqueda
            );


        echo json_encode([
            "success" => true,
            "empleados" => $empleados
        ]);

        exit();
    }


    /*
    ==========================================================
        OBTENER CARGOS
    ==========================================================
    */

    public function obtenerCargos()
    {
        session_start();

        header(
            "Content-Type: application/json; charset=UTF-8"
        );

        $id_usuario =
            $_GET["id_usuario"] ?? 0;


        if (empty($id_usuario)) {

            echo json_encode([
                "success" => false,
                "mensaje" => "No se especificó el empleado."
            ]);

            exit();
        }


        $cargos =
            $this->empleadoObra->obtenerCargosEmpleado(
                $id_usuario
            );


        echo json_encode([
            "success" => true,
            "cargos" => $cargos
        ]);

        exit();
    }


    /*
    ==========================================================
        AGREGAR
    ==========================================================
    */

    public function agregar()
    {
        session_start();

        $id_usuario =
            $_POST["id_usuario"] ?? 0;

        $id_obra =
            $_POST["id_obra"] ?? 0;

        $fecha_ingreso =
            $_POST["fecha_ingreso"] ?? "";

        $observaciones =
            $_POST["observaciones"] ?? "";

        $id_cargo =
            $_POST["id_cargo"] ?? 0;


        if (
            empty($id_usuario) ||
            empty($id_obra) ||
            empty($id_cargo) ||
            empty($fecha_ingreso)
        ) {

            header(
                "Location: ../vistas/obras/empleados/index.php?id_obra="
                . $id_obra
                . "&error=datos"
            );

            exit();
        }


        if (
            $this->empleadoObra->existeEmpleadoActivo(
                $id_usuario,
                $id_obra
            )
        ) {

            header(
                "Location: ../vistas/obras/empleados/index.php?id_obra="
                . $id_obra
                . "&error=duplicado"
            );

            exit();
        }


        $datos = [

            "id_usuario" => $id_usuario,

            "id_obra" => $id_obra,

            "id_cargo" => $id_cargo,

            "fecha_ingreso" => $fecha_ingreso,

            "observaciones" => $observaciones
        ];


        $resultado =
            $this->empleadoObra->asignar($datos);


        if ($resultado) {

            $this->auditoria->registrar([

                "id_usuario" =>
                    $_SESSION["usuario"]["id"],

                "accion" =>
                    "INSERTAR",

                "tabla_afectada" =>
                    "empleado_obra",

                "id_registro" =>
                    $id_usuario,

                "descripcion" =>
                    "Asignó un empleado a una obra"
            ]);
        }


        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $id_obra
        );

        exit();
    }


    /*
    ==========================================================
        EDITAR
    ==========================================================
    */

    public function editar()
    {
        session_start();

        $datos = [

            "id_empleado_obra" =>
                $_POST["id_empleado_obra"],

            "fecha_ingreso" =>
                $_POST["fecha_ingreso"],

            "observaciones" =>
                $_POST["observaciones"]
        ];


        $this->empleadoObra->editar($datos);


        $this->auditoria->registrar([

            "id_usuario" =>
                $_SESSION["usuario"]["id"],

            "accion" =>
                "EDITAR",

            "tabla_afectada" =>
                "empleado_obra",

            "id_registro" =>
                $_POST["id_empleado_obra"],

            "descripcion" =>
                "Editó una asignación de empleado"
        ]);


        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $_POST["id_obra"]
        );

        exit();
    }


    /*
    ==========================================================
        RETIRAR
    ==========================================================
    */

    public function retirar()
    {
        session_start();


        $id_empleado_obra =
            $_POST["id_empleado_obra"] ?? 0;

        $id_obra_actual =
            $_POST["id_obra"] ?? 0;

        $id_usuario =
            $_POST["id_usuario"] ?? 0;

        $fecha_egreso =
            $_POST["fecha_egreso"] ?? "";

        $motivo_egreso =
            trim($_POST["motivo_egreso"] ?? "");

        $observaciones =
            trim($_POST["observaciones"] ?? "");

        $alcance =
            $_POST["alcance"] ?? "actual";

        $obras_seleccionadas =
            $_POST["obras_seleccionadas"] ?? [];


        /*
        ======================================================
            VALIDACIONES
        ======================================================
        */

        if (
            empty($id_empleado_obra) ||
            empty($id_obra_actual) ||
            empty($id_usuario) ||
            empty($fecha_egreso) ||
            empty($motivo_egreso)
        ) {

            header(
                "Location: ../vistas/obras/empleados/retirar.php?id="
                . $id_empleado_obra
                . "&error=datos"
            );

            exit();
        }


        /*
        ======================================================
            TRANSACCIÓN
        ======================================================
        */

        $conexion =
            $this->empleadoObra->getConexion();


        try {

            $conexion->beginTransaction();


            /*
            ==============================================
                SOLO ESTA OBRA
            ==============================================
            */

            if ($alcance === "actual") {

                $resultado =
                    $this->empleadoObra->retirar([

                        "id_empleado_obra" =>
                            $id_empleado_obra,

                        "fecha_egreso" =>
                            $fecha_egreso,

                        "motivo_egreso" =>
                            $motivo_egreso,

                        "observaciones" =>
                            $observaciones
                    ]);


                if (!$resultado) {

                    throw new Exception(
                        "No se pudo retirar al empleado."
                    );
                }
            }


            /*
            ==============================================
                TODAS LAS OBRAS
            ==============================================
            */

            elseif ($alcance === "todas") {

                $resultado =
                    $this->empleadoObra
                        ->retirarDeTodasLasObras(
                            $id_usuario,
                            $fecha_egreso,
                            $motivo_egreso,
                            $observaciones
                        );


                if (!$resultado) {

                    throw new Exception(
                        "No se pudieron retirar las obras."
                    );
                }
            }


            /*
            ==============================================
                OBRAS SELECCIONADAS
            ==============================================
            */

            elseif ($alcance === "seleccionadas") {

                $ids = array_map(
                    "intval",
                    $obras_seleccionadas
                );


                /*
                ------------------------------------------
                    SIEMPRE INCLUIR LA OBRA ACTUAL
                ------------------------------------------
                */

                if (
                    !in_array(
                        (int)$id_obra_actual,
                        $ids
                    )
                ) {

                    $ids[] =
                        (int)$id_obra_actual;
                }


                $resultado =
                    $this->empleadoObra
                        ->retirarDeObrasSeleccionadas(

                            $id_usuario,

                            $ids,

                            $fecha_egreso,

                            $motivo_egreso,

                            $observaciones
                        );


                if (!$resultado) {

                    throw new Exception(
                        "No se pudieron retirar las obras seleccionadas."
                    );
                }
            }


            else {

                throw new Exception(
                    "Tipo de retiro inválido."
                );
            }


            /*
            ==================================================
                AUDITORÍA
            ==================================================
            */

            if ($alcance === "actual") {

                $descripcion =
                    "Retiró al empleado de la obra actual. "
                    . "Motivo: "
                    . $motivo_egreso;
            }

            elseif ($alcance === "todas") {

                $descripcion =
                    "Retiró al empleado de todas sus obras activas. "
                    . "Motivo: "
                    . $motivo_egreso;
            }

            else {

                $descripcion =
                    "Retiró al empleado de las obras seleccionadas. "
                    . "Motivo: "
                    . $motivo_egreso;
            }


            $this->auditoria->registrar([

                "id_usuario" =>
                    $_SESSION["usuario"]["id"],

                "accion" =>
                    "EDITAR",

                "tabla_afectada" =>
                    "empleado_obra",

                "id_registro" =>
                    $id_empleado_obra,

                "descripcion" =>
                    $descripcion
            ]);


            $conexion->commit();


            header(
                "Location: ../vistas/obras/empleados/index.php?id_obra="
                . $id_obra_actual
                . "&success=retirado"
            );

            exit();


        } catch (Exception $e) {

            if ($conexion->inTransaction()) {

                $conexion->rollBack();

            }


            header(
                "Location: ../vistas/obras/empleados/retirar.php?id="
                . $id_empleado_obra
                . "&error=retiro"
            );

            exit();
        }
    }
    public function activar()
{
    session_start();

    $id_empleado_obra = $_GET["id"] ?? 0;
    $id_obra = $_GET["id_obra"] ?? 0;

    if (empty($id_empleado_obra) || empty($id_obra)) {

        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $id_obra
            . "&error=datos"
        );

        exit();
    }


    /*
    ==================================================
        BUSCAR ASIGNACIÓN
    ==================================================
    */

    $empleado = $this->empleadoObra->buscarPorId(
        $id_empleado_obra
    );


    if (!$empleado) {

        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $id_obra
            . "&error=no_encontrado"
        );

        exit();
    }


    /*
    ==================================================
        VERIFICAR SI YA ESTÁ ACTIVO
    ==================================================
    */

    if ((int)$empleado["estado"] === 1) {

        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $id_obra
            . "&error=ya_activo"
        );

        exit();
    }


    /*
    ==================================================
        REACTIVAR
    ==================================================
    */

    $resultado = $this->empleadoObra->activar(
        $id_empleado_obra
    );


    if (!$resultado) {

        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $id_obra
            . "&error=activar"
        );

        exit();
    }


    /*
    ==================================================
        AUDITORÍA
    ==================================================
    */

    $this->auditoria->registrar([

        "id_usuario" =>
            $_SESSION["usuario"]["id"],

        "accion" =>
            "ACTIVAR",

        "tabla_afectada" =>
            "empleado_obra",

        "id_registro" =>
            $id_empleado_obra,

        "descripcion" =>
            "Reactivó al empleado "
            . $empleado["nombre"]
            . " "
            . $empleado["apellido"]
            . " en una obra"

    ]);


    /*
    ==================================================
        VOLVER
    ==================================================
    */
    header(
        "Location: ../vistas/obras/empleados/index.php?id_obra="
        . $id_obra
        . "&success=reactivado"
    );
    exit();
}
}

$controlador =
    new EmpleadoObraController();

if (isset($_GET["accion"])) {
    switch ($_GET["accion"]) {
        case "listar":
            $controlador->listar();
            break;

        case "crear":
            $controlador->crear();
            break;

        case "buscarEmpleados":
            $controlador->buscarEmpleados();
            break;

        case "obtenerCargos":
            $controlador->obtenerCargos();
            break;

        case "activar":
            $controlador->activar();
            break;
    }
}


if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

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