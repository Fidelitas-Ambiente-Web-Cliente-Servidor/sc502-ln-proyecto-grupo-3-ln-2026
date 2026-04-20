<?php
//Asegurarnos de que la sesión esté iniciada antes de usar $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $model;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->model = new User($db);
    }

    public function login()
    {
        $correo = $_POST['correo'];
        $password = $_POST['password'];

        $user = $this->model->login($correo);

        //Verificamos que el usuario exista y que la contraseña coincida con el Hash guardado
        if ($user && password_verify($password, $user['contrasena'])) {
            //Guardamos datos en la sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['correo'] = $user['correo'];

            echo json_encode(['response' => "00", 'message' => "Login exitoso"]);
        } else {
            echo json_encode(['response' => "01", 'message' => "Correo o contraseña incorrectos"]);
        }
    }

    public function registrar()
    {
        try {
            $nombre = $_POST['nombre'];
            $apellidos = $_POST['apellidos'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];
            $password = $_POST['password'];

            //ENCRIPTAMOS LA CONTRASEÑA ANTES DE GUARDARLA
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $exito = $this->model->registrar($nombre, $apellidos, $telefono, $correo, $password_hash);

            if ($exito) {
                echo json_encode(['response' => "00", 'message' => "Usuario registrado correctamente"]);
            } else {
                echo json_encode(['response' => "01", 'message' => "El correo ya está registrado o hubo un error"]);
            }
        } catch (Exception $e) {
            echo json_encode(['response' => "01", 'message' => "Error interno: Verifica que el correo no esté duplicado."]);
        }
    }

    public function recuperar() {
        $correo = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';

        if(empty($correo) || empty($telefono) || empty($nueva_contrasena)) {
            echo json_encode(["response" => "01", "message" => "Todos los campos son obligatorios."]);
            return;
        }

        $resultado = $this->model->actualizarContrasena($correo, $telefono, $nueva_contrasena);

        if ($resultado['ok']) {
            echo json_encode(["response" => "00", "message" => "¡Contraseña actualizada con éxito! Ya puedes iniciar sesión."]);
        } else {
            echo json_encode(["response" => "01", "message" => $resultado['message']]);
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}