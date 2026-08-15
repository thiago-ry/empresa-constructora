<?php

require_once __DIR__ . "/../../config/permisos.php";

verificarPermiso("reportes");

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";

?>

<main class="content">


    <!-- =========================================================
         TÍTULO
    ========================================================== -->

    <div class="page-title no-print">

        <h1>
            Reportes
        </h1>

        <p>
            Consulte y genere informes sobre la información de la empresa.
        </p>

    </div>



    <!-- =========================================================
         CONTENEDOR
    ========================================================== -->

    <div class="table-container">


        <!-- =====================================================
             ENCABEZADO
        ====================================================== -->

        <div class="table-header">

            <div>

                <h2>
                    <i class="fa-solid fa-chart-column"></i>
                    Centro de reportes
                </h2>

                <small>
                    Seleccione el reporte que desea consultar.
                </small>

            </div>

        </div>



        <!-- =====================================================
             REPORTES
        ====================================================== -->

        <div class="card-grid reportes-grid">


            <!-- =================================================
                 REPORTE DE OBRAS
            ================================================== -->

            <a
                href="/empresa_constructora/vistas/reportes/obras.php"
                class="card reporte-card"
            >

                <div class="reporte-icon">

                    <i class="fa-solid fa-building"></i>

                </div>


                <div class="reporte-content">

                    <h3>
                        Obras
                    </h3>

                    <p>
                        Consulte información general de las obras,
                        estados, avances, fechas y estadísticas.
                    </p>

                </div>


                <div class="reporte-footer">

                    <span>
                        Ver reporte
                    </span>

                    <i class="fa-solid fa-arrow-right"></i>

                </div>

            </a>



            <!-- =================================================
                 REPORTE DE EMPLEADOS
            ================================================== -->

            <a
                href="/empresa_constructora/vistas/reportes/empleados.php"
                class="card reporte-card"
            >

                <div class="reporte-icon">

                    <i class="fa-solid fa-users"></i>

                </div>


                <div class="reporte-content">

                    <h3>
                        Empleados
                    </h3>

                    <p>
                        Consulte el personal, cargos, estado,
                        obras asignadas y otra información laboral.
                    </p>

                </div>


                <div class="reporte-footer">

                    <span>
                        Ver reporte
                    </span>

                    <i class="fa-solid fa-arrow-right"></i>

                </div>

            </a>



            <!-- =================================================
                 REPORTE DE MATERIALES
            ================================================== -->

            <a
                href="/empresa_constructora/vistas/reportes/materiales.php"
                class="card reporte-card"
            >

                <div class="reporte-icon">

                    <i class="fa-solid fa-boxes-stacked"></i>

                </div>


                <div class="reporte-content">

                    <h3>
                        Materiales
                    </h3>

                    <p>
                        Consulte stock, materiales disponibles,
                        stock mínimo y materiales críticos.
                    </p>

                </div>


                <div class="reporte-footer">

                    <span>
                        Ver reporte
                    </span>

                    <i class="fa-solid fa-arrow-right"></i>

                </div>

            </a>



            <!-- =================================================
                 REPORTE DE HERRAMIENTAS
            ================================================== -->

            <a
                href="/empresa_constructora/vistas/reportes/herramientas.php"
                class="card reporte-card"
            >

                <div class="reporte-icon">

                    <i class="fa-solid fa-screwdriver-wrench"></i>

                </div>


                <div class="reporte-content">

                    <h3>
                        Herramientas
                    </h3>

                    <p>
                        Consulte herramientas, estados,
                        asignaciones y disponibilidad.
                    </p>

                </div>


                <div class="reporte-footer">

                    <span>
                        Ver reporte
                    </span>

                    <i class="fa-solid fa-arrow-right"></i>

                </div>

            </a>



            <!-- =================================================
                 REPORTE DE CLIENTES
            ================================================== -->

            <a
                href="/empresa_constructora/vistas/reportes/clientes.php"
                class="card reporte-card"
            >

                <div class="reporte-icon">

                    <i class="fa-solid fa-user-tie"></i>

                </div>


                <div class="reporte-content">

                    <h3>
                        Clientes
                    </h3>

                    <p>
                        Consulte los clientes registrados y
                        las obras asociadas a cada uno.
                    </p>

                </div>


                <div class="reporte-footer">

                    <span>
                        Ver reporte
                    </span>

                    <i class="fa-solid fa-arrow-right"></i>

                </div>

            </a>



            <!-- =================================================
                 REPORTE DE USUARIOS
            ================================================== -->

            <a
                href="/empresa_constructora/vistas/reportes/usuarios.php"
                class="card reporte-card"
            >

                <div class="reporte-icon">

                    <i class="fa-solid fa-user-gear"></i>

                </div>


                <div class="reporte-content">

                    <h3>
                        Usuarios
                    </h3>

                    <p>
                        Consulte usuarios, roles, estados y
                        información de acceso al sistema.
                    </p>

                </div>


                <div class="reporte-footer">

                    <span>
                        Ver reporte
                    </span>

                    <i class="fa-solid fa-arrow-right"></i>

                </div>

            </a>



            <!-- =================================================
                 REPORTE DE AUDITORÍA
            ================================================== -->

            <a
                href="/empresa_constructora/vistas/reportes/auditoria.php"
                class="card reporte-card"
            >

                <div class="reporte-icon">

                    <i class="fa-solid fa-clock-rotate-left"></i>

                </div>


                <div class="reporte-content">

                    <h3>
                        Auditoría
                    </h3>

                    <p>
                        Consulte las acciones realizadas por los
                        usuarios dentro del sistema.
                    </p>

                </div>


                <div class="reporte-footer">

                    <span>
                        Ver reporte
                    </span>

                    <i class="fa-solid fa-arrow-right"></i>

                </div>

            </a>



            <!-- =================================================
                 REPORTE DE ASIGNACIONES
            ================================================== -->

            <a
                href="/empresa_constructora/vistas/reportes/asignaciones.php"
                class="card reporte-card"
            >

                <div class="reporte-icon">

                    <i class="fa-solid fa-people-arrows"></i>

                </div>


                <div class="reporte-content">

                    <h3>
                        Asignaciones
                    </h3>

                    <p>
                        Consulte la distribución de empleados,
                        herramientas y recursos en las obras.
                    </p>

                </div>


                <div class="reporte-footer">

                    <span>
                        Ver reporte
                    </span>

                    <i class="fa-solid fa-arrow-right"></i>

                </div>

            </a>



        </div>


    </div>


