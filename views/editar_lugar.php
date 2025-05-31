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
<?php
include_once __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Lugar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">

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
    <div class="main-bg">
        <div class="container mt-5">
            <h1 class="text-center mb-4">Editar Lugar</h1>
            <form action="../controllers/actualizar_lugar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="idlugar" value="<?php echo htmlspecialchars($lugar['idlugar']); ?>">

                <div class="row">
                    <!-- Primera columna -->
                    <div class="col-md-6 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-3">
                                <label for="nombre_lugar" class="form-label font-weight-bold">Nombre del Lugar</label>
                                <input type="text" id="nombre_lugar" name="nombre_lugar" class="form-control" value="<?php echo htmlspecialchars($lugar['nombre_lugar']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="ubicacion_lugar" class="form-label font-weight-bold">Ubicación</label>
                                <input type="text" id="ubicacion_lugar" name="ubicacion_lugar" class="form-control" value="<?php echo htmlspecialchars($lugar['ubicacion_lugar']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion_lugar" class="form-label font-weight-bold">Descripción</label>
                                <textarea id="descripcion_lugar" name="descripcion_lugar" class="form-control" rows="6" required><?php echo htmlspecialchars($lugar['descripcion_lugar']); ?></textarea>
                            </div>
                        </div>
                        <div>
                            <div class="mb-3">
                                <label for="cantidad_habitaciones" class="form-label font-weight-bold">Cantidad de Habitaciones</label>
                                <input type="number" id="cantidad_habitaciones" name="cantidad_habitaciones" class="form-control" value="<?php echo htmlspecialchars($lugar['cantidad_habitaciones']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="precio_lugar" class="form-label font-weight-bold">Precio</label>
                                <input type="number" id="precio_lugar" name="precio_lugar" class="form-control" value="<?php echo htmlspecialchars($lugar['precio_lugar']); ?>" required>
                            </div>                            
                        </div>
                    </div>

                    <!-- Segunda columna -->
                    <div class="col-md-6 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-3">
                                <label for="tipo" class="form-label font-weight-bold">Tipo</label>
                                <input type="text" id="tipo" name="tipo" class="form-control" value="<?php echo htmlspecialchars($lugar['tipo']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="disponibilidad_lugar" class="form-label font-weight-bold">Disponibilidad</label>
                                <div class="form-check">
                                    <input type="checkbox" id="disponibilidad_lugar" name="disponibilidad_lugar" class="form-check-input" <?php echo $lugar['disponibilidad_lugar'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="disponibilidad_lugar">Disponible</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="cantidad_banos" class="form-label font-weight-bold">Cantidad de Baños</label>
                                <input type="number" id="cantidad_banos" name="cantidad_banos" class="form-control" value="<?php echo htmlspecialchars($lugar['cantidad_banos']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="cantidad_piscinas" class="form-label font-weight-bold">Cantidad de Piscinas</label>
                                <input type="number" id="cantidad_piscinas" name="cantidad_piscinas" class="form-control" value="<?php echo htmlspecialchars($lugar['cantidad_piscinas']); ?>" required>
                            </div>
                        </div>
                        <div>
                            <div class="mb-3">
                                <label for="juegos_infantiles" class="form-label font-weight-bold">Juegos Infantiles</label>
                                <div class="form-check">
                                    <input type="checkbox" id="juegos_infantiles" name="juegos_infantiles" class="form-check-input" <?php echo $lugar['juegos_infantiles'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="juegos_infantiles">Incluye</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="zonas_verdes" class="form-label font-weight-bold">Zonas Verdes</label>
                                <div class="form-check">
                                    <input type="checkbox" id="zonas_verdes" name="zonas_verdes" class="form-check-input" <?php echo $lugar['zonas_verdes'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="zonas_verdes">Incluye</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="imagen_lugar" class="form-label font-weight-bold">Imagen</label>
                                <input type="file" id="imagen_lugar" name="imagen_lugar" class="form-control">
                                <small class="form-text text-muted">Deja este campo vacío si no deseas cambiar la imagen.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-4">Actualizar</button>
                    <a href="javascript:history.back()" class="btn btn-secondary px-4 ml-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <style>
        body, html {
            background: #f0f4f8 !important;
        }
        .main-bg {
            min-height: 100vh;
            background: linear-gradient(135deg, #e3eafc 0%, #f0f4f8 100%);
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .form-label {
            color: #343a40;
        }
        .form-control, .form-check-input {
            border-radius: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
            border: none;
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
        .container {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2rem 2.5rem;
            max-width: 900px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .col-md-6 {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
        }
        @media (max-width: 767.98px) {
            .container {
                padding: 1rem;
            }
            .row {
                flex-direction: column;
            }
            .col-md-6 {
                min-height: unset;
            }
        }
    </style>
</body>
</html>
<?php
// Incluir pie de página
include '../includes/footer.php';
?>
