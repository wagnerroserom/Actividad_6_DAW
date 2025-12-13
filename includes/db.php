<?php
// Conexión a la base de datos usando PDO

function conectarBD() {
    $host = "localhost";
    $db   = "gestion_salones";
    $user = "root";
    $pass = "";
    $charset = "utf8mb4";

    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        return new PDO($dsn, $user, $pass, $options);

    } catch (PDOException $e) {
        die("Error de conexión a la base de datos");
    }
}
