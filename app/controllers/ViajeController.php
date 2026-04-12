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

    //Función para listar viajes activos
    public function listar()
    {
        $viajes = $this->model->getAll();
        //Convertimos el resultado de la base de datos a JSON y lo enviamos
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
}