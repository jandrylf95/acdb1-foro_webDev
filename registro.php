<?php
require_once 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if (!empty($nombre) && !empty($correo) && !empty($password)) {
        // Corrección de retroalimentación: validar formato del correo
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "El formato del correo electrónico no es válido.";
        } else {
            // Encripta la contraseña
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $db = (new Conexion())->conectar();
                // Prepara la consulta SQL de inserción
                $stmt = $db->prepare("INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':password', $passwordHash);

                if ($stmt->execute()) {
                    $mensaje = "¡Registro exitoso! Ya puedes iniciar sesión.";
                }
            } catch (PDOException $e) {
                // El código de error 23000 indica un correo duplicado (UNIQUE)
                if ($e->getCode() == 23000) {
                    $mensaje = "Error: El correo ya está registrado.";
                } else {
                    error_log($e->getMessage());
                    $mensaje = "Ocurrió un error en el servidor. Por favor intente más tarde.";
                }
            }
        }
    } else {
        $mensaje = "Por favor completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
    <h2>Registro de Usuario</h2>
    
    <?php if (!empty($mensaje)): ?>
        <p class="mensaje-exito"><strong><?php echo $mensaje; ?></strong></p>
    <?php endif; ?>

    <form action="registro.php" method="POST">
        <div>
            <label>Nombre:</label>
            <input type="text" name="nombre" required>
        </div>
        <br>
        <div>
            <label>Correo Electrónico:</label>
            <input type="email" name="correo" required>
        </div>
        <br>
        <div>
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>
        <br>
        <button type="submit">Registrarse</button>
    </form>

    <p style="margin-top: 15px;"><a href="login.php">¿Ya tienes cuenta? Inicia sesión aquí</a></p>
    </div>
</body>
</html>