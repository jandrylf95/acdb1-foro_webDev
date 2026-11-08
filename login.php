<?php
// 1. Siempre iniciar la sesión al comienzo del archivo
session_start();

// Si el usuario ya está autenticado, lo redirigimos a la página privada principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'conexion.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if (!empty($correo) && !empty($password)) {
        try {
            $db = (new Conexion())->conectar();
            // Buscar el usuario por correo
            $stmt = $db->prepare("SELECT id_usuario, nombre, password FROM usuarios WHERE correo = :correo");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            $usuario = $stmt->fetch(); // Obtener el registro como arreglo asociativo

            // 2. Comprobar si el usuario existe Y verificar la contraseña encriptada
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Autenticación correcta: Almacenar datos en la variable superglobal $_SESSION
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];

                // Redirigir a la agenda privada
                header("Location: index.php");
                exit();
            } else {
                $error = "Credenciales incorrectas. Verifique su correo o contraseña.";
            }
        } catch (PDOException $e) {
            $error = "Error en el servidor: " . $e->getMessage();
        }
    } else {
        $error = "Por favor ingrese correo y contraseña.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Agenda</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><strong><?php echo $error; ?></strong></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
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
        <button type="submit">Ingresar</button>
    </form>

    <p><a href="registro.php">¿No tienes cuenta? Regístrate aquí</a></p>
</body>
</html> 