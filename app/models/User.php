<?php

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //Buscar usuario por correo para el Login
    public function login($correo)
    {
        $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc(); //Retorna los datos del usuario o null si no existe
    }

    //Registrar un nuevo usuario
    public function registrar($nombre, $apellidos, $telefono, $correo, $contrasena_hash)
    {
        //1.Insertamos al usuario
        $stmt = $this->conn->prepare(
            "INSERT INTO USUARIO (nombre, apellidos, correo, contrasena, telefono) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $nombre, $apellidos, $correo, $contrasena_hash, $telefono);

        if ($stmt->execute()) {
            $id_usuario = $this->conn->insert_id; // Obtenemos el ID que se acaba de crear

            //2.Le asignamos un rol por defecto (Asumimos que el rol "Pasajero" tiene ID=2 en la tabla ROL)
            $rol_default = 2;
            $stmt_rol = $this->conn->prepare("INSERT INTO USUARIO_ROL (id_usuario, id_rol) VALUES (?, ?)");
            $stmt_rol->bind_param("ii", $id_usuario, $rol_default);
            $stmt_rol->execute();

            return true;
        }
        return false;
    }
}