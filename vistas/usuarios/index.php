<?php

require_once "../../modelos/Usuario.php";
require_once "../../modelos/Rol.php";

$usuario = new Usuario();
$rol = new Rol();

$usuarios = $usuario->obtenerTodos();
$roles = $rol->obtenerTodos();


require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>


<main class="content">


    <div class="page-title no-print">

        <h1>Usuarios</h1>

        <p>
            Administración de usuarios registrados.
        </p>

    </div>



    <div class="table-container">


        <!-- SOLO SE MUESTRA AL IMPRIMIR -->

        <div class="print-header">


            <h1>
                Empresa Constructora
            </h1>


            <h2>
                Reporte de Usuarios
            </h2>


            <p>
                Listado de usuarios registrados en el sistema.
            </p>


            <p>
                Fecha de generación:
                <?= date("d/m/Y H:i"); ?>
                <br>
                Generado por:
                <?= $_SESSION["usuario"]["nombre"]." ".$_SESSION["usuario"]["apellido"]; ?> 
            </p>


        </div>



        <div class="toolbar no-print">


            <div class="toolbar-left">


                <input
                    type="text"
                    id="buscarUsuario"
                    class="search-box"
                    placeholder="Buscar usuario...">



                <select class="filter" id="filtroRol">


                    <option value="">
                        Todos los roles
                    </option>


                    <?php foreach ($roles as $r) { ?>


                        <option value="<?= $r['nombre_rol']; ?>">

                            <?= $r['nombre_rol']; ?>

                        </option>


                    <?php } ?>


                </select>



                <select id="filtroEstado" class="filter">


                    <option value="">
                        Todos los estados
                    </option>


                    <option value="activo">
                        Activo
                    </option>


                    <option value="inactivo">
                        Inactivo
                    </option>


                </select>
            </div>
            <div>
                <button
                    onclick="window.print()"
                    class="btn btn-primary">
                    <i class="fa-solid fa-print"></i>
                </button>
                <a
                    href="agregar.php"
                    class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>
        </div>
        <table class="table" id="tablaUsuarios">
            <thead>
                <tr>
                    <th>
                        Nombre
                    </th>
                    <th>
                        Correo
                    </th>
                    <th>
                        Rol
                    </th>
                    <th>
                        Estado
                    </th>
                    <th class="no-print">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u) { ?>
                    <tr>
                        <td>
                            <?= $u["nombre"] . " " . $u["apellido"]; ?>
                        </td>
                        <td>
                            <?= $u["correo"]; ?>
                        </td>
                        <td>
                            <?= $u["nombre_rol"]; ?>
                        </td>
                        <td>
                            <span class="<?= $u['estado'] == 1 ? 'badge badge-success' : 'badge badge-danger' ?>">
                                <?= $u['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="no-print">
                            <a href="editar.php?id=<?= $u['id_usuario']; ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <?php if ($u["estado"] == 1) { ?>
                                <a
                                    href="../../controladores/UsuarioController.php?accion=baja&id=<?= $u['id_usuario']; ?>"
                                    onclick="return confirm('¿Está seguro que desea dar de baja este usuario?');">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            <?php } else { ?>
                                <a
                                    href="../../controladores/UsuarioController.php?accion=activar&id=<?= $u['id_usuario']; ?>">
                                    <i class="fa-solid fa-check link-accionA"></i>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>
<?php
$script = "usuarios";
require_once "../../layouts/footer.php";
?>