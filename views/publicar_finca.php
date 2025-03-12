<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Finca</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="card bg-light text-dark">
            <div class="card-body">
                <h2 class="card-title text-center">Publicar Finca</h2>
                <nav class="nav justify-content-center mb-4">
                    <a class="nav-link" href="listar_fincas.php">Ver Fincas Publicadas</a>
                    <a class="nav-link" href="publicar_finca.php">Publicar Finca</a>
                    <a class="nav-link" href="reservar_finca.php">Reservar Finca</a>
                </nav>
                <form action="../controllers/PublicarFincaController.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_lugar" class="form-label">Nombre del lugar <span class="text-danger">*</span>:</label>
                            <input type="text" id="nombre_lugar" name="nombre_lugar" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ubicacion_lugar" class="form-label">Ubicación del lugar <span class="text-danger">*</span>:</label>
                            <input type="text" id="ubicacion_lugar" name="ubicacion_lugar" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="descripcion_lugar" class="form-label">Descripción del lugar <span class="text-danger">*</span>:</label>
                            <textarea id="descripcion_lugar" name="descripcion_lugar" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cantidad_habitaciones" class="form-label">Cantidad de habitaciones <span class="text-danger">*</span>:</label>
                            <input type="number" id="cantidad_habitaciones" name="cantidad_habitaciones" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="precio_lugar" class="form-label">Precio del lugar <span class="text-danger">*</span>:</label>
                            <input type="number" id="precio_lugar" name="precio_lugar" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span>:</label>
                            <input type="text" id="tipo" name="tipo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="disponibilidad_lugar" class="form-label">Disponibilidad:</label>
                            <input type="checkbox" id="disponibilidad_lugar" name="disponibilidad_lugar" class="form-check-input">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cantidad_banos" class="form-label">Cantidad de baños <span class="text-danger">*</span>:</label>
                            <input type="number" id="cantidad_banos" name="cantidad_banos" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cantidad_piscinas" class="form-label">Cantidad de piscinas <span class="text-danger">*</span>:</label>
                            <input type="number" id="cantidad_piscinas" name="cantidad_piscinas" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="juegos_infantiles" class="form-label">Juegos infantiles:</label>
                            <input type="checkbox" id="juegos_infantiles" name="juegos_infantiles" class="form-check-input">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="zonas_verdes" class="form-label">Zonas verdes:</label>
                            <input type="checkbox" id="zonas_verdes" name="zonas_verdes" class="form-check-input">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="imagen_lugar" class="form-label">Imagen del lugar <span class="text-danger">*</span>:</label>
                            <input type="file" id="imagen_lugar" name="imagen_lugar" class="form-control" required>
                        </div>
                        <input type="hidden" name="usuario_id" value="1">
                        <input type="hidden" name="rol_id" value="1">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-danger w-100" onclick="window.history.back()">Cancelar</button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="submit" class="btn btn-warning w-100">Publicar</button>
                        </div>
                    </div>
                    <p class="text-center">
                        Al registrarte, aceptas los <a href="#" class="text-primary">términos y condiciones</a>.
                    </p>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
