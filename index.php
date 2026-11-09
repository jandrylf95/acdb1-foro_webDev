<?php
//Solo usuarios autenticados pueden ver esta página
require_once 'auth.php';
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];

//Obtener los contactos correspondientes a este usuario
try {
    $db = (new Conexion())->conectar();
    $stmt = $db->prepare("SELECT * FROM contactos WHERE id_usuario = :id_usuario ORDER BY id_contacto DESC");
    $stmt->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $contactos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al consultar contactos: " . $e->getMessage());
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
                                <a href="eliminar_contacto.php?id=<?php echo $c['id_contacto']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este contacto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</body>
</html>