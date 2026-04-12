<?php
session_start();

require_once './config/database.php';
require_once './app/controllers/UserController.php';
require_once './app/controllers/ViajeController.php';

$page = $_GET['page'] ?? 'main';

//Manejamos las solicitudes GET para listar viajes (si implementamos esa funcionalidad)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['option']) && $_GET['option'] == 'buscarViajes') {
        $viajeCtrl = new ViajeController();
        $viajeCtrl->listar();
        exit;
    }
}

//Manejamos las solicitudes POST para Login, Registro y Publicar Viaje
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['option'] == 'login') {
        $userCtrl = new UserController();
        $userCtrl->login();
        exit;
    }
    if ($_POST['option'] == 'registrar') {
        $userCtrl = new UserController();
        $userCtrl->registrar();
        exit;
    }
    if ($_POST['option'] == 'publicarViaje') {
        $viajeCtrl = new ViajeController();
        $viajeCtrl->store();
        exit;
    }
    if ($_POST['option'] == 'reservarViaje') {
        $viajeCtrl = new ViajeController();
        $viajeCtrl->reservar();
        exit;
    }
}

//Renderizamos la vista según el parámetro 'page'
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
        $userCtrl = new UserController();
        $userCtrl->logout();
        break;
    default:
        require './app/views/home/main.php';
        break;
}