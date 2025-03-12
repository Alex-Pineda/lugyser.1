<?php
include_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    public function login($nombre, $password) {
        return $this->usuario->login($nombre, $password);
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
