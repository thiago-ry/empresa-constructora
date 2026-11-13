
<?php

require_once __DIR__ . "/../modelos/Cliente.php";
require_once __DIR__ . "/../modelos/Empleado.php";
require_once __DIR__ . "/../modelos/Presupuesto.php";
require_once __DIR__ . "/../modelos/Conexion.php";

class DashboardController
{
    public function obtenerDatos(): array
    {
        $cliente = new Cliente();
        $empleado = new Empleado();

        $conexionDB = new Conexion();
        $conexion = $conexionDB->conectar();

        $presupuesto = new Presupuesto($conexion);

        $estadisticasClientes = $cliente->obtenerEstadisticas();
        $estadisticasEmpleados = $empleado->obtenerEstadisticas();

        $clientes = $cliente->obtenerTodos();
        $empleados = $empleado->obtenerTodos();

        $presupuestos = $presupuesto->listar();

        return [
            "total_clientes" => (int)($estadisticasClientes["total_clientes"] ?? 0),
            "clientes_activos" => (int)($estadisticasClientes["activos"] ?? 0),

            "total_empleados" => (int)($estadisticasEmpleados["total"] ?? 0),
            "empleados_activos" => (int)($estadisticasEmpleados["activos"] ?? 0),

            "total_presupuestos" => count($presupuestos),

            "clientes" => array_slice($clientes, 0, 5),
            "empleados" => array_slice($empleados, 0, 5),
            "presupuestos" => array_slice($presupuestos, 0, 5)
        ];
    }
}
