<?php
session_start();
require_once '../config/database.php';
require_once '../models/LugarModel.php';

class PublicarFincaController {
    public function publicar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = new Database();
            $conn = $db->getConnection();
            $lugarModel = new LugarModel($conn);

            // Depuración: Registrar los datos enviados
            error_log("Datos enviados desde el formulario: " . json_encode($_POST));
            error_log("Archivo subido: " . json_encode($_FILES['imagen_lugar']));

            // Validar los datos del formulario
            $nombre_lugar = trim($_POST['nombre_lugar']);
            $ubicacion_lugar = trim($_POST['ubicacion_lugar']);
            $descripcion_lugar = trim($_POST['descripcion_lugar']);
            $cantidad_habitaciones = intval($_POST['cantidad_habitaciones']);
            $precio_lugar = floatval($_POST['precio_lugar']);
            $tipo = trim($_POST['tipo']);
            $disponibilidad_lugar = isset($_POST['disponibilidad_lugar']) ? 1 : 0;
            $cantidad_banos = intval($_POST['cantidad_banos']);
            $cantidad_piscinas = intval($_POST['cantidad_piscinas']);
            $juegos_infantiles = isset($_POST['juegos_infantiles']) ? 1 : 0;
            $zonas_verdes = isset($_POST['zonas_verdes']) ? 1 : 0;
            $usuario_id = $_SESSION['usuario']['idusuario'];
            $rol_id = 2; // Rol proveedor

            // Depuración: Registrar los valores de usuario y rol
            error_log("Usuario ID: " . $usuario_id);
            error_log("Rol ID: " . $rol_id);

            // Validar campos obligatorios
            if (empty($nombre_lugar) || empty($ubicacion_lugar) || empty($descripcion_lugar) || empty($_FILES['imagen_lugar']['name'])) {
                header('Location: ../views/publicar_finca.php?error=campos_obligatorios');
                exit;
            }

            // Validar que el usuario y el rol existen en las tablas relacionadas
            $queryUsuario = "SELECT idusuario FROM usuario WHERE idusuario = :usuario_id";
            $stmtUsuario = $conn->prepare($queryUsuario);
            $stmtUsuario->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtUsuario->execute();
            if ($stmtUsuario->rowCount() === 0) {
                error_log("Error: El usuario con ID $usuario_id no existe.");
                header('Location: ../views/publicar_finca.php?error=usuario_invalido');
                exit;
            }

            $queryRol = "SELECT idrol FROM rol WHERE idrol = :rol_id";
            $stmtRol = $conn->prepare($queryRol);
            $stmtRol->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
            $stmtRol->execute();
            if ($stmtRol->rowCount() === 0) {
                error_log("Error: El rol con ID $rol_id no existe.");
                header('Location: ../views/publicar_finca.php?error=rol_invalido');
                exit;
            }

            // Validar que el usuario y el rol existen en la tabla `usuario_has_rol`
            $queryUsuarioRol = "SELECT * FROM usuario_has_rol WHERE usuario_idusuario = :usuario_id AND rol_idrol = :rol_id";
            $stmtUsuarioRol = $conn->prepare($queryUsuarioRol);
            $stmtUsuarioRol->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmtUsuarioRol->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
            $stmtUsuarioRol->execute();

            if ($stmtUsuarioRol->rowCount() === 0) {
                error_log("Error: No existe una relación válida entre el usuario con ID $usuario_id y el rol con ID $rol_id.");
                header('Location: ../views/publicar_finca.php?error=relacion_invalida');
                exit;
            }

            // Mover la imagen subida a la carpeta de destino
            $target_dir = "../uploads/";
            $imagen_lugar = basename($_FILES['imagen_lugar']['name']);
            $target_file = $target_dir . $imagen_lugar;
            if (!move_uploaded_file($_FILES['imagen_lugar']['tmp_name'], $target_file)) {
                error_log("Error al mover la imagen al directorio destino.");
                header('Location: ../views/publicar_finca.php?error=imagen');
                exit;
            }

            // Preparar los datos para insertar
            $datos = [
                'nombre_lugar' => $nombre_lugar,
                'imagen_lugar' => file_get_contents($target_file),
                'tipo' => $tipo,
                'ubicacion_lugar' => $ubicacion_lugar,
                'descripcion_lugar' => $descripcion_lugar,
                'cantidad_habitaciones' => $cantidad_habitaciones,
                'disponibilidad_lugar' => $disponibilidad_lugar,
                'precio_lugar' => $precio_lugar,
                'usuario_idusuario' => $usuario_id,
                'rol_idrol' => $rol_id,
                'cantidad_banos' => $cantidad_banos,
                'cantidad_piscinas' => $cantidad_piscinas,
                'juegos_infantiles' => $juegos_infantiles,
                'zonas_verdes' => $zonas_verdes,
            ];

            // Insertar los datos en la base de datos
            if ($lugarModel->insertarLugar($datos)) {
                header('Location: ../views/listar_fincas.php?mensaje=publicado');
            } else {
                error_log("Error al insertar lugar: " . json_encode($datos));
                header('Location: ../views/publicar_finca.php?error=fallo');
            }
        }
    }
}

// Instanciar el controlador y llamar al método
$controller = new PublicarFincaController();
$controller->publicar();
?>
