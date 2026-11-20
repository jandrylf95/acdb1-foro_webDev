<?php

class Conexion {
    private $host = "localhost";
    private $dbname = "acdb1-foro_webdev";
    private $user = "root";
    private $password = "";
    private $conexion;

    public function conectar() {
        $this->conexion = null;

        try {
            // Data Source Name (DSN)
            $dsn = "mysql:host=" . $this->host . ";port=3307;dbname=" . $this->dbname . ";charset=utf8mb4";
            
            // Instancia de PDO con opciones de configuración
            $this->conexion = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activa el manejo estricto de excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Retorna arrays asociativos
            ]);

        } catch (PDOException $e) {
            error_log($e->getMessage()); // Guarda el detalle técnico en el log del servidor
            die("Error: No se pudo conectar a la base de datos. Intente más tarde.");
        }

        return $this->conexion;
    }
}
?>