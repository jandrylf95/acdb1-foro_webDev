<?php
//Solo usuarios autenticados pueden ver esta página
require_once 'auth.php';
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
//correccion de la retroalimentacion 
$usuario_correo = $_SESSION['usuario_correo'] ?? ''; 

// Nuevos parámetros de búsqueda, filtro y ordenamiento (v2.0)
$busqueda = trim($_GET['buscar'] ?? '');
$solo_favoritos = isset($_GET['favoritos']) && $_GET['favoritos'] === '1';
$orden = $_GET['orden'] ?? 'recientes';

// Lista blanca para ordenamiento dinámico seguro (Mejora B)
$ordenes_validos = [
    'recientes'   => 'c.id_contacto DESC',
    'antiguos'    => 'c.id_contacto ASC',
    'nombre_asc'  => 'c.nombre ASC',
    'nombre_desc' => 'c.nombre DESC',
    'ciudad_asc'  => 'c.ciudad ASC'
];
$columna_orden = $ordenes_validos[$orden] ?? 'c.id_contacto DESC';

// Obtener los contactos correspondientes a este usuario con mejoras de búsqueda, categoría y favoritos
try {
    $db = (new Conexion())->conectar();

    // Consulta con LEFT JOIN para incluir el nombre de la categoría (Mejora C)
    $sql = "SELECT c.*, cat.nombre AS categoria_nombre 
            FROM contactos c
            LEFT JOIN categorias cat ON c.id_categoria = cat.id_categoria 
            WHERE c.id_usuario = :id_usuario";

    // Filtro de búsqueda por Nombre, Correo o Ciudad (Mejora A)
    if (!empty($busqueda)) {
        $sql .= " AND (c.nombre LIKE :busqueda OR c.correo LIKE :busqueda OR c.ciudad LIKE :busqueda)";
    }

    // Filtro para mostrar solo favoritos (Mejora D)
    if ($solo_favoritos) {
        $sql .= " AND c.favorito = 1";
    }

    // Aplicar orden dinámico
    $sql .= " ORDER BY " . $columna_orden;

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);

    if (!empty($busqueda)) {
        $termino = "%" . $busqueda . "%";
        $stmt->bindParam(':busqueda', $termino);
    }

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
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
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

        <!-- Barra de Búsqueda, Filtro y Ordenamiento (v2.0) -->
        <form method="GET" action="index.php" class="barra-filtros">
            <input 
                type="text" 
                name="buscar" 
                placeholder="Buscar por nombre, correo o ciudad..." 
                value="<?php echo htmlspecialchars($busqueda); ?>"
                size="35">

            <select name="orden">
                <option value="recientes" <?php if ($orden === 'recientes') echo 'selected'; ?>>Más recientes</option>
                <option value="antiguos" <?php if ($orden === 'antiguos') echo 'selected'; ?>>Más antiguos</option>
                <option value="nombre_asc" <?php if ($orden === 'nombre_asc') echo 'selected'; ?>>Nombre (A - Z)</option>
                <option value="nombre_desc" <?php if ($orden === 'nombre_desc') echo 'selected'; ?>>Nombre (Z - A)</option>
                <option value="ciudad_asc" <?php if ($orden === 'ciudad_asc') echo 'selected'; ?>>Ciudad (A - Z)</option>
            </select>

            <label style="margin-left: 10px;">
                <input type="checkbox" name="favoritos" value="1" <?php if ($solo_favoritos) echo 'checked'; ?>>
                ⭐ Solo favoritos
            </label>

            <button type="submit">Filtrar</button>
            <?php if (!empty($busqueda) || $solo_favoritos || $orden !== 'recientes'): ?>
                <a href="index.php" style="margin-left: 10px;">Limpiar filtros</a>
            <?php endif; ?>
        </form>

        <?php if (empty($contactos)): ?>
            <p>No se encontraron contactos con los criterios seleccionados.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Fav</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Ciudad</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contactos as $c): ?>
                        <tr>
                            <td align="center"><?php echo ($c['favorito'] == 1) ? '⭐' : '—'; ?></td>
                            <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($c['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                            <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($c['correo']); ?></td>
                            <td><?php echo htmlspecialchars($c['ciudad']); ?></td>
                            <td><?php echo !empty($c['fecha_creacion']) ? date('d/m/Y', strtotime($c['fecha_creacion'])) : '—'; ?></td>
                            <td>
                                <a href="editar_contacto.php?id=<?php echo $c['id_contacto']; ?>">Editar</a> |
                                <form action="eliminar_contacto.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este contacto?');">
                                    <input type="hidden" name="id_contacto" value="<?php echo $c['id_contacto']; ?>">
                                    <button type="submit" class="btn-eliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
    </div>
</body>
</html>