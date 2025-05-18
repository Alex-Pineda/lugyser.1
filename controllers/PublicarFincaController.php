<?php
session_start();

$_SESSION['roles'] = [
    ['idrol' => 2, 'nombre_rol' => 'proveedor']
];

$rol_id = null;
foreach ($_SESSION['roles'] as $rol) {
    if ($rol['nombre_rol'] === 'proveedor') {
        $rol_id = $rol['idrol'];
        break;
    }
}

if ($rol_id === null) {
    echo "<script>alert('No tienes rol de proveedor asignado.'); window.location.href = '../index.php';</script>";
    exit;
}

include_once '../models/Lugar.php';

class PublicarFincaController {
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
        $requiredFields = [
            'nombre_lugar', 'ubicacion_lugar', 'descripcion_lugar', 
            'cantidad_habitaciones', 'precio_lugar', 'imagen_lugar', 
            'usuario_id', 'rol_id', 'tipo', 'disponibilidad_lugar', 
            'cantidad_banos', 'cantidad_piscinas', 'juegos_infantiles', 'zonas_verdes'
        ];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        return $this->lugar->createLugar(
            $data['nombre_lugar'], 
            $data['ubicacion_lugar'], 
            $data['descripcion_lugar'], 
            $data['cantidad_habitaciones'], 
            $data['precio_lugar'], 
            $data['imagen_lugar'], 
            $data['usuario_id'], 
            $data['rol_id'],
            $data['tipo'],
            $data['disponibilidad_lugar'],
            $data['cantidad_banos'],
            $data['cantidad_piscinas'],
            $data['juegos_infantiles'],
            $data['zonas_verdes']
        );
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'nombre_lugar' => $_POST['nombre_lugar'] ?? null,
        'ubicacion_lugar' => $_POST['ubicacion_lugar'] ?? null,
        'descripcion_lugar' => $_POST['descripcion_lugar'] ?? null,
        'cantidad_habitaciones' => $_POST['cantidad_habitaciones'] ?? null,
        'precio_lugar' => $_POST['precio_lugar'] ?? null,
        'imagen_lugar' => isset($_FILES['imagen_lugar']) ? file_get_contents($_FILES['imagen_lugar']['tmp_name']) : null,
        'usuario_id' => $_POST['usuario_id'] ?? null,
        'rol_id' => $rol_id,
        'tipo' => $_POST['tipo'] ?? null,
        'disponibilidad_lugar' => isset($_POST['disponibilidad_lugar']) ? 1 : 0,
        'cantidad_banos' => $_POST['cantidad_banos'] ?? null,
        'cantidad_piscinas' => $_POST['cantidad_piscinas'] ?? null,
        'juegos_infantiles' => isset($_POST['juegos_infantiles']) ? 1 : 0,
        'zonas_verdes' => isset($_POST['zonas_verdes']) ? 1 : 0
    ];

    $publicarFincaController = new PublicarFincaController();
    if ($publicarFincaController->createLugar($data)) {
        echo "Finca publicada exitosamente!";
    } else {
        echo "Error al publicar la finca. Por favor, complete todos los campos requeridos.";
    }
}
?>
