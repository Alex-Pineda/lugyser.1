<?php
session_start();

// Conexión a la BD
$conexion = new mysqli("localhost", "root", "", "lugyser");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener idLugar de la URL
$idLugar = isset($_GET['idlugar']) ? intval($_GET['idlugar']) : 0;

// Preparar consulta para obtener la dirección del lugar
$stmt = $conexion->prepare("SELECT ubicacion_lugar FROM lugar WHERE idlugar = ?");
$stmt->bind_param("i", $idLugar);
$stmt->execute();
$stmt->bind_result($ubicacion_lugar);
$stmt->fetch();
$stmt->close();
$conexion->close();

if (!$ubicacion_lugar) {
    die("No se encontró la ubicación para el lugar solicitado.");
}

// Geocodificación con Nominatim
function geocodeNominatim($direccion) {
    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($direccion) . "&format=json&limit=1";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'LugyserApp/1.0 (contacto@lugyser.com)');
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        curl_close($curl);
        return null;
    }
    curl_close($curl);

    return json_decode($response, true);
}

// Geocodificar dirección del lugar
$dataLugar = geocodeNominatim($ubicacion_lugar);
if (isset($dataLugar[0])) {
    $latLugar = $dataLugar[0]['lat'];
    $lonLugar = $dataLugar[0]['lon'];
} else {
    $latLugar = 19.432608; // CDMX por defecto
    $lonLugar = -99.133209;
    $ubicacion_lugar = "Ubicación no encontrada";
}

// Ubicación del usuario si ya está en la sesión
$usuarioTieneUbicacion = false;
$latUsuario = null;
$lonUsuario = null;

if (isset($_SESSION['ubicacion_usuario'])) {
    $latUsuario = $_SESSION['ubicacion_usuario']['lat'];
    $lonUsuario = $_SESSION['ubicacion_usuario']['lon'];
    $usuarioTieneUbicacion = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Mapa del Lugar</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="icon" href="/lugyser/favicon-rounded.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js"></script>

    <style>
        #map { height: 500px; width: 100%; }
    </style>
</head>
<body>
    <h3>Ubicación de: <?= htmlspecialchars($ubicacion_lugar) ?></h3>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const lugar = {
            lat: <?= json_encode(floatval($latLugar)) ?>,
            lon: <?= json_encode(floatval($lonLugar)) ?>,
            direccion: <?= json_encode($ubicacion_lugar) ?>
        };

        let usuario = <?= $usuarioTieneUbicacion
            ? json_encode(['lat' => floatval($latUsuario), 'lon' => floatval($lonUsuario)])
            : 'null' ?>;

        const map = L.map('map').setView([lugar.lat, lugar.lon], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const marcadorLugar = L.marker([lugar.lat, lugar.lon]).addTo(map)
            .bindPopup(`<b>Lugar:</b> ${lugar.direccion}`).openPopup();

        // Función para dibujar marcador del usuario y trazar línea
        function mostrarRutaUsuario(lat, lon) {
            const marcadorUsuario = L.marker([lat, lon]).addTo(map)
                .bindPopup('Tu ubicación');
            
            const apiKey = "5b3ce3597851110001cf6248c3e262bfb6f248139868885347b00016"; // ← Reemplaza esto con tu clave real de OpenRouteService

            const urlRuta = `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}`;

    fetch(urlRuta, {
        method: 'POST',
        headers: {
            'Accept': 'application/json, application/geo+json',
            'Content-Type': 'application/json'
        },
    body: JSON.stringify({
        coordinates: [
            [lon, lat], // origen: usuario
            [lugar.lon, lugar.lat] // destino: lugar
        ]
    })
})
.then(response => response.json())
.then(data => {
    const geometry = data.features[0].geometry;
    const coords = geometry.coordinates.map(coord => [coord[1], coord[0]]);
    const ruta = L.polyline(coords, {color: 'blue'}).addTo(map);

    const marcadorUsuario = L.marker([lat, lon]).addTo(map)
        .bindPopup('Tu ubicación');

    const grupo = new L.featureGroup([marcadorLugar, marcadorUsuario, ruta]);
    map.fitBounds(grupo.getBounds().pad(0.5));
})
.catch(error => {
    console.error("Error al trazar la ruta vial:", error);
});

            const grupo = new L.featureGroup([marcadorLugar, marcadorUsuario]);
            map.fitBounds(grupo.getBounds().pad(0.5));
        }

        // Si ya se tiene ubicación del usuario desde PHP
        if (usuario) {
            mostrarRutaUsuario(usuario.lat, usuario.lon);
        } else {
            // Si no, pedirla al navegador
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Guardar en sesión vía fetch POST
                    fetch("controllers/guardar_ubicacion.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `lat=${lat}&lon=${lon}`
                    }).then(res => res.json())
                      .then(data => {
                          if (data.success) {
                              console.log("Ubicación guardada en la sesión.");
                          } else {
                              console.warn("No se guardó la ubicación.");
                          }
                      });

                    mostrarRutaUsuario(lat, lon);
                }, error => {
                    alert("No se pudo obtener tu ubicación.");
                });
            } else {
                alert("Geolocalización no soportada por este navegador.");
            }
        }
    </script>
   
</body>
</html>


