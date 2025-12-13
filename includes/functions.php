<?php

require_once __DIR__ . '/config.php';

/* VALIDAR DISPONIBILIDAD DE SALÓN */

function salonDisponible($id_salon, $fecha, $hora_inicio, $hora_fin)
{
    global $pdo;

    $sql = "SELECT COUNT(*)
            FROM reservas
            WHERE id_salon = ?
            AND fecha = ?
            AND estado IN ('pendiente', 'confirmada')
            AND NOT (hora_fin <= ? OR hora_inicio >= ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $id_salon,
        $fecha,
        $hora_inicio,
        $hora_fin
    ]);

    return $stmt->fetchColumn() == 0;
}

/* FUNCIONES PARA GESTIÓN DE SALONES */

function crearSalon($nombre, $descripcion, $capacidad, $precio, $imagen = 'default.jpg')
{
    global $pdo;

    $sql = "INSERT INTO salones
            (nombre, descripcion, capacidad, precio_por_hora, imagen, disponible)
            VALUES (?, ?, ?, ?, ?, 1)";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $nombre,
        $descripcion,
        $capacidad,
        $precio,
        $imagen
    ]);
}

function obtenerTodosLosSalones()
{
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM salones ORDER BY id_salon DESC");
    return $stmt->fetchAll();
}

function obtenerSalonesDisponibles()
{
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM salones WHERE disponible = 1 ORDER BY nombre ASC");
    return $stmt->fetchAll();
}

function actualizarEstadoSalon($id_salon, $estado)
{
    global $pdo;
    $sql = "UPDATE salones SET disponible = ? WHERE id_salon = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$estado, $id_salon]);
}

/* FUNCIONES PARA GESTIÓN DE RESERVAS= */

function crearReserva(
    $id_salon,
    $id_usuario,
    $fecha,
    $hora_inicio,
    $hora_fin,
    $nombre_contacto,
    $telefono_contacto
) {
    global $pdo;

    $pdo->beginTransaction();

    try {
        if (!salonDisponible($id_salon, $fecha, $hora_inicio, $hora_fin)) {
            $pdo->rollBack();
            return false;
        }

        $sql = "INSERT INTO reservas
                (id_salon, id_usuario, fecha, hora_inicio, hora_fin,
                    nombre_contacto, telefono_contacto, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_salon,
            $id_usuario,
            $fecha,
            $hora_inicio,
            $hora_fin,
            $nombre_contacto,
            $telefono_contacto
        ]);

        $pdo->commit();
        return true;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error al crear reserva: " . $e->getMessage());
        return false;
    }
}

function obtenerReservas($id_usuario = null)
{
    global $pdo;

    if ($id_usuario) {
        $stmt = $pdo->prepare(
            "SELECT r.*, s.nombre AS salon
                FROM reservas r
                JOIN salones s ON r.id_salon = s.id_salon
                WHERE r.id_usuario = ?
                ORDER BY r.fecha DESC"
        );
        $stmt->execute([$id_usuario]);
    } else {
        $stmt = $pdo->query(
            "SELECT r.*, s.nombre AS salon
                FROM reservas r
                JOIN salones s ON r.id_salon = s.id_salon
                ORDER BY r.fecha DESC"
        );
    }

    return $stmt->fetchAll();
}

function obtenerTodasLasReservas()
{
    global $pdo;

    $sql = "SELECT r.id_reserva,
                s.nombre AS salon,
                u.nombre_completo AS cliente,
                r.fecha,
                r.hora_inicio,
                r.hora_fin,
                r.nombre_contacto,
                r.telefono_contacto,
                r.estado
            FROM reservas r
            JOIN salones s ON r.id_salon = s.id_salon
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            ORDER BY r.fecha DESC";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function actualizarEstadoReserva($id_reserva, $estado)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE reservas SET estado = ? WHERE id_reserva = ?");
    return $stmt->execute([$estado, $id_reserva]);
}
