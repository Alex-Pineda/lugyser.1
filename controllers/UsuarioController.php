<?php
include_once __DIR__ . '/../models/Usuario.php';
include_once __DIR__ . '/../includes/footer.php';

class UsuarioController {
    private $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    public function login($nombre, $password) {
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre = ? AND password = ?");
        $stmt->bindParam(1, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(2, $password, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol']; // Guardar el rol del usuario en la sesión

            // Redirigir según el rol
            if ($user['rol'] === 'admin') {
                header('Location: ../views/admin_dashboard.php');
            } elseif ($user['rol'] === 'proveedor') {
                header('Location: ../views/proveedor_dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            return false;
        }
    }

    public function register($nombre, $email, $password, $telefono, $direccion, $ciudad, $pais, $codigo_postal) {
        return $this->usuario->register($nombre, $email, $password, $telefono, $direccion, $ciudad, $pais, $codigo_postal);
    }

    public function getUsuarioById($idusuario) {
        return $this->usuario->getUsuarioById($idusuario);
    }

    public function getAllUsuarios() {
        return $this->usuario->getAllUsuarios();
    }
}
?>
