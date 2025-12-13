<?php
// Funciones de negocio del sistema
// Salones y Reservas

require_once __DIR__ . '/config.php';

/* SALONES */

/* Obtiene solo los salones disponibles */
function obtenerSalonesDisponibles()
{
    global $pdo;

    $stmt = $pdo->query(
        "SELECT * FROM salones WHERE disponible = 1 ORDER BY nombre"
    );
    return $stmt->fetchAll();
}

/* Obtiene todos los salones (admin) */
function obtenerTodosLosSalones()
{
    global $pdo;

    return $pdo->query(
        "SELECT * FROM salones ORDER BY id_salon DESC"
    )->fetchAll();
}

/* Crea un nuevo salón */
function crearSalon($nombre, $descripcion, $capacidad, $precio)
{
    global $pdo;

    $stmt = $pdo->prepare(
        "INSERT INTO salones 
        (nombre, descripcion, capacidad, precio_por_hora, disponible)
        VALUES (?, ?, ?, ?, 1)"
    );

    return $stmt->execute([
        $nombre,
        $descripcion,
        $capacidad,
        $precio
    ]);
}

/* Activa o desactiva un salón */
function actualizarEstadoSalon($id, $estado)
{
    global $pdo;

    $stmt = $pdo->prepare(
        "UPDATE salones SET disponible = ? WHERE id_salon = ?"
    );

    return $stmt->execute([$estado, $id]);
}

/* Obtiene un salón por su ID */
function obtenerSalonPorId($id)
{
    global $pdo;

    $stmt = $pdo->prepare(
        "SELECT * FROM salones WHERE id_salon = ? LIMIT 1"
    );
    $stmt->execute([$id]);

    return $stmt->fetch();
}

/* RESERVAS */

/* Verifica si un salón está disponible en un horario */
function salonDisponible($id_salon, $fecha, $inicio, $fin)
{
    global $pdo;

    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM reservas
        WHERE id_salon = ?
        AND fecha = ?
        AND estado IN ('pendiente','confirmada')
        AND NOT (hora_fin <= ? OR hora_inicio >= ?)"
    );

    $stmt->execute([$id_salon, $fecha, $inicio, $fin]);

    return $stmt->fetchColumn() == 0;
}

/* Crea una reserva */
function crearReserva($id_salon, $id_usuario, $fecha, $inicio, $fin, $nombre, $telefono)
{
    global $pdo;

    // Valida disponibilidad antes de insertar
    if (!salonDisponible($id_salon, $fecha, $inicio, $fin)) {
        return false;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO reservas
        (id_salon, id_usuario, fecha, hora_inicio, hora_fin,
            nombre_contacto, telefono_contacto, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')"
    );

    return $stmt->execute([
        $id_salon,
        $id_usuario,
        $fecha,
        $inicio,
        $fin,
        $nombre,
        $telefono
    ]);
}

/* FUNCIONES PARA GESTIÓN DE RESERVAS (ADMIN) */

/* Obtiene todas las reservas con datos del salón y del cliente */
function obtenerTodasLasReservas()
{
    global $pdo;

    $sql = "
        SELECT 
            r.id_reserva,
            s.nombre AS salon,
            u.nombre_completo AS cliente,
            r.fecha,
            r.hora_inicio,
            r.hora_fin,
            r.nombre_contacto,
            r.telefono_contacto,
            r.estado
        FROM reservas r
        INNER JOIN salones s ON r.id_salon = s.id_salon
        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
        ORDER BY r.fecha DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}

/*  Actualiza el estado de una reserva, estados posibles: pendiente, confirmada, rechazada, cancelada */
function actualizarEstadoReserva($id_reserva, $estado)
{
    global $pdo;

    $stmt = $pdo->prepare(
        "UPDATE reservas SET estado = ? WHERE id_reserva = ?"
    );

    return $stmt->execute([$estado, $id_reserva]);
}
