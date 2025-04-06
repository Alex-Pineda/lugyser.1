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
            $roles = $this->rolModel->obtenerRolesPorUsuario($usuario['idusuario']);

            // Priorizar el rol de proveedor si está presente
            $proveedorRol = array_filter($roles, function ($rol) {
                return $rol['nombre_rol'] === 'proveedor';
            });

            if (!empty($proveedorRol)) {
                $_SESSION['roles'] = $proveedorRol; // Solo asignar el rol de proveedor
            } else {
                $_SESSION['roles'] = $roles; // Asignar todos los roles si no es proveedor
            }

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
                header("Location: ../index.php"); // Redirigir al dashboard de cliente
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
        // Validar datos antes de enviarlos al modelo
        if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['nombre_usuario']) || 
            empty($datos['contrasena']) || empty($datos['tipo_documento']) || empty($datos['documento_identidad']) || 
            empty($datos['email']) || empty($datos['telefono'])) {
            return false; // Datos incompletos
        }

        // Registrar el usuario utilizando el modelo
        $resultado = $this->usuarioModel->registrarUsuario($datos);

        // Devuelve el resultado del registro
        return $resultado;
    }
}
?>
