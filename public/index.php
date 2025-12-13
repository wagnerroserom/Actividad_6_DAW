
<?php
// Página principal del sistema: catálogo de salones disponibles

require_once "../includes/functions.php";
require_once "../includes/auth.php";

// Obtener solo salones disponibles
$salones = obtenerSalonesDisponibles();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salones de Eventos</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            margin: 0;
        }
        header {
            background: #003366;
            color: white;
            padding: 17px;
            text-align: center;
        }
        .contenedor {
            max-width: 1000px;
            margin: 21px auto;
        }
        .salon {
            background: white;
            padding: 13px;
            margin-bottom: 13px;
            border-radius: 9px;
            box-shadow: 0 0 9px rgba(0,0,0,0.15);
        }
        .acciones a {
            text-decoration: none;
            padding: 9px 13px;
            background: #0066cc;
            color: white;
            border-radius: 7px;
            margin-right: 7px;
            font-size: 13px;
        }
        .acciones a.secondary {
            background: #777;
        }
        .mensaje {
            text-align: center;
            margin: 33px;
            color: #666;
        }
    </style>
</head>
<body>

<header>
    <h1>Salones de Eventos</h1>

    <?php if (estaLogueado()): ?>
        <p>
            Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?> |
            <a href="logout.php" style="color:#ffcc00;">Cerrar sesión</a>
        </p>
    <?php else: ?>
        <p>
            <a href="login.php" style="color:#ffcc00;">Iniciar sesión</a>
        </p>
    <?php endif; ?>
</header>

<div class="contenedor">

    <?php if (empty($salones)): ?>
        <p class="mensaje">Lo sentimos, no hay salones disponibles actualmente.</p>
    <?php endif; ?>

    <?php foreach ($salones as $salon): ?>
        <div class="salon">
            <h3><?= htmlspecialchars($salon['nombre']) ?></h3>

            <p><?= nl2br(htmlspecialchars($salon['descripcion'])) ?></p>

            <p>
                <strong>Capacidad:</strong> <?= $salon['capacidad'] ?> personas<br>
                <strong>Precio:</strong>
                $<?= number_format($salon['precio_por_hora'], 2) ?> por hora
            </p>

            <div class="acciones">
                <?php if (estaLogueado() && $_SESSION['tipo'] === 'cliente'): ?>
                    <a href="reservar.php?id=<?= $salon['id_salon'] ?>" class="secondary">
                        Reservar
                    </a>
                <?php else: ?>
                    <a href="login.php" class="secondary">
                        Reservar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>

</body>
</html>
