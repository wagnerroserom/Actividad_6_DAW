<?php

// Manejo de sesiones, login, logout y verificación de roles.

require_once __DIR__ . '/config.php';   // conexión PDO
require_once __DIR__ . '/functions.php'; // funciones de negocio

// Iniciar sesión siempre que este archivo sea llamado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 1. Función para iniciar sesión (login) */

function login($correo, $contrasena)
{
    global $pdo;

    // Se busca el usuario por correo
    $sql = "SELECT * FROM usuarios WHERE correo = ? AND activo = 1 LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();

    // Se valida existencia
    if (!$usuario) {
        return false;
    }

    // Se valida la contraseña
    if (!password_verify($contrasena, $usuario['contrasena'])) {
        return false;
    }

    // Se regenera ID de sesión por seguridad
    session_regenerate_id(true);

    // Guarda  en la sesión
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['tipo'] = $usuario['tipo'];
    $_SESSION['nombre'] = $usuario['nombre_completo'];

    return true;
}


/* 2. Se verifica si el usuario está autenticado */

function estaLogueado()
{
    return isset($_SESSION['id_usuario']);
}


/* 3. Se restringe el acceso según rol */

// Función sólo para administradores
function soloAdmin()
{
    if (!estaLogueado() || $_SESSION['tipo'] !== 'admin') {
        header("Location: ../public/login.php");
        exit;
    }
}

// Función sólo para clientes
function soloCliente()
{
    if (!estaLogueado() || $_SESSION['tipo'] !== 'cliente') {
        header("Location: ../public/login.php");
        exit;
    }
}


/* 4. Función para cerrar sesión (logout) */

function logout()
{
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../public/login.php");
    exit;
}
?>
