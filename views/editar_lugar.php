<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); // Redirigir al nuevo index.php si no hay sesión
    exit;
}

require_once '../config/database.php';
require_once '../models/LugarModel.php';

$db = new Database();
$conn = $db->getConnection();
$lugarModel = new LugarModel($conn);

// Obtener los datos del lugar a editar
$idlugar = isset($_GET['id']) ? intval($_GET['id']) : 0;
$lugar = $lugarModel->obtenerLugarPorId($idlugar);

if (!$lugar) {
    header("Location: ../index.php?error=lugar_no_encontrado");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Lugar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>

html, body {
    margin: 0;
    padding: 0;
    height: auto;
    overflow-x: hidden;
    padding-bottom: 100px; /* Aumenta este valor según necesites */
}
</style>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Editar Lugar</h1>
        <form action="../controllers/actualizar_lugar.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="idlugar" value="<?php echo htmlspecialchars($lugar['idlugar']); ?>">

            <div class="mb-3">
                <label for="nombre_lugar" class="form-label">Nombre del Lugar</label>
                <input type="text" id="nombre_lugar" name="nombre_lugar" class="form-control" value="<?php echo htmlspecialchars($lugar['nombre_lugar']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="ubicacion_lugar" class="form-label">Ubicación</label>
                <input type="text" id="ubicacion_lugar" name="ubicacion_lugar" class="form-control" value="<?php echo htmlspecialchars($lugar['ubicacion_lugar']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="descripcion_lugar" class="form-label">Descripción</label>
                <textarea id="descripcion_lugar" name="descripcion_lugar" class="form-control" rows="4" required><?php echo htmlspecialchars($lugar['descripcion_lugar']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="cantidad_habitaciones" class="form-label">Cantidad de Habitaciones</label>
                <input type="number" id="cantidad_habitaciones" name="cantidad_habitaciones" class="form-control" value="<?php echo htmlspecialchars($lugar['cantidad_habitaciones']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="precio_lugar" class="form-label">Precio</label>
                <input type="number" id="precio_lugar" name="precio_lugar" class="form-control" value="<?php echo htmlspecialchars($lugar['precio_lugar']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" id="tipo" name="tipo" class="form-control" value="<?php echo htmlspecialchars($lugar['tipo']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="disponibilidad_lugar" class="form-label">Disponibilidad</label>
                <input type="checkbox" id="disponibilidad_lugar" name="disponibilidad_lugar" <?php echo $lugar['disponibilidad_lugar'] ? 'checked' : ''; ?>>
            </div>

            <div class="mb-3">
                <label for="cantidad_banos" class="form-label">Cantidad de Baños</label>
                <input type="number" id="cantidad_banos" name="cantidad_banos" class="form-control" value="<?php echo htmlspecialchars($lugar['cantidad_banos']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="cantidad_piscinas" class="form-label">Cantidad de Piscinas</label>
                <input type="number" id="cantidad_piscinas" name="cantidad_piscinas" class="form-control" value="<?php echo htmlspecialchars($lugar['cantidad_piscinas']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="juegos_infantiles" class="form-label">Juegos Infantiles</label>
                <input type="checkbox" id="juegos_infantiles" name="juegos_infantiles" <?php echo $lugar['juegos_infantiles'] ? 'checked' : ''; ?>>
            </div>

            <div class="mb-3">
                <label for="zonas_verdes" class="form-label">Zonas Verdes</label>
                <input type="checkbox" id="zonas_verdes" name="zonas_verdes" <?php echo $lugar['zonas_verdes'] ? 'checked' : ''; ?>>
            </div>

            <div class="mb-3">
                <label for="imagen_lugar" class="form-label">Imagen</label>
                <input type="file" id="imagen_lugar" name="imagen_lugar" class="form-control">
                <small>Deja este campo vacío si no deseas cambiar la imagen.</small>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="../index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
