<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Viaje.php';

class ViajeController
{
    private $model;

    public function __construct()
    {

        $database = new Database();
        $db = $database->connect();

        $this->model = new Viaje($db);
    }

    public function store()
    {
        try {
            //1.Recibimos los datos del POST (AJAX)
            $origen_texto = $_POST['origen'];
            $destino_texto = $_POST['destino'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $espacios = $_POST['espacios'];
            $precio = $_POST['precio'];
            $comentarios = $_POST['comentarios'];

            //2.Guardamos las ubicaciones para obtener sus IDs
            $id_origen = $this->model->crearUbicacion($origen_texto);
            $id_destino = $this->model->crearUbicacion($destino_texto);

            //3.Obtenemos el ID del conductor desde la sesión actual
            $id_conductor = $_SESSION['id_usuario']; 

            if (!$id_conductor) {
                echo json_encode(["response" => "01", "message" => "La sesión expiró. Por favor, inicia sesión de nuevo."]);
                return;
            }

            //4.Guardamos el viaje completo
            $exito = $this->model->crear($id_conductor, $id_origen, $id_destino, $fecha, $hora, $espacios, $precio, $comentarios);

            if ($exito) {
                echo json_encode(["response" => "00", "message" => "Viaje publicado con éxito"]);
            } else {
                echo json_encode(["response" => "01", "message" => "Error interno al guardar en base de datos"]);
            }
            
        } catch (Exception $e) {
            echo json_encode(["response" => "01", "message" => "Excepción: " . $e->getMessage()]);
        }
    }

    public function listar()
    {
        $id_usuario = $_SESSION['id_usuario'] ?? null;

        $viajes = $this->model->getAll($id_usuario);

        echo json_encode($viajes->fetch_all(MYSQLI_ASSOC));
    }

    //Función para procesar la reserva
    public function reservar()
    {
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(["response" => "01", "message" => "Debes iniciar sesión para reservar un viaje."]);
            return;
        }

        $id_viaje = $_POST['id_viaje'];
        $id_pasajero = $_SESSION['id_usuario'];

        $exito = $this->model->registrarReserva($id_viaje, $id_pasajero);

        if ($exito) {
            echo json_encode(["response" => "00", "message" => "Reserva exitosa"]);
        } else {
            echo json_encode(["response" => "01", "message" => "Error al intentar reservar en la base de datos."]);
        }
    }

    public function misRides()
    {
        if (!isset($_SESSION['id_usuario'])) {
            http_response_code(401);
            echo json_encode(["message" => "No autorizado"]);
            return;
        }

        $id_usuario = $_SESSION['id_usuario'];

        $passenger = $this->model->getPassengerTrips($id_usuario)->fetch_all(MYSQLI_ASSOC);
        $driver = $this->model->getDriverTrips($id_usuario)->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            "passenger" => $passenger,
            "driver" => $driver
        ]);
    }

    public function finalizar()
    {
        $id_viaje = $_POST['id_viaje'];

        $ok = $this->model->finalizarViaje($id_viaje);

        echo json_encode([
            "response" => $ok ? "00" : "01"
        ]);
    }

    public function calificar()
    {
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(["response" => "01", "message" => "No autenticado"]);
            return;
        }

        $id_viaje = $_POST['id_viaje'];
        $puntuacion = $_POST['puntuacion'];
        $comentario = $_POST['comentario'];
        $id_pasajero = $_SESSION['id_usuario'];

        $result = $this->model->calificarConductorSeguro(
            $id_viaje,
            $id_pasajero,
            $puntuacion,
            $comentario
        );

        if ($result["ok"]) {
            echo json_encode(["response" => "00"]);
        } else {
            echo json_encode([
                "response" => "01",
                "message" => $result["message"]
            ]);
        }
    }
}