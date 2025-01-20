async function onSubmit(event) {
  event.preventDefault();
  $('#card-error').hide();
  const url = base_url(['validation']);
  const data = {
    email_username: $('#email-username').val(),
    password: $('#password').val(),
    captcha: $('#captcha').val()
  }
  if(data.email_username == '' || data.password == '' || data.captcha == ''){
    $('#card-error h5').html('Campos obligatorios.');
    $('#card-error p').html('Por favor revisa que los campos no esten vacios.');
    return $('#card-error').show();
  }
  await proceso_fetch(url, data).then(respond => {
    alert('Credenciales confirmadas', msg = 'Será redireccinado al Home');
    window.location.href = respond.url;
  });
}

async function getAddress() {
  if (!navigator.geolocation) {
      console.log("La geolocalización no está soportada en este navegador.");
      return;
  }

  try {
      // Esperar a obtener la ubicación del usuario
      const position = await new Promise((resolve, reject) => {
          navigator.geolocation.getCurrentPosition(resolve, reject);
      });

      const lat = position.coords.latitude;  // Latitud
      const lng = position.coords.longitude; // Longitud
      console.log(`Latitud: ${lat}, Longitud: ${lng}`);

      // API Key de Google Maps
      const apiKey = 'AIzaSyDZ878czxaqiqJVLU-C2lJalQxNYs8PUgM';

      // URL para la API de Geocodificación de Google Maps
      const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`;

      // Realizar la petición a la API
      const response = await fetch(url);
      const data = await response.json();

      if (data.results && data.results.length > 0) {
          const address = data.results[1].formatted_address;
          console.log(`Dirección: ${address}`);
      } else {
          console.log("No se encontró ninguna dirección.");
      }
  } catch (error) {
      console.error("Error al obtener la dirección:", error.message);
  }
}




let map;
let marker;

async function initMap() {

  const position = await new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(resolve, reject);
  });

  const lat = position.coords.latitude;  // Latitud
  const lng = position.coords.longitude; // Longitud
  console.log(`Latitud: ${lat}, Longitud: ${lng}`);
  // Inicializar el mapa en una ubicación predeterminada
  const initialLocation = { lat, lng }; // Bogotá, Colombia
  map = new google.maps.Map(document.getElementById('map'), {
    center: initialLocation,
    zoom: 12,
  });

  // Añadir un marcador al mapa
  marker = new google.maps.Marker({
    position: initialLocation,
    map: map,
    draggable: true, // Permitir arrastrar el marcador
  });

  // Escuchar el evento de arrastre del marcador
  marker.addListener('dragend', (event) => {
    const lat = event.latLng.lat();
    const lng = event.latLng.lng();

    // Mostrar las coordenadas en la página
    document.getElementById('coordinates').innerText =
      `Latitud: ${lat}, Longitud: ${lng}`;
  });

  // Permitir que el usuario coloque el marcador al hacer clic en el mapa
  map.addListener('click', (event) => {
    const clickedLocation = event.latLng;

    // Mover el marcador al punto seleccionado
    marker.setPosition(clickedLocation);

    // Mostrar las coordenadas
    document.getElementById('coordinates').innerText =
      `Latitud: ${clickedLocation.lat()}, Longitud: ${clickedLocation.lng()}`;
  });
}