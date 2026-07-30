<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {

    header("Location: /empresa_constructora/vistas/login.php");

    exit();

}

date_default_timezone_set("America/Argentina/Buenos_Aires");

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BUILDPRO</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    <link rel="stylesheet" href="/empresa_constructora/assets/css/layout.css">

    <link rel="stylesheet" href="/empresa_constructora/assets/css/components.css">


</head>


<body>


<header class="navbar no-print">


    <button class="menu-toggle" id="menuToggle">

        <i class="fa-solid fa-bars"></i>

    </button>



    <div class="logo" style=" width: 250px;">

        <span><img src="../../assets/img/logo.png" width="85px" style="margin-left: 60%;"></span>

    </div>




    <div class="navbar-right">


        <div class="user">


            <div class="avatar">

                <?= strtoupper(substr($_SESSION['usuario']['nombre'],0,1)); ?>

            </div>



            <div class="user-info">


                <h4>

                    <?= $_SESSION['usuario']['nombre']." ".$_SESSION['usuario']['apellido']; ?>

                </h4>


                <p>

                    <?= $_SESSION['usuario']['rol']; ?>

                </p>


            </div>


        </div>



        <a 
        href="/empresa_constructora/controladores/LogoutController.php"
        class="logout">


            <i class="fa-solid fa-right-from-bracket"></i>


        </a>



    </div>


</header>



<div class="sidebar-overlay" id="sidebarOverlay"></div>


<div class="container">