<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BUILDPRO - Inicio de Sesión</title>

<style>
:root {
    --bg-primary: #070d14;
    --bg-surface: rgba(17, 30, 43, 0.55);
    --accent: #ffb703;
    --accent-hover: #fb8500;
    --text-muted: #94a3b8;
    --border: rgba(255,255,255,0.08);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Roboto, sans-serif;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background:
    linear-gradient(
        rgba(7,13,20,0.92),
        rgba(7,13,20,0.98)
    ),
    url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5') center/cover fixed;
    color: white;
}

.logo {
    position: absolute;
    top: 35px;
    left: 45px;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: 2px;
}

.logo span {
    color: var(--accent);
    text-shadow: 0 0 15px rgba(255,183,3,.5);
}

.login-card {
    width: 400px;
    padding: 40px;
    background: var(--bg-surface);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(0,0,0,.5);
}

.title {
    text-align: center;
    margin-bottom: 30px;
}

.title h1 {
    font-size: 28px;
}

.title p {
    margin-top: 8px;
    color: var(--text-muted);
    font-size: 14px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    color: var(--text-muted);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 7px;
}

input {
    width: 100%;
    padding: 13px 15px;
    background: rgba(7,13,20,.7);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 10px;
    color: white;
    outline: none;
    font-size: 14px;
}

input:focus {
    border-color: var(--accent);
}

button {
    width: 100%;
    padding: 14px;
    margin-top: 10px;
    background: var(--accent);
    color: #070d14;
    border: none;
    border-radius: 10px;
    font-weight: 800;
    text-transform: uppercase;
    cursor: pointer;
    transition: .3s;
}

button:hover {
    background: var(--accent-hover);
    transform: translateY(-2px);
}

.footer {
    text-align: center;
    margin-top: 25px;
    color: var(--text-muted);
    font-size: 12px;
}
</style>

</head>

<body>

<div class="logo">
    BUILD<span>PRO</span>
</div>

<div class="login-card">

    <div class="title">
        <h1>Inicio de Sesión</h1>
        <p>Acceso al sistema de gestión empresarial</p>
    </div>

    <form action="../controladores/UsuarioController.php" method="POST">

        <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="correo" placeholder="usuario@empresa.com" required>
        </div>

        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="Ingrese su contraseña" required>
        </div>

        <input type="hidden" name="accion" value="login">

        <button type="submit">
            Ingresar
        </button>

    </form>

    <div class="footer">
        BUILDPRO © Sistema de Gestión Constructora
    </div>

</div>

</body>

</html>