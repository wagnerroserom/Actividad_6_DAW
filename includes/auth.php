<?php

// Funciones de autenticación y sesiones

require_once __DIR__ . '/config.php';

// Inicia la sesión solo si no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Función de inicio de sesión */
function login($correo, $contrasena)
{
    global $pdo;

    // Busca al usuario activo por correo
    $stmt = $pdo->prepare(
        "SELECT * FROM usuarios WHERE correo = ? AND activo = 1 LIMIT 1"
    );
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();

    // Verifica la existencia del usuario
    if (!$usuario) {
        return false;
    }

    // Se verifica la contraseña hasheada
    if (!password_verify($contrasena, $usuario['contrasena'])) {
        return false;
    }

    // Regenera el ID de sesión por seguridad
    session_regenerate_id(true);

    // Guarda datos importantes en la sesión
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre']     = $usuario['nombre_completo'];
    $_SESSION['tipo']       = $usuario['tipo'];

    return true;
}

/* Verifica si hay un usuario logueado */
function estaLogueado()
{
    return isset($_SESSION['id_usuario']);
}

/* Restringe acceso solo a administradores */
function soloAdmin()
{
    if (!estaLogueado() || $_SESSION['tipo'] !== 'admin') {
        header("Location: ../public/login.php");
        exit;
    }
}

/* Restringe acceso solo a clientes */
function soloCliente()
{
    if (!estaLogueado() || $_SESSION['tipo'] !== 'cliente') {
        header("Location: ../public/login.php");
        exit;
    }
}

/* Cierra la sesión del usuario */
function logout()
{
    session_unset();
    session_destroy();
    header("Location: ../public/login.php");
    exit;
}
