<?php
//Solo usuarios autenticados pueden ver esta página
require_once 'auth.php';
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
//correccion de la retroalimentacion 
$usuario_correo = $_SESSION['usuario_correo'] ?? ''; 

//Obtener los contactos correspondientes a este usuario
try {
    $db = (new Conexion())->conectar();
    $stmt = $db->prepare("SELECT * FROM contactos WHERE id_usuario = :id_usuario ORDER BY id_contacto DESC");
    $stmt->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $contactos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Ocurrió un error al cargar los contactos. Intente más tarde.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal - Agenda</title>
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?>!</h1>
        <!--Correccion de retroalimentacion-->
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario_correo); ?></p>
        <p>Has iniciado sesión correctamente en tu agenda personal.</p>
        <p>
            <a href="logout.php">Cerrar Sesión</a>
        </p>
    </header>

    <hr>

    <section>
        <h2>Mis Contactos</h2>
        <p><a href="crear_contacto.php">+ Agregar Nuevo Contacto</a></p>

        <?php if (empty($contactos)): ?>
            <p>No tienes contactos registrados en tu agenda aún.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Ciudad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contactos as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($c['correo']); ?></td>
                            <td><?php echo htmlspecialchars($c['ciudad']); ?></td>
                            <td>
                                <a href="editar_contacto.php?id=<?php echo $c['id_contacto']; ?>">Editar</a> |
                                <form action="eliminar_contacto.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este contacto?');">
                                    <input type="hidden" name="id_contacto" value="<?php echo $c['id_contacto']; ?>">
                                    <button type="submit" style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; padding:0; font:inherit;">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</body>
</html>