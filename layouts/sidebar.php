<?php
require_once __DIR__."/../config/menu.php";
$rol=$_SESSION['usuario']['rol'];
?>

<aside class="sidebar">

<?php foreach($menu[$rol] as $item){ ?>

<a href="<?= $item[2] ?>">
<i class="<?= $item[0] ?>"></i>
<span><?= $item[1] ?></span>
</a>

<?php } ?>

</aside>