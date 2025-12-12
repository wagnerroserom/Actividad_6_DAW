<?php
// Se muestra la información detallada de un salón específico

require_once "../includes/functions.php";
require_once "../includes/auth.php";

// Valida que se reciba el ID del salón
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../public/index.php");
    exit;
}

$id_salon = (int) $_GET['id'];

// Obtiene datos del salón
$salon = obtenerSalonPorId($id_salon);

// Si no existe o está inactivo, redirige
if (!$salon || $salon['activo'] == 0) {
    header("Location: ../public/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Salón</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: #003366;
            color: white;
            padding: 17px;
            text-align: center;
        }
        .contenedor {
            max-width: 800px;
            margin: 33px auto;
            background: white;
            padding: 26px;
            border-radius: 9px;
            box-shadow: 0 0 13px rgba(0,0,0,0.15);
        }
        .acciones a {
            text-decoration: none;
            padding: 10px 14px;
            background: #0066cc;
            color: white;
            border-radius: 7px;
            margin-right: 9px;
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
    <h1><?= htmlspecialchars($salon['nombre']) ?></h1>
</header>

<div class="contenedor">

    <p><?= nl2br(htmlspecialchars($salon['descripcion'])) ?></p>

    <p>
        <strong>Capacidad:</strong> <?= $salon['capacidad'] ?> personas<br>
        <strong>Precio:</strong> $<?= number_format($salon['precio'], 2) ?>
    </p>

    <div class="acciones">
        <a href="../public/index.php" class="secondary">Volver</a>

        <?php if (estaLogueado() && $_SESSION['tipo'] === 'cliente'): ?>
            <a href="solicitar_reserva.php?id=<?= $salon['id_salon'] ?>">
                Reservar este salón
            </a>
        <?php else: ?>
            <a href="../public/login.php">
                Iniciar sesión para reservar
            </a>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
