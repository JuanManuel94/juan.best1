document.write(
  `<script src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=${key_google}&callback=initMap"></script>`
);

function initMap() {
  var myLatlng = new google.maps.LatLng(0.0, -0.0);
  var infowindow = null;
  var mapOptions = {
    center: myLatlng,
    zoom: 15,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
  };
  var map = new google.maps.Map(
    document.getElementById("customer_map"),
    mapOptions
  );

  let icon = `${base_url}/Assets/images/default`;

  axios
    .get(`${base_url}/cajaNap/list_records`)
    .then(({ data }) => {
      data?.forEach((item) => {
        const marker = new google.maps.Marker({
          position: new google.maps.LatLng(item.latitud, item.longitud),
          map: map,
          icon: `${icon}/${item.tipo == "mufa" ? "mufa" : "caja-nap"}.png`,
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
      });
    })
    .catch((err) => alert_msg("error", "No se pudo obtener los registros"));
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
