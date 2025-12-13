<?php
// Permite al administrador gestionar las reservas

require_once "../includes/functions.php";
require_once "../includes/auth.php";

// Solo administradores
soloAdmin();

// Procesar acciones sobre reservas
if (isset($_GET['accion'], $_GET['id']) && is_numeric($_GET['id'])) {

    $id_reserva = (int) $_GET['id'];

    if ($_GET['accion'] === 'confirmar') {
        actualizarEstadoReserva($id_reserva, 'confirmada');
    }

    if ($_GET['accion'] === 'rechazar') {
        actualizarEstadoReserva($id_reserva, 'rechazada');
    }

    if ($_GET['accion'] === 'cancelar') {
        actualizarEstadoReserva($id_reserva, 'cancelada');
    }

    header("Location: gestion_reservas.php");
    exit;
}

// Obtener todas las reservas
$reservas = obtenerTodasLasReservas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas</title>
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
            padding: 15px;
            text-align: center;
        }
        .contenedor {
            max-width: 1100px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #eee;
        }
        .acciones a {
            padding: 6px 10px;
            text-decoration: none;
            border-radius: 4px;
            color: white;
            font-size: 13px;
        }
        .confirmar {
            background: green;
        }
        .rechazar {
            background: orange;
        }
        .cancelar {
            background: red;
        }
    </style>
</head>
<body>

<header>
    <h1>Gestión de Reservas</h1>
    <a href="dashboard.php" style="color:#ffcc00;">Volver al panel</a>
</header>

<div class="contenedor">

    <h3>Listado de reservas</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Salón</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Horario</th>
            <th>Contacto</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>

        <?php foreach ($reservas as $reserva): ?>
            <tr>
                <td><?= $reserva['id_reserva'] ?></td>
                <td><?= htmlspecialchars($reserva['salon']) ?></td>
                <td><?= htmlspecialchars($reserva['cliente']) ?></td>
                <td><?= $reserva['fecha'] ?></td>
                <td><?= $reserva['hora_inicio'] ?> - <?= $reserva['hora_fin'] ?></td>
                <td>
                    <?= htmlspecialchars($reserva['nombre_contacto']) ?><br>
                    <?= htmlspecialchars($reserva['telefono_contacto']) ?>
                </td>
                <td><?= ucfirst($reserva['estado']) ?></td>
                <td class="acciones">
                    <?php if ($reserva['estado'] === 'pendiente'): ?>
                        <a class="confirmar" href="?accion=confirmar&id=<?= $reserva['id_reserva'] ?>">Confirmar</a>
                        <a class="rechazar" href="?accion=rechazar&id=<?= $reserva['id_reserva'] ?>">Rechazar</a>
                    <?php elseif ($reserva['estado'] === 'confirmada'): ?>
                        <a class="cancelar" href="?accion=cancelar&id=<?= $reserva['id_reserva'] ?>">Cancelar</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
