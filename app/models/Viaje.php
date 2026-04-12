<?php

class Viaje
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //Función auxiliar para registrar la ubicación y obtener su ID
    public function crearUbicacion($detalle)
    {
        //Ponemos guiones en provincia/canton/distrito por ahora, ya que el frontend solo envía un texto general
        $stmt = $this->conn->prepare(
            "INSERT INTO UBICACION (provincia, canton, distrito, detalle) VALUES ('-', '-', '-', ?)"
        );
        $stmt->bind_param("s", $detalle);
        $stmt->execute();
        
        
        return $this->conn->insert_id; 
    }

    //Función para obtener todos los viajes activos con espacios disponibles
    public function getAll()
    {
        //Traemos viajes activos (estado = 1) y con espacios disponibles
        $sql = "SELECT v.id_viaje, u.nombre AS driver, o.detalle AS origen, d.detalle AS destination, 
                       v.fecha_viaje AS date, v.hora_salida AS time, v.precio, v.asientos_disponibles AS seatsAvailable
                FROM VIAJE v
                JOIN USUARIO u ON v.id_conductor = u.id_usuario
                JOIN UBICACION o ON v.id_origen = o.id_ubicacion
                JOIN UBICACION d ON v.id_destino = d.id_ubicacion
                WHERE v.id_estado_viaje = 1 AND v.asientos_disponibles > 0
                ORDER BY v.fecha_viaje ASC, v.hora_salida ASC";

        $result = $this->conn->query($sql);
        return $result;
    }

    //Función principal para guardar el viaje
    public function crear($id_conductor, $id_origen, $id_destino, $fecha, $hora, $espacios, $precio, $comentarios)
    {
        $id_estado_viaje = 1; //Asumimos que 1 significa "Activo" en la tabla ESTADO

        $stmt = $this->conn->prepare(
            "INSERT INTO VIAJE 
            (id_conductor, id_origen, id_destino, id_estado_viaje, fecha_viaje, hora_salida, precio, asientos_disponibles, detalle)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        //La 'i' es para enteros (int), la 'd' para decimales (double/float), la 's' para strings
        $stmt->bind_param("iiiissdis", $id_conductor, $id_origen, $id_destino, $id_estado_viaje, $fecha, $hora, $precio, $espacios, $comentarios);

        return $stmt->execute();
    }

    //Función para registrar una reserva
    public function registrarReserva($id_viaje, $id_pasajero)
    {
        $id_estado = 1; // 1 = Estado "Activo"

        //Insertamos en la tabla RESERVA
        $stmt = $this->conn->prepare("INSERT INTO RESERVA (id_viaje, id_pasajero, id_estado) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_viaje, $id_pasajero, $id_estado);

        if ($stmt->execute()) {
            //Si se reservó con éxito, restamos 1 a los asientos disponibles en la tabla VIAJE
            $update = $this->conn->prepare("UPDATE VIAJE SET asientos_disponibles = asientos_disponibles - 1 WHERE id_viaje = ?");
            $update->bind_param("i", $id_viaje);
            $update->execute();
            return true;
        }
        return false;
    }
}