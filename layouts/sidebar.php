<?php

require_once __DIR__ . "/../config/menu.php";

$rol = $_SESSION['usuario']['rol'];

$rutaActual = $_SERVER['REQUEST_URI'];

?>

<aside class="sidebar">

    <?php foreach ($menu[$rol] as $item) {

        $activo = strpos($rutaActual, $item[2]) !== false;

    ?>

        <a
            href="<?= $item[2] ?>"
            class="<?= $activo ? 'active' : '' ?>">

            <i class="<?= $item[0] ?>"></i>

            <span>
                <?= $item[1] ?>
            </span>

        </a>

    <?php } ?>

</aside>