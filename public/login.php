<?php
// Formulario para el inicio de sesión del sistema

require_once "../includes/auth.php";

// Si ya está logueado, lo redirige según el rol
if (estaLogueado()) {
    if ($_SESSION['tipo'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$mensaje_error = "";

// Procesa el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $contrasena = trim($_POST["contrasena"] ?? "");

    if (login($correo, $contrasena)) {
        // Redirige según el tipo de usuario
        if ($_SESSION['tipo'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $mensaje_error = "Credenciales incorrectas. Intente nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <style>
        body {
            background: #f2f2f2;
            font-family: Arial;
            margin: 0;
            padding: 0;
        }
        .contenedor {
            max-width: 380px;
            margin: 90px auto;
            background: white;
            padding: 27px;
            border-radius: 9px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 17px;
        }
        input[type=email], input[type=password] {
            width: 100%;
            padding: 13px;
            margin-bottom: 13px;
            border: 1px solid #aaa;
            border-radius: 7px;
        }
        button {
            width: 100%;
            padding: 13px;
            background: #0066cc;
            border: none;
            color: white;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #004c77;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 13px;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($mensaje_error)): ?>
        <div class="error"><?= htmlspecialchars($mensaje_error) ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <input type="email" name="correo" placeholder="Correo electrónico" required>

        <input type="password" name="contrasena" placeholder="Contraseña" required>

        <button type="submit">Entrar</button>

    </form>
</div>

</body>
</html>
