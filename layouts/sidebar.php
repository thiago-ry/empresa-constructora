<?php
require_once __DIR__."/../config/menu.php";

$rol=$_SESSION['usuario']['rol'];
?>

<aside class="sidebar">

<?php foreach($menu[$rol] as $item){ ?>

<a href="<?= $item[1] ?>">
<?= $item[0] ?>
</a>

<?php } ?>

</aside>