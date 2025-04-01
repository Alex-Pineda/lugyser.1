<?php
class AuthController {
    private $usuarioModel;
    private $rolModel;

    public function __construct($usuarioModel, $rolModel) {
        $this->usuarioModel = $usuarioModel;
        $this->rolModel = $rolModel;
    }

    public function login($nombre_usuario, $contrasena) {
        $usuario = $this->usuarioModel->autenticarUsuario($nombre_usuario, $contrasena);
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['roles'] = $this->rolModel->obtenerRolesPorUsuario($usuario['idusuario']);

            // Redirigir según el rol
            $this->redirigirSegunRol($_SESSION['roles']);
        } else {
            return false; // Credenciales incorrectas
        }
    }

    private function redirigirSegunRol($roles) {
        foreach ($roles as $rol) {
            if ($rol['nombre_rol'] === 'administrador') {
                header("Location: ../views/admin_dashboard.php"); // Redirigir al panel de administrador
                exit;
            } elseif ($rol['nombre_rol'] === 'proveedor') {
                header("Location: ../views/proveedor_dashboard.php"); // Redirigir al dashboard de proveedor
                exit;
            } elseif ($rol['nombre_rol'] === 'cliente') {
                header("Location: ../views/reservar_finca.php"); // Redirigir al dashboard de cliente
                exit;
            }
        }
        header("Location: ../index.php"); // Redirección por defecto
        exit;
    }

    public function verificarAcceso($rolRequerido) {
        if (!isset($_SESSION['roles'])) {
            return false;
        }
        foreach ($_SESSION['roles'] as $rol) {
            if ($rol['nombre_rol'] === $rolRequerido) {
                return true;
            }
        }
        return false;
    }

    public function register($datos) {
        $resultado = $this->usuarioModel->registrarUsuario($datos);
        if ($resultado) {
            header("Location: /lugyser/index.php"); // Redirigir al inicio de sesión después del registro
            exit;
        } else {
            echo "Error al registrar el usuario. Por favor, inténtelo de nuevo.";
        }
    }
}
?>
