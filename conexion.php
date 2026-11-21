<?php

class Conexion {
    private $host;
    private $dbname;
    private $user;
    private $password;
    private $port;
    private $conexion;

    public function __construct() {
        // Detección automática del entorno (Local vs Producción)
        $isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']) 
                   || (php_sapi_name() === 'cli');

        if ($isLocal) {

            // CONFIGURACIÓN LOCAL
            $this->host     = "localhost";
            $this->dbname   = "acdb1-foro_webdev";
            $this->user     = "root";
            $this->password = "";
            $this->port     = "3307"; // Puerto local de MySQL
        } else {

            // CONFIGURACIÓN PRODUCCIÓN
            $this->host     = "sql309.infinityfree.com";        
            $this->dbname   = "if0_43494843_apeb1";      
            $this->user     = "if0_43494843";             
            $this->password = "9HJuwuAJv4";     
            $this->port     = "3306";   
        }
    }

    public function conectar() {
        $this->conexion = null;

        try {
            // Data Source Name (DSN) dinámico
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            
            // Instancia de PDO con opciones seguras
            $this->conexion = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Excepciones estrictas
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC        // Retorna arrays asociativos
            ]);

        } catch (PDOException $e) {
            error_log("Error de conexión BD: " . $e->getMessage()); // Registro técnico en log
            die("Error: No se pudo conectar a la base de datos. Intente más tarde.");
        }

        return $this->conexion;
    }
}
?>
