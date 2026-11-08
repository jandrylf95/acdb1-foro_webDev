<?php
require_once 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if (!empty($nombre) && !empty($correo) && !empty($password)) {
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
                $mensaje = "Error al registrar: " . $e->getMessage();
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
</head>
<body>
    <h2>Registro de Usuario</h2>
    
    <?php if (!empty($mensaje)): ?>
        <p><strong><?php echo $mensaje; ?></strong></p>
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

    <p><a href="login.php">¿Ya tienes cuenta? Inicia sesión aquí</a></p>
</body>
</html>