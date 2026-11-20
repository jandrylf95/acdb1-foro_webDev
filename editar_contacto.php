<?php
require_once 'auth.php';
require_once 'conexion.php';

$id_usuario = $_SESSION['usuario_id'];
$mensaje = "";
$contacto = null;

// Obtiene y valida el ID del contacto desde la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_contacto = intval($_GET['id']);
$db = (new Conexion())->conectar();
// Cargar lista de categorías para el selector
try {
    $stmtCat = $db->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
    $categorias = $stmtCat->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $categorias = [];
}

// Procesa el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $ciudad = trim($_POST['ciudad']);
     // Nuevos campos
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $favorito = isset($_POST['favorito']) ? 1 : 0;

    if (!empty($nombre) && !empty($telefono)) {
        if (!preg_match('/^[0-9]{10}$/', $telefono)) {
        $mensaje = "El teléfono debe contener únicamente números y un máximo de 10 dígitos.";
        } elseif (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "El formato del correo electrónico no es válido.";
            } else {
                try {
                    // Sentencia UPDATE (id_contacto y id_usuario)
                    $update = $db->prepare("UPDATE contactos 
                            SET id_categoria = :id_categoria,
                                nombre = :nombre, 
                                telefono = :telefono, 
                                correo = :correo, 
                                ciudad = :ciudad,
                                favorito = :favorito 
                            WHERE id_contacto = :id_contacto AND id_usuario = :id_usuario");
                    // Vincular categoría (permite NULL si no seleccionó ninguna)
                    if ($id_categoria === null) {
                        $update->bindValue(':id_categoria', null, PDO::PARAM_NULL);
                    } else {
                        $update->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                    }
                    $update->bindParam(':nombre', $nombre);
                    $update->bindParam(':telefono', $telefono);
                    $update->bindParam(':correo', $correo);
                    $update->bindParam(':ciudad', $ciudad);
                    $update->bindParam(':favorito', $favorito, PDO::PARAM_INT);
                    $update->bindParam(':id_contacto', $id_contacto, PDO::PARAM_INT);
                    $update->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                    

                    if ($update->execute()) {
                        header("Location: index.php");
                        exit();
                    }
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    $mensaje = "Ocurrió un error en el servidor. Por favor intente más tarde.";
                    $contacto = [
                        'nombre' => $nombre,
                        'telefono' => $telefono,
                        'correo' => $correo,
                        'ciudad' => $ciudad,
                        'id_categoria' => $id_catego,                ria,
                        'favorito' => $favorito
                    ];
                }
            }    
    } else {
        $mensaje = "Nombre y Teléfono son obligatorios.";
        $contacto = [
            'nombre' => $nombre,
            'telefono' => $telefono,
            'correo' => $correo,
            'ciudad' => $ciudad,
            'id_categoria' => $id_categoria,
            'favorito' => $favorito
        ];
    }
}

// Carga los datos actuales de la base de datos si es petición GET inicial
if ($contacto === null) {
    try {
        $stmt = $db->prepare("SELECT * FROM contactos WHERE id_contacto = :id_contacto AND id_usuario = :id_usuario");
        $stmt->bindParam(':id_contacto', $id_contacto, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $contacto = $stmt->fetch();

        if (!$contacto) {
            // Si el contacto no existe o pertenece a otro usuario, redirige
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        die("Ocurrió un error en el servidor. Por favor intente más tarde.");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Contacto</title>
</head>
<body>
    <h2>Editar Contacto</h2>

    <?php if (!empty($mensaje)): ?>
        <p style="color: red;"><strong><?php echo $mensaje; ?></strong></p>
    <?php endif; ?>

    <form action="editar_contacto.php?id=<?php echo $id_contacto; ?>" method="POST">
        <input type="hidden" name="id_contacto" value="<?php echo $id_contacto; ?>">

        <div>
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($contacto['nombre']); ?>" required>
        </div>
        <br>
        <div>
            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($contacto['telefono']); ?>" maxlength="10" pattern="[0-9]{10}" inputmode="numeric"
            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
            title="Solo se permiten 10 números" required>
        </div>
        <br>
        <div>
            <label>Correo:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($contacto['correo']); ?>">
        </div>
        <br>
        <div>
            <label>Ciudad:</label>
            <input type="text" name="ciudad" value="<?php echo htmlspecialchars($contacto['ciudad']); ?>">
        </div>
        <br>
         <div>
            <label>Categoría:</label>
            <select name="id_categoria">
                <option value="">-- Sin categoría --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>" 
                        <?php if (isset($contacto['id_categoria']) && $contacto['id_categoria'] == $cat['id_categoria']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label>
                <input type="checkbox" name="favorito" value="1" <?php if (!empty($contacto['favorito'])) echo 'checked'; ?>>
                ⭐ Marcar como favorito
            </label>
        </div>
        <br>
        <button type="submit">Actualizar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
