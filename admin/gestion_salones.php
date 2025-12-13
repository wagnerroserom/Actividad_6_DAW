<?php
// CRUD para la gestión de los salones (Administrador)

require_once "../includes/functions.php";
require_once "../includes/auth.php";

// Restringir acceso solo a administradores
soloAdmin();

$mensaje = "";

/* CREAR UN NUEVO SALÓN */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['crear'])) {

    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $capacidad = (int) $_POST['capacidad'];
    $precio = (float) $_POST['precio'];

    if ($nombre && $capacidad > 0 && $precio > 0) {
        crearSalon($nombre, $descripcion, $capacidad, $precio);
        $mensaje = "El salón ha sido creado correctamente.";
    } else {
        $mensaje = "Complete correctamente todos los campos.";
    }
}

/* ACTIVAR O DESACTIVAR UN SALÓN */
if (isset($_GET['accion'], $_GET['id']) && is_numeric($_GET['id'])) {

    $id = (int) $_GET['id'];

    if ($_GET['accion'] === 'activar') {
        actualizarEstadoSalon($id, 1);
    }

    if ($_GET['accion'] === 'desactivar') {
        actualizarEstadoSalon($id, 0);
    }

    header("Location: gestion_salones.php");
    exit;
}

/* OBTENER SALONES */
$salones = obtenerTodosLosSalones();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Salones</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            margin: 0;
        }
        header {
            background: #003366;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .contenedor {
            max-width: 1000px;
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
            font-size: 14px;
        }
        .activar {
            background: green;
        }
        .desactivar {
            background: red;
        }
        input, textarea, button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            background: #0066cc;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<header>
    <h1>Gestión de Salones</h1>
    <a href="dashboard.php" style="color:#ffcc00;">Volver al panel</a>
</header>

<div class="contenedor">

    <h3>Crear nuevo salón</h3>

    <?php if ($mensaje): ?>
        <p><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del salón" required>
        <textarea name="descripcion" placeholder="Descripción"></textarea>
        <input type="number" name="capacidad" placeholder="Capacidad" required>
        <input type="number" step="0.01" name="precio" placeholder="Precio por hora" required>
        <button type="submit" nam
