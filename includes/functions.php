<?php

require_once __DIR__ . '/config.php';

/* 1. Se verifica si hay disponibilidad de salón */
function salonDisponible($id_salon, $fecha, $hora_inicio, $hora_fin)
{
    global $pdo;

    // Consulta para detectar solapamientos de horarios.
    $sql = "SELECT COUNT(*) AS conflictos
            FROM reservas
            WHERE id_salon = :id_salon
                AND fecha = :fecha
                AND estado IN ('pendiente', 'confirmada')
                AND NOT (hora_fin <= :hora_inicio OR hora_inicio >= :hora_fin)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_salon' => $id_salon,
        ':fecha' => $fecha,
        ':hora_inicio' => $hora_inicio,
        ':hora_fin' => $hora_fin
    ]);

    return $stmt->fetchColumn() == 0;
}

/* CRUD de salones */
// Obtener todos los salones
function obtenerSalones()
{
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM salones ORDER BY nombre ASC");
    return $stmt->fetchAll();
}

// Crear un nuevo salón
function crearSalon($nombre, $descripcion, $capacidad, $precio)
{
    global $pdo;
    $sql = "INSERT INTO salones (nombre, descripcion, capacidad, precio, activo)
            VALUES (?, ?, ?, ?, 1)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nombre, $descripcion, $capacidad, $precio]);
}

// Obtener datos de un salón específico
function obtenerSalonPorId($id_salon)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM salones WHERE id_salon = ?");
    $stmt->execute([$id_salon]);
    return $stmt->fetch();
}

// Editar un salón
function editarSalon($id_salon, $nombre, $descripcion, $capacidad, $precio)
{
    global $pdo;
    $sql = "UPDATE salones
            SET nombre=?, descripcion=?, capacidad=?, precio=?
            WHERE id_salon=?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nombre, $descripcion, $capacidad, $precio, $id_salon]);
}

// Activar o desactivar un salón
function cambiarEstadoSalon($id_salon, $activo)
{
    global $pdo;
    $sql = "UPDATE salones SET activo=? WHERE id_salon=?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$activo, $id_salon]);
}

/* CRUD de reservas (para cliente y admin) */
// Crear la solicitud de reserva
function crearReserva($id_salon, $id_usuario, $fecha, $hora_inicio, $hora_fin, $nombre_contacto, $telefono_contacto)
{
    global $pdo;

    // Iniciar la transacción para evitar doble reserva simultánea
    $pdo->beginTransaction();

    try {

        // Validar la disponibilidad antes de insertar
        if (!salonDisponible($id_salon, $fecha, $hora_inicio, $hora_fin)) {
            $pdo->rollBack();
            return false; // No disponible
        }

        $sql = "INSERT INTO reservas 
                (id_salon, id_usuario, fecha, hora_inicio, hora_fin, contacto_nombre, contacto_telefono, estado) 
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
        error_log("Error en crearReserva(): " . $e->getMessage());
        return false;
    }
}


// Obtener las reservas, todas o filtradas por usuario
function obtenerReservas($id_usuario = null)
{
    global $pdo;

    if ($id_usuario) {
        $stmt = $pdo->prepare("SELECT r.*, s.nombre AS salon
                                FROM reservas r
                                JOIN salones s ON r.id_salon = s.id_salon
                                WHERE r.id_usuario = ?
                                ORDER BY r.fecha DESC");
        $stmt->execute([$id_usuario]);
    } else {
        $stmt = $pdo->query("SELECT r.*, s.nombre AS salon
                                FROM reservas r
                                JOIN salones s ON r.id_salon = s.id_salon
                                ORDER BY r.fecha DESC");
    }

    return $stmt->fetchAll();
}

// Cambiar el estado de una reserva (admin)
function actualizarEstadoReserva($id_reserva, $nuevo_estado)
{
    global $pdo;

    $sql = "UPDATE reservas SET estado=? WHERE id_reserva=?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nuevo_estado, $id_reserva]);
}
?>
