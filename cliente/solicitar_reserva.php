<?php
require_once "../includes/functions.php";
require_once "../includes/auth.php";

// Solo clientes pueden reservar
soloCliente();

$id = $_GET['id'] ?? null;

// Obtiene datos del salón
$salon = obtenerSalonPorId($id);

// Si no existe o está inactivo, redirige
if (!$salon || $salon['disponible'] == 0) {
    header("Location: ../public/index.php");
    exit;
}

$mensaje = "";

// Procesa el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $resultado = crearReserva(
        $id,
        $_SESSION['id_usuario'],
        $_POST['fecha'],
        $_POST['hora_inicio'],
        $_POST['hora_fin'],
        $_POST['nombre'],
        $_POST['telefono']
    );

    $mensaje = $resultado
        ? "Su reserva ha sido registrada correctamente. Pendiente de confirmación."
        : "Lo sentimos el salón no está disponible en ese horario.";
}
