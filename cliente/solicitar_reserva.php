<?php
// Permite a un usuario cliente solicitar la reserva de un salón

require_once "../includes/functions.php";
require_once "../includes/auth.php";

// Sólo los clientes pueden acceder
soloCliente();

// Validar el ID del salón
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../public/index.php");
    exit;
}

$id_salon = (int) $_GET['id'];
$salon = obtenerSalonPorId($id_salon);

// Verificar la existencia y el estado del salón
if (!$salon || $salon['activo'] == 0) {
    header("Location: ../public/index.php");
    exit;
}

$mensaje = "";
$error = "";

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fecha = $_POST['fecha'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';
    $nombre_contacto = trim($_POST['nombre_contacto'] ?? '');
    $telefono_contacto = trim($_POST['telefono_contacto'] ?? '');

    // Validaciones básicas
    if (empty($fecha) || empty($hora_inicio) || empty($hora_fin) || empty($nombre_contacto)) {
        $error = "Atención: todos los campos obligatorios deben completarse.";
    } elseif ($hora_inicio >= $hora_fin) {
        $error = "La hora de inicio debe ser menor que la hora de fin.";
    } else {

        $resultado = crearReserva(
            $id_salon,
            $_SESSION['id_usuario'],
            $fecha,
            $hora_inicio,
            $hora_fin,
            $nombre_contacto,
            $telefono_contacto
        );

        if ($resultado) {
            $mensaje = "Su reserva ha sido enviada correctamente. Queda pendiente de confirmación.";
        } else {
            $error = "Lo sentimos, el salón no está disponible en el horario seleccionado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Reserva</title>
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
            max-width: 500px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 5px;
            border: 1px solid #aaa;
        }
        button {
            background: #0066cc;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #004c77;
        }
        .mensaje {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<header>
    <h1>Reservar Salón</h1>
</header>

<div class="contenedor">

    <h3><?= htmlspecialchars($salon['nombre']) ?></h3>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
        <a href="../public/index.php">Volver al catálogo</a>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($mensaje)): ?>
        <form method="POST">

            <label>Fecha del evento</label>
            <input type="date" name="fecha" required>

            <label>Hora de inicio</label>
            <input type="time" name="hora_inicio" required>

            <label>Hora de fin</label>
            <input type="time" name="hora_fin" required>

            <label>Nombre del contacto</label>
            <input type="text" name="nombre_contacto" required>

            <label>Teléfono del contacto</label>
            <input type="text" name="telefono_contacto">

            <button type="submit">Enviar solicitud</button>

        </form>
    <?php endif; ?>

</div>

</body>
</html>
