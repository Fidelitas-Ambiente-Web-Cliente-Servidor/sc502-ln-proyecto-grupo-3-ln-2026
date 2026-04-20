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

    // =========================
    // RECUPERAR CONTRASEÑA
    // =========================
    public function actualizarContrasena($correo, $telefono, $nueva_contrasena) {
        // 1. Verificamos si existe un usuario que coincida EXACTAMENTE con ese correo y teléfono
        $sql = "SELECT id_usuario FROM USUARIO WHERE correo = ? AND telefono = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $correo, $telefono);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 0) {
            return ["ok" => false, "message" => "Los datos no coinciden con ninguna cuenta registrada."];
        }

        // 2. Si coinciden, encriptamos la nueva contraseña y actualizamos
        $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $sqlUpdate = "UPDATE USUARIO SET contrasena = ? WHERE correo = ?";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ss", $hash, $correo);

        if($stmtUpdate->execute()) {
            return ["ok" => true];
        }
        
        return ["ok" => false, "message" => "Ocurrió un error al intentar actualizar la contraseña."];
    }
}