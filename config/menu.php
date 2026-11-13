<?php

$menu = [

    "Gerente" => [
        ["fa-solid fa-house", "Dashboard", "/empresa_constructora/vistas/dashboard/gerente.php"],
        ["fa-solid fa-users", "Usuarios", "/empresa_constructora/vistas/usuarios/"],
        ["fa-solid fa-user-group", "Clientes", "/empresa_constructora/vistas/clientes/"],
        ["fa-solid fa-hard-hat", "Empleados", "/empresa_constructora/vistas/empleados/"],
        ["fa-solid fa-building", "Obras", "/empresa_constructora/vistas/obras/"],
        ["fa-solid fa-boxes-stacked", "Materiales", "/empresa_constructora/vistas/materiales/"],
        ["fa-solid fa-clipboard-list", "Solicitudes", "/empresa_constructora/controladores/SolicitudMaterialController.php?accion=gerente"],
        ["fa-solid fa-screwdriver-wrench", "Herramientas", "/empresa_constructora/vistas/herramientas/"],
        ["fa-solid fa-clipboard-list", "Solicitudes de herramientas", "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"],
        ["fa-solid fa-chart-column", "Reportes", "/empresa_constructora/vistas/reportes/"],
        ["fa-solid fa-file-invoice-dollar", "Presupuestos", "/empresa_constructora/vistas/presupuestos/"],
        ["fa-solid fa-user", "Perfil", "/empresa_constructora/vistas/perfil/"]
    ],

    "Administrativo" => [
        ["fa-solid fa-house", "Dashboard", "/empresa_constructora/vistas/dashboard/administrativo.php"],
        ["fa-solid fa-user-group", "Clientes", "/empresa_constructora/vistas/clientes/"],
        ["fa-solid fa-hard-hat", "Empleados", "/empresa_constructora/vistas/empleados/"],
        ["fa-solid fa-file-invoice-dollar", "Presupuestos", "/empresa_constructora/vistas/presupuestos/"],
        ["fa-solid fa-cash-register", "Caja", "/empresa_constructora/vistas/caja/"],
        ["fa-solid fa-user", "Perfil", "/empresa_constructora/vistas/perfil/"]
    ],

    "Jefe de Obra" => [
        ["fa-solid fa-house", "Dashboard", "/empresa_constructora/vistas/dashboard/jefe_obra.php"],
        ["fa-solid fa-building", "Obras", "/empresa_constructora/vistas/obras/"],
        ["fa-solid fa-chart-line", "Avances", "/empresa_constructora/vistas/avances/"],
        ["fa-solid fa-list-check", "Tareas", "/empresa_constructora/vistas/tareas/"],
        ["fa-solid fa-file-invoice-dollar", "Presupuestos", "/empresa_constructora/vistas/presupuestos/"],
        ["fa-solid fa-triangle-exclamation", "Incidencias", "/empresa_constructora/vistas/incidencias/"],
        ["fa-solid fa-user", "Perfil", "/empresa_constructora/vistas/perfil/"]
    ],

    "Capataz" => [
        ["fa-solid fa-house", "Dashboard", "/empresa_constructora/controladores/DashboardCapatazController.php"],
        ["fa-solid fa-building", "Mi Obra", "/empresa_constructora/vistas/obras/"],
        ["fa-solid fa-users", "Empleados", "/empresa_constructora/vistas/empleado_obra/"],
        ["fa-solid fa-clock-rotate-left", "Historial", "/empresa_constructora/vistas/asistencia/historial.php"],
        ["fa-solid fa-chart-line", "Avances", "/empresa_constructora/controladores/AvanceController.php?accion=listar"],
        ["fa-solid fa-list-check","Tareas", "/empresa_constructora/vistas/tareas/index.php"],
        ["fa-solid fa-triangle-exclamation", "Incidencias", "/empresa_constructora/vistas/incidencias/capataz.php"],
["fa-solid fa-screwdriver-wrench", "Solicitar herramientas", "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=crear"],
        ["fa-solid fa-user", "Perfil", "/empresa_constructora/vistas/perfil/"]
    ],

    "Encargado de Depósito" => [
        ["fa-solid fa-house", "Dashboard", "/empresa_constructora/vistas/dashboard/deposito.php"],
        ["fa-solid fa-boxes-stacked", "Materiales", "/empresa_constructora/vistas/materiales/"],
        ["fa-solid fa-clipboard-list", "Solicitudes", "/empresa_constructora/controladores/EntregaMaterialController.php?accion=listar"],
        ["fa-solid fa-screwdriver-wrench", "Herramientas", "/empresa_constructora/vistas/herramientas/"],
        ["fa-solid fa-warehouse", "Inventario", "/empresa_constructora/vistas/inventario/"],
["fa-solid fa-screwdriver-wrench", "Solicitudes de herramientas", "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"],
        ["fa-solid fa-user", "Perfil", "/empresa_constructora/vistas/perfil/"]
    ],

    "Empleado" => [
        ["fa-solid fa-house", "Dashboard", "/empresa_constructora/vistas/dashboard/empleado.php"],
        ["fa-solid fa-list-check", "Mis tareas", "/empresa_constructora/vistas/tareas/mis_tareas.php"],
        ["fa-solid fa-clock", "Asistencia", "/empresa_constructora/vistas/asistencia/asistencia_personal.php"],
        ["fa-solid fa-user", "Perfil", "/empresa_constructora/vistas/perfil/"]
    ],

"Cliente" => [
    ["fa-solid fa-house", "Inicio", "/empresa_constructora/controladores/PortalClienteController.php?accion=dashboard"],
    ["fa-solid fa-building", "Mi obra", "/empresa_constructora/controladores/PortalClienteController.php?accion=obra"],
    ["fa-solid fa-chart-line", "Avances", "/empresa_constructora/controladores/PortalClienteController.php?accion=avances"],
    ["fa-solid fa-list-check", "Etapas", "/empresa_constructora/controladores/PortalClienteController.php?accion=etapas"],
    ["fa-solid fa-camera", "Fotografías", "/empresa_constructora/controladores/PortalClienteController.php?accion=fotos"],
    ["fa-solid fa-folder-open", "Documentación", "/empresa_constructora/controladores/PortalClienteController.php?accion=documentos"],
    ["fa-solid fa-users", "Equipo de trabajo", "/empresa_constructora/controladores/PortalClienteController.php?accion=equipo"],
    ["fa-solid fa-file-invoice-dollar", "Presupuesto", "/empresa_constructora/controladores/PortalClienteController.php?accion=presupuesto"],
    ["fa-solid fa-user", "Perfil", "/empresa_constructora/vistas/perfil/"]
],

];
