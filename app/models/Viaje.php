<?php

class Viaje
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================
    // UBICACION
    // =========================
    public function crearUbicacion($detalle)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO UBICACION (provincia, canton, distrito, detalle) VALUES ('-', '-', '-', ?)"
        );
        $stmt->bind_param("s", $detalle);
        $stmt->execute();

        return $this->conn->insert_id;
    }

    // =========================
    // VIAJES PUBLICOS
    // =========================
    public function getAll($id_usuario = null)
    {
        $sql = "SELECT 
            v.id_viaje,
            v.id_conductor,
            u.nombre AS driver,
            o.detalle AS origen,
            d.detalle AS destination,
            v.fecha_viaje AS date,
            v.hora_salida AS time,
            v.precio,
            v.asientos_disponibles AS seatsAvailable,
            ROUND(AVG(c.puntuacion), 2) AS driverRating

        FROM VIAJE v
        JOIN USUARIO u ON v.id_conductor = u.id_usuario
        JOIN UBICACION o ON v.id_origen = o.id_ubicacion
        JOIN UBICACION d ON v.id_destino = d.id_ubicacion

        LEFT JOIN CALIFICACION c 
            ON c.id_calificado = v.id_conductor 
            AND c.tipo = 'CONDUCTOR'

        WHERE v.id_estado_viaje = 1
        AND v.asientos_disponibles > 0";

        if ($id_usuario) {
            $sql .= "
            AND v.id_viaje NOT IN (
                SELECT r.id_viaje
                FROM RESERVA r
                WHERE r.id_pasajero = ?
            )
            AND v.id_conductor != ?";
        }

        $sql .= "
        GROUP BY v.id_viaje
        ORDER BY v.fecha_viaje ASC, v.hora_salida ASC";

        $stmt = $this->conn->prepare($sql);

        if ($id_usuario) {
            $stmt->bind_param("ii", $id_usuario, $id_usuario);
        }

        $stmt->execute();

        return $stmt->get_result();
    }

    // =========================
    // CREAR VIAJE
    // =========================
    public function crear($id_conductor, $id_origen, $id_destino, $fecha, $hora, $espacios, $precio, $comentarios)
    {
        $id_estado_viaje = 1;

        $stmt = $this->conn->prepare(
            "INSERT INTO VIAJE 
            (id_conductor, id_origen, id_destino, id_estado_viaje, fecha_viaje, hora_salida, precio, asientos_disponibles, detalle)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("iiiissdis", $id_conductor, $id_origen, $id_destino, $id_estado_viaje, $fecha, $hora, $precio, $espacios, $comentarios);

        return $stmt->execute();
    }

    // =========================
    // RESERVA
    // =========================
    public function registrarReserva($id_viaje, $id_pasajero)
    {
        $id_estado = 1;

        $stmt = $this->conn->prepare("INSERT INTO RESERVA (id_viaje, id_pasajero, id_estado) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_viaje, $id_pasajero, $id_estado);

        if ($stmt->execute()) {
            $update = $this->conn->prepare("UPDATE VIAJE SET asientos_disponibles = asientos_disponibles - 1 WHERE id_viaje = ?");
            $update->bind_param("i", $id_viaje);
            $update->execute();
            return true;
        }
        return false;
    }

    // =========================
    // VIAJES COMO PASAJERO
    // =========================
    public function getPassengerTrips($id_usuario)
    {
        $sql = "
            SELECT 
                v.id_viaje,
                d.detalle as destino,
                v.fecha_viaje,
                v.hora_salida,
                v.precio,
                v.id_conductor,
                v.id_estado_viaje,

                c.puntuacion AS mi_rating

            FROM RESERVA r
            JOIN VIAJE v ON r.id_viaje = v.id_viaje
            JOIN UBICACION d ON v.id_destino = d.id_ubicacion

            LEFT JOIN CALIFICACION c 
                ON c.id_viaje = v.id_viaje
                AND c.id_calificador = r.id_pasajero
                AND c.tipo = 'CONDUCTOR'

            WHERE r.id_pasajero = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();

        return $stmt->get_result();
    }

    // =========================
    // VIAJES COMO CONDUCTOR
    // =========================
    public function getDriverTrips($id_usuario)
    {
        $sql = "
            SELECT 
                v.id_viaje,
                d.detalle as destino,
                v.fecha_viaje,
                v.hora_salida,
                v.precio,
                v.id_estado_viaje,

                (SELECT COUNT(*) 
                 FROM RESERVA r 
                 WHERE r.id_viaje = v.id_viaje) as pasajeros

            FROM VIAJE v
            JOIN UBICACION d ON v.id_destino = d.id_ubicacion
            WHERE v.id_conductor = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();

        return $stmt->get_result();
    }

    // =========================
    // FINALIZAR VIAJE
    // =========================
    public function finalizarViaje($id_viaje)
    {
        $sql = "UPDATE VIAJE SET id_estado_viaje = 2 WHERE id_viaje = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_viaje);
        return $stmt->execute();
    }

    // =========================
    // CALIFICAR CONDUCTOR (SEGURO)
    // =========================
    public function calificarConductorSeguro($id_viaje, $id_pasajero, $puntuacion, $comentario)
    {
        // 1. Validar viaje
        $sqlViaje = "SELECT id_conductor, id_estado_viaje FROM VIAJE WHERE id_viaje = ?";
        $stmt = $this->conn->prepare($sqlViaje);
        $stmt->bind_param("i", $id_viaje);
        $stmt->execute();
        $viaje = $stmt->get_result()->fetch_assoc();

        if (!$viaje) {
            return ["ok" => false, "message" => "El viaje no existe"];
        }

        if ($viaje['id_estado_viaje'] != 2) {
            return ["ok" => false, "message" => "Solo puedes calificar viajes completados"];
        }

        // 2. Validar participación
        $sqlReserva = "SELECT id_reserva FROM RESERVA WHERE id_viaje = ? AND id_pasajero = ?";
        $stmt = $this->conn->prepare($sqlReserva);
        $stmt->bind_param("ii", $id_viaje, $id_pasajero);
        $stmt->execute();
        $reserva = $stmt->get_result()->fetch_assoc();

        if (!$reserva) {
            return ["ok" => false, "message" => "No participaste en este viaje"];
        }

        // 3. Evitar doble calificación
        $sqlCheck = "SELECT id_calificacion FROM CALIFICACION WHERE id_viaje = ? AND id_calificador = ?";
        $stmt = $this->conn->prepare($sqlCheck);
        $stmt->bind_param("ii", $id_viaje, $id_pasajero);
        $stmt->execute();
        $existe = $stmt->get_result()->fetch_assoc();

        if ($existe) {
            return ["ok" => false, "message" => "Ya calificaste este viaje"];
        }

        // 4. Insertar
        $sqlInsert = "
            INSERT INTO CALIFICACION 
            (id_viaje, id_calificador, id_calificado, tipo, puntuacion, comentario)
            VALUES (?, ?, ?, 'CONDUCTOR', ?, ?)
        ";

        $stmt = $this->conn->prepare($sqlInsert);
        $id_conductor = $viaje['id_conductor'];

        $stmt->bind_param("iiiis", $id_viaje, $id_pasajero, $id_conductor, $puntuacion, $comentario);

        if ($stmt->execute()) {
            return ["ok" => true];
        }

        return ["ok" => false, "message" => "Error al guardar calificación"];
    }
}