</main>



<!-- =============================================================
     ESTILOS ESPECÍFICOS DE REPORTES
============================================================= -->

<style>

    .reportes-grid {

        grid-template-columns:
            repeat(auto-fit, minmax(260px, 1fr));

        gap: 20px;

        margin-top: 20px;

    }


    .reporte-card {

        text-decoration: none;

        color: inherit;

        display: flex;

        flex-direction: column;

        min-height: 245px;

        cursor: pointer;

        transition:
            transform .2s ease,
            box-shadow .2s ease;

    }


    .reporte-card:hover {

        transform: translateY(-4px);

    }


    .reporte-icon {

        width: 52px;

        height: 52px;

        border-radius: 12px;

        display: flex;

        align-items: center;

        justify-content: center;

        margin-bottom: 18px;

        font-size: 22px;

        background: rgba(255,255,255,.07);

    }


    .reporte-content {

        flex: 1;

    }


    .reporte-content h3 {

        margin: 0 0 10px;

        font-size: 19px;

    }


    .reporte-content p {

        margin: 0;

        line-height: 1.6;

        opacity: .7;

        font-size: 14px;

    }


    .reporte-footer {

        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-top: 22px;

        padding-top: 15px;

        border-top: 1px solid rgba(255,255,255,.08);

        font-size: 14px;

        font-weight: 600;

    }


    .reporte-footer i {

        transition: transform .2s ease;

    }


    .reporte-card:hover .reporte-footer i {

        transform: translateX(5px);

    }


    @media (max-width: 700px) {

        .reportes-grid {

            grid-template-columns: 1fr;

        }

    }

</style>



<?php

require_once __DIR__ . "/../../layouts/footer.php";

?>