<?php
require_once '../config/database.php';
session_start();

// Función para obtener un administrador activo de forma aleatoria
function obtenerPrimerAdministrador($conn) {
    $sql = "SELECT u.idusuario, u.nombre 
        FROM usuario u
        INNER JOIN usuario_has_rol ur ON u.idusuario = ur.usuario_idusuario
        INNER JOIN rol r ON ur.rol_idrol = r.idrol
        WHERE r.nombre_rol = 'administrador'
          AND ur.estado = 'activo'
        ORDER BY RAND()
        LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row;
    }
    return null;
}

// Función para obtener el nombre del remitente
function obtenerNombreRemitente($conn, $remitenteId) {
    $stmt = $conn->prepare("SELECT nombre FROM usuario WHERE idusuario = ?");
    $stmt->bind_param("i", $remitenteId);
    $stmt->execute();
    $nombre = null;
    $stmt->bind_result($nombre);
    $stmt->fetch();
    $stmt->close();
    return $nombre ?? '';
}

// Procesar formulario
$mensajeEnviado = false;
$conn = new mysqli("localhost", "root", "", "Lugyser");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$admin = obtenerPrimerAdministrador($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = trim($_POST['mensaje'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $tipo_notificacion = trim($_POST['tipo_notificacion'] ?? 'Mensaje');
    if ($admin && $mensaje && $asunto) {
        $remitenteId = $_SESSION['usuario_id'] ?? null;
        $remitenteNombre = $remitenteId ? obtenerNombreRemitente($conn, $remitenteId) : '';
        $stmt = $conn->prepare("INSERT INTO notificacion (fecha_notificacion, tipo_notificacion, asunto, remitente, destino, mensaje) VALUES (CURDATE(), ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $tipo_notificacion, $asunto, $remitenteNombre, $admin['nombre'], $mensaje);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $mensajeEnviado = true;
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Enviar Mensaje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
        }
        .card {
            margin-top: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .btn-primary {
            background: linear-gradient(90deg, #4f8cff 0%, #3358ff 100%);
            border: none;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php
// Incluir pie de página
include '../includes/header.php';
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card p-4">
                <h3 class="mb-4 text-center">Enviar Mensaje a soporte</h3>
                <?php if ($mensajeEnviado): ?>
                    <div class="alert alert-success">¡Mensaje enviado correctamente!</div>
                <?php endif; ?>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="destinatario" class="form-label">Destinatario</label>
                        <input type="text" class="form-control" id="destinatario" value="<?= htmlspecialchars($admin['nombre'] ?? 'Administrador') ?>" disabled>
                        <input type="hidden" name="destinatario" value="<?= htmlspecialchars($admin['idusuario'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="asunto" class="form-label">Asunto</label>
                        <input type="text" class="form-control" id="asunto" name="asunto" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_notificacion" class="form-label">Tipo de Notificación</label>
                        <select class="form-select" id="tipo_notificacion" name="tipo_notificacion">
                            <option value="Mensaje">Mensaje</option>
                            <option value="Consulta">Consulta</option>
                            <option value="Sugerencia">Sugerencia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="mensaje" class="form-label">Mensaje</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="5" maxlength="500" required></textarea>
                    </div>
                    <div class="d-grid">
<?php if ($mensajeEnviado): ?>
    <script>
        setTimeout(function () {
            const ref = document.referrer;
            if (ref) {
                window.location.href = ref;
            } else {
                window.location.href = '../index.php'; // respaldo si no hay referrer
            }
        }, 1500);
    </script>
<?php endif; ?>

                        <button type="submit" class="btn btn-primary btn-lg">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
