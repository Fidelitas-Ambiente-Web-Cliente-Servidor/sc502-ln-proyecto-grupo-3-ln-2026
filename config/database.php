<?php

class Database
{
    private $host = "db"; 
    private $db = "appdb";
    private $user = "appuser";
    private $pass = "apppass";

    public function connect()
    {
        $conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->db
        );

        if ($conn->connect_error) {
            die("Error conexión: " . $conn->connect_error);
        }

        //Para que los caracteres en español (tildes, ñ) se guarden correctamente en la base de datos
        $conn->set_charset("utf8");

        return $conn;
    }
}