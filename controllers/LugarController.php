<?php
include_once __DIR__ . '/../models/Lugar.php';

class LugarController {
    private $lugar;

    public function __construct() {
        $this->lugar = new Lugar();
    }

    public function getAllLugares() {
        return $this->lugar->getAllLugares();
    }

    public function getLugarById($idlugar) {
        return $this->lugar->getLugarById($idlugar);
    }

    public function createLugar($data) {
        return $this->lugar->createLugar(
            $data['nombre_lugar'], 
            $data['ubicacion_lugar'], 
            $data['descripcion_lugar'], 
            $data['cantidad_habitaciones'], 
            $data['precio_lugar'], 
            $data['imagen_lugar'], 
            $data['usuario_idusuario'], 
            $data['rol_idrol'],
            $data['tipo'],
            $data['disponibilidad_lugar'],
            $data['cantidad_banos'],
            $data['cantidad_piscinas'],
            $data['juegos_infantiles'],
            $data['zonas_verdes']
        );
    }
}
?>
