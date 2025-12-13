<?php
// Panel principal del administrador

require_once "../includes/auth.php";

// Restringe el acceso sólo a administradores
soloAdmin();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        header {
            background: #003366;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .contenedor {
            max-width: 900px;
            margin: 30px auto;
        }
        .card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.15);
        }
        .card h3 {
            margin-top: 0;
        }
        .acciones a {
            display: inline-block;
            margin-right: 10px;
            padding: 10px 14px;
            background: #0066cc;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .acciones a.secondary {
            background: #777;
        }
        .acciones a:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<header>
    <h1>Panel de Administración</h1>
    <p>
        Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?> |
        <a href="../public/logout.php" style="color:#ffcc00;">Cerrar sesión</a>
    </p>
</header>

<div class="contenedor">

    <div class="card">
        <h3>Gestión de Salones</h3>
        <p>Crear, editar, activar o desactivar salones de eventos.</p>
        <div class="acciones">
            <a href="gestion_salones.php">Administrar salones</a>
        </div>
    </div>

    <div class="card">
        <h3>Gestión de Reservas</h3>
        <p>Confirmar, rechazar o cancelar solicitudes de reserva.</p>
        <div class="acciones">
            <a href="gestion_reservas.php">Administrar reservas</a>
        </div>
    </div>

    <div class="card">
        <h3>Volver al sitio público</h3>
        <div class="acciones">
            <a href="../public/index.php" class="secondary">Ver catálogo</a>
        </div>
    </div>

</div>

</body>
</html>
