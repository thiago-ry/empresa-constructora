<?php

require_once __DIR__ . '/../modelos/Presupuesto.php';

class PresupuestoController
{
    private Presupuesto $modelo;

    public function __construct(PDO $db)
    {
        $this->modelo = new Presupuesto($db);
    }

    public function listar(): array
    {
        return $this->modelo->listar();
    }

    public function obras(): array
    {
        return $this->modelo->listarObras();
    }

    public function materiales(): array
    {
        return $this->modelo->listarMaterialesActivos();
    }

    public function buscarMateriales(string $busqueda = ''): array
    {
        return $this->modelo->buscarMateriales($busqueda);
    }

    public function materialesDeObra(int $idObra): array
    {
        return $this->modelo->materialesDeObra($idObra);
    }

    public function rubros(): array
    {
        return $this->modelo->listarRubros();
    }

    public function guardar(array $datos): int
    {
        return $this->modelo->crear($datos);
    }

    public function obtener(int $id): ?array
    {
        return $this->modelo->obtener($id);
    }
}