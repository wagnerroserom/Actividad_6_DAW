<?php
// includes/config.php
// Configuración general del sistema y conexión a base de datos

$host = '127.0.0.1';
$base_datos = 'gestion_salones';
$usuario = 'root';
$contrasena = ''; // normalmente vacío en XAMPP Windows

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // arrays asociativos por defecto
            PDO::ATTR_EMULATE_PREPARES => false // uso real de prepared statements
        ]
    );
} catch (PDOException $e) {
    // Registra el error en un log sin mostrarlo al usuario
    error_log("Error de conexión: " . $e->getMessage());
    die("Error de conexión a la base de datos."); // mensaje genérico
}
?>
