document.write(
  `<script src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=${key_google}&callback=initMap"></script>`
);
var map;
var lat_client = $("#lat_client").val();
var lng_client = $("#lng_client").val();
var name_client = $("#name_client").val();
var address = $("#address_client").val();
var optionsroute = {
  enableHighAccuracy: true,
  timeout: 15000,
  maximumAge: 0,
};
function bottonRoute(map) {
  const button_route = document.createElement("button");
  button_route.style.backgroundColor = "rgb(255, 255, 255)";
  button_route.style.border = "1px solid rgb(218,220,224)";
  button_route.style.borderRadius = "2px";
  button_route.style.boxShadow = "rgba(0, 0, 0, 0.3) 0px 1px 4px -1px";
  button_route.style.color = "#6c757d";
  button_route.style.cursor = "pointer";
  button_route.style.fontFamily = "Roboto,Arial,sans-serif";
  button_route.style.fontSize = "20px";
  button_route.style.margin = "0 10px 0 0";
  button_route.style.width = "40px";
  button_route.style.height = "40px";
  button_route.style.textAlign = "center";
  button_route.innerHTML = '<i class="fa fa-map-signs"></i>';
  button_route.title = "Trazar ruta";
  button_route.type = "button";
  button_route.addEventListener("click", () => {
    trace_route();
  });
  return button_route;
}
/* TRAZAR RUTA V1 */
function initMap() {
  map = new google.maps.Map(document.getElementById("customer_location"), {
    zoom: 16,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
    },
  });

  const centerControlDiv = document.createElement("div");
  const centerControl = bottonRoute(map);
  centerControlDiv.appendChild(centerControl);
  map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(centerControlDiv);

  latLng = new google.maps.LatLng(
    parseFloat(lat_client),
    parseFloat(lng_client)
  );
  marker = new google.maps.Marker({
    position: latLng,
    map: map,
    draggable: false,
  });

  map.setCenter(latLng);

  var infowindow = new google.maps.InfoWindow({
    content:
      "<h5 class='text-center f-w-600 mb-0'>" + name_client + "</h5>" + address,
  });
  infowindow.open(map, marker);
}
function trace_route() {
  if (navigator.geolocation) {
    var options_coord = {
      enableHighAccuracy: true,
      timeout: 15000,
      maximumAge: 0,
    };
    var directionsRenderer = new google.maps.DirectionsRenderer();
    var directionsService = new google.maps.DirectionsService();
    directionsRenderer.setMap(map);
    navigator.geolocation.getCurrentPosition(
      function (position, location_error, options_coord) {
        var start = new google.maps.LatLng(
          position.coords.latitude,
          position.coords.longitude
        );
        var end = new google.maps.LatLng(lat_client, lng_client);

        directionsService.route(
          {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING,
          },
          function (response, status) {
            if (status == "OK") {
              directionsRenderer.setDirections(response);
              if (response.routes[0].legs[0].steps.length) {
                var step = response.routes[0].legs[0].steps.length - 1;
              } else {
                var step = 0;
              }
              var infowindow = new google.maps.InfoWindow();
              infowindow.setContent(
                "<h5 class='text-center f-w-600 mb-0'>Mi ubicación actual</h5>"
              );
              infowindow.setPosition(response.routes[0].legs[0].start_location);
              infowindow.open(map);

              var infowindow2 = new google.maps.InfoWindow();
              infowindow2.setContent(
                "<h5 class='text-center f-w-600 mb-0'>" +
                  name_client +
                  "</h5>" +
                  address
              );
              infowindow2.setPosition(
                response.routes[0].legs[0].steps[step].end_location
              );
              infowindow2.open(map);
            } else {
              alert_msg("error", "ERROR GOOGLE: <br>" + status);
            }
          }
        );
      },
      function () {
        var start = new google.maps.LatLng(
          -8.381723950980284,
          -74.54314678745268
        );
        var end = new google.maps.LatLng(lat_client, lng_client);

        directionsService.route(
          {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING,
          },
          function (response, status) {
            if (status == "OK") {
              directionsRenderer.setDirections(response);
              if (response.routes[0].legs[0].steps.length) {
                var step = response.routes[0].legs[0].steps.length - 1;
              } else {
                var step = 0;
              }
              var infowindow = new google.maps.InfoWindow();
              infowindow.setContent(
                "<h5 class='text-center f-w-600 mb-0'>Mi ubicación actual</h5>"
              );
              infowindow.setPosition(response.routes[0].legs[0].start_location);
              infowindow.open(map);

              var infowindow2 = new google.maps.InfoWindow();
              infowindow2.setContent(
                "<h5 class='text-center f-w-600 mb-0'>" +
                  name_client +
                  "</h5><b>Distancia:</b> " +
                  response.routes[0].legs[0].distance.text +
                  "<br>" +
                  address
              );
              infowindow2.setPosition(
                response.routes[0].legs[0].steps[step].end_location
              );
              infowindow2.open(map);
            } else {
              alert_msg("error", "ERROR GOOGLE: <br>" + status);
            }
          }
        );
      }
    );
  }
}
