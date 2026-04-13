<?php
session_start();

require_once './config/database.php';
require_once './app/controllers/UserController.php';
require_once './app/controllers/ViajeController.php';

$page = $_GET['page'] ?? 'main';
$option = $_REQUEST['option'] ?? null;

//=========================
// GET REQUESTS
//=========================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if ($option === 'buscarViajes') {
        (new ViajeController())->listar();
        exit;
    }

    if ($option === 'misRides') {
        if (!isset($_SESSION['id_usuario'])) {
            http_response_code(401);
            echo json_encode(["message" => "No autorizado"]);
            exit;
        }

        (new ViajeController())->misRides();
        exit;
    }
}

//=========================
// POST REQUESTS
//=========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($option) {
        case 'login':
            (new UserController())->login();
            exit;

        case 'registrar':
            (new UserController())->registrar();
            exit;

        case 'publicarViaje':
            (new ViajeController())->store();
            exit;

        case 'reservarViaje':
            (new ViajeController())->reservar();
            exit;

        case 'finalizar':
            (new ViajeController())->finalizar();
            exit;

        case 'calificar':
            (new ViajeController())->calificar();
            exit;
    }
}

//=========================
// VISTAS
//=========================
switch ($page) {
    case 'login':
        require './app/views/auth/login.php';
        break;

    case 'crear_viaje':
        require './app/views/viajes/crear.php';
        break;

    case 'buscar_viaje':
        require './app/views/viajes/buscar.php';
        break;

    case 'logout':
        (new UserController())->logout();
        break;

    default:
        require './app/views/home/main.php';
        break;
}