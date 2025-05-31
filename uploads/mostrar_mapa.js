function mostrarMapa() {
  document.getElementById('map').style.display = 'block';

  const latLugar = 6.2518;
  const lngLugar = -75.5636;
  const apiKey = '5b3ce3597851110001cf6248c3e262bfb6f248139868885347b00016';

  const map = L.map('map').setView([latLugar, lngLugar], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const marcadorLugar = L.marker([latLugar, lngLugar]).addTo(map).bindPopup('Ubicación del lugar').openPopup();

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;

function mostrarRutaUsuario(lat, lon) {
  // Crear marcador del usuario
  const marcadorUsuario = L.marker([lat, lon]).addTo(map).bindPopup('Tu ubicación');
  
  // Coordenadas para la ruta
  const coordenadasRuta = [
    [lon, lat], // usuario
    [lngLugar, latLugar] // destino
  ];
  console.log("Ubicación usuario:", lat, lon);
  console.log("Lugar destino:", lugar.lat, lugar.lon);
  console.log("Coordenadas para la API:", coordenadasRuta);

  fetch('https://api.openrouteservice.org/v2/directions/driving-car/geojson', {
    method: 'POST',
    headers: {
      'Authorization': apiKey,
      'Accept': 'application/json, application/geo+json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ coordinates: coordenadasRuta })
  })
    .then(response => response.json())
    .then(data => {
      console.log("Respuesta de ruta:", data);

      if (data.features && data.features.length > 0) {
        const geometry = data.features[0].geometry;
        const coords = geometry.coordinates.map(coord => [coord[1], coord[0]]); // Invertir para Leaflet

        const ruta = L.polyline(coords, { color: 'blue' }).addTo(map);
        
        // Ajustar el zoom del mapa para que se vea la ruta
        const grupo = new L.featureGroup([marcadorUsuario, ruta]);
        map.fitBounds(grupo.getBounds().pad(0.5));
      } else {
        console.error("No se encontraron rutas:", data);
        
      }
    })
    .catch(error => {
      console.error("Error en la solicitud de ruta:", error);
     
    });
  }
  mostrarRutaUsuario(userLat, userLng);
});
  } else {
    alert("La geolocalización no está disponible en este navegador.");
  }
}
