</head>

<body>

<header class="navbar">

<div class="logo">
BUILD<span>PRO</span>
</div>

<div class="navbar-right">

<div class="user">

<div class="avatar">
<?= strtoupper(substr($_SESSION['usuario']['nombre'],0,1)) ?>
</div>

<div class="user-info">
<h4><?= $_SESSION['usuario']['nombre']." ".$_SESSION['usuario']['apellido'] ?></h4>
<p><?= $_SESSION['usuario']['rol'] ?></p>
</div>

</div>

<a href="../../controladores/LogoutController.php" class="logout">
Cerrar sesión
</a>

</div>

</header>