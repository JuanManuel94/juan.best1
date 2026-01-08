document.write(
  `<script src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=${key_google}&callback=initMap"></script>`
);

function initMap() {
  const item = {
    latitud: $("#latitud").val(),
    longitud: $("#longitud").val(),
    nombre: $("#nombre").val(),
    puertos: $("#puertos").val(),
    detalles: $("#detalles").val(),
  };
  var myLatlng = new google.maps.LatLng(0.0, -0.0);
  var infowindow = null;
  var mapOptions = {
    center: myLatlng,
    zoom: 15,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
  };
  var map = new google.maps.Map(
    document.getElementById("nap_location"),
    mapOptions
  );
  var icon = `${base_url}/Assets/images/default/caja-nap.png`;
  const marker = new google.maps.Marker({
    position: new google.maps.LatLng(item.latitud, item.longitud),
    map: map,
    icon: icon,
  });
  // centrar marcador
  map.setCenter(marker.position);
  // mostrar información del marcador
  google.maps.event.addListener(marker, "click", function () {
    if (infowindow) infowindow.close();
    infowindow = new google.maps.InfoWindow({
      content: renderInfo(item),
    });
    infowindow.open(map, marker);
  });
}

function renderInfo(item) {
  return `
    <div style="width: 250px">
      <div>
        <i class="fas fa-info-circle"></i> <b>${item.nombre}</b>
      </div>
      <div>
        <i class="fas fa-location-arrow"></i> ${item.ubicacion}
      </div>
      <div>
        <i class="fas fa-project-diagram"></i> ${item.puertos} Puertos
      </div>
      <div>
        <i class="fas fa-asterisk"></i> ${item.detalles}
      </div>
    </div>`;
}
