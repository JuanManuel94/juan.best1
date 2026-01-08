document.write(
  `<script src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=${key_google}"></script>`
);

const btnAction = (title, className, icon, action) => {
  return `
    <a href="javascript:;" 
      class="ml-2 ${className}"
      data-toggle="tooltip"
      data-original-title="${title}"
      onclick="${action};"
    >
      <i class="${icon}"></i>
    </a>`;
};

const componentLoading = (status = false) => {
  if (status) loading.style.display = "flex";
  else loading.style.display = "none";
};

const tableList = () => {
  const id = `list`;

  const getColumns = () => {
    const arrayColumns = [
      { data: "id", className: "text-center" },
      { data: "nombre" },
      { data: "tipo" },
      { data: "zona" },
      {
        data: "color_tubo",
        render: (data) => {
          return `<div style="height: 10px; width: 100%; background: ${data}"></div>`;
        },
      },
      {
        data: "color_hilo",
        render: (data) => {
          return `<div style="height: 10px; width: 100%; background: ${data}"></div>`;
        },
      },
      {
        data: "ubicacion",
      },
      {
        data: "id",
        sWidth: "200px",
        render(data, type, full) {
          const a = document.createElement("a");
          a.href = `${base_url}/cajaNap/view_location/${data}`;
          a.target = "_blank";
          const icon = document.createElement("i");
          icon.className = "fa fa-map-marker-alt mr-1 text-danger";
          a.innerHTML = `${icon.outerHTML} ${full.latitud}, ${full.longitud} `;
          return a.outerHTML;
        },
      },
      {
        data: "puertos",
        sWidth: "300px",
        render(data, type, full) {
          const content = document.createElement("div");
          content.className = "text-center";
          full?.array_puertos?.forEach((item) => {
            const div = document.createElement("div");
            const btnClass = `napport ${item.state ? "" : "red"}`;
            const btnAlert = item.state
              ? ""
              : `onclick="showInfoClient('${item.cliente}', '${item.puerto}')"`;
            div.innerHTML = `<div ${btnAlert} class="${btnClass}">${item.puerto}</div>`;
            content.appendChild(div);
          });
          return content.outerHTML;
        },
      },

      { data: "detalles" },
    ];
    // agregar opciones
    arrayColumns.push({
      data: "id",
      className: "text-center",
      sWidth: "40px",
      render: (data, type, full) => {
        let optDelete = "";
        let optEdit = "";
        if (full.isRemove) {
          optDelete = btnAction(
            "Eliminar",
            "red",
            "far fa-trash-alt",
            `openRemove('${data}')`
          );
        }
        if (full.isEdit) {
          optEdit = btnAction(
            "Editar",
            "blue",
            "far fa-edit",
            `openUpdate('${data}')`
          );
        }
        return `<div>${optEdit}${optDelete}</div>`;
      },
    });
    // response
    return arrayColumns;
  };

  return datatableHelper(id, "Lista de AP Emisor", {
    ajax: {
      url: ` ${base_url}/cajaNap/list_records`,
      dataSrc: "",
      data: function (data) {
        const zonaId = $("#filter-zonaId").val();
        const tipo = $("#filter-tipo").val();

        if (zonaId) {
          data.zonaId = zonaId;
        }

        if (tipo) {
          data.tipo = tipo;
        }
      },
    },
    deferRender: true,
    idDataTables: "1",
    columns: getColumns(),
  });
};

const openCreate = () => {
  document.querySelector("#text-title").innerHTML = "Nueva Mufa o Caja Nap";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#transactions").reset();
  $("#puertos").prop("disabled", false);
  $("#tipo").attr("disabled", false).trigger("change");
  $("#transactions").parsley().reset();
  $("#modal-action").modal("show");
  const component = document.getElementById("transactions");
  component.onsubmit = (e) => {
    e.preventDefault();
    create();
  };
};

const create = () => {
  const transactions = document.querySelector("#transactions");
  const isValid = $("#transactions").parsley().isValid();
  if (!isValid) return;
  loading.style.display = "flex";
  const formData = new FormData(transactions);
  fetch(`${base_url}/cajaNap/save`, {
    method: "post",
    body: formData,
  })
    .then((dataJson) => dataJson.json())
    .then((data) => {
      if (!data.status) throw new Error(data.message);
      $("#modal-action").modal("hide");
      transactions.reset();
      alert_msg("success", data.message);
      tableList().refresh();
    })
    .catch((err) => alert_msg("error", err.message))
    .finally(() => (loading.style.display = "none"));
};

const openUpdate = (id) => {
  document.querySelector("#text-title").innerHTML = "Editar Mufa y Caja Nap";
  document.querySelector("#text-button").innerHTML = "Actualizar Registro";
  const component = document.getElementById("transactions");
  component.reset();
  axios
    .get(`${base_url}/cajaNap/select_record/${id}`)
    .then(({ data }) => {
      $("#transactions").parsley().reset();
      $("#modal-action").modal("show");
      $("#objectId").val(data.id);
      $("#nombre").val(data.nombre);
      $("#tipo").val(data.tipo).attr("disabled", true).trigger("change");
      $("#latitud").val(data.latitud);
      $("#longitud").val(data.longitud);
      $("#coordenadas").val(`${data.latitud}, ${data.longitud}`);
      $("#ubicacion").val(data.ubicacion);
      $("#puertos").val(data.puertos);
      $("#puertos").prop("disabled", data.state ? true : false);
      $("#detalles").val(data.detalles);
      $("#color_tubo").val(data.color_tubo);
      $("#color_hilo").val(data.color_hilo);
      $("#zonaId").val(data.zonaId);
      component.onsubmit = (evt) => update(evt, data.id);
    })
    .catch(() => null);
};

const update = (evt, id) => {
  evt.preventDefault();
  loading.style.display = "flex";
  const form = document.querySelector("#transactions");
  const data = new FormData(form);
  data.set("tipo", $("#tipo").val());
  axios
    .post(`${base_url}/cajaNap/update_record/${id}`, data)
    .then(({ data }) => {
      if (data.status == "error") throw new Error(data.msg);
      alert_msg("success", data.msg);
      tableList().refresh();
    })
    .catch((err) => alert_msg("error", err.message))
    .finally(() => (loading.style.display = "none"));
};

const openRemove = (id) => {
  const alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ELIMINAR",
    content: "Esta seguro que desea eliminar este registro.",
    buttons: {
      remove: {
        text: "Aceptar",
        btnClass: "btn-info",
        action: function () {
          this.buttons.remove.setText(
            '<i class="fas fa-spinner fa-spin icodialog"></i> Procesando...'
          );
          this.buttons.remove.disable();
          componentLoading(true);
          remove(id)
            .then((data) => {
              alsup.close();
              tableList().refresh();
              $('[data-toggle="tooltip"]').tooltip("hide");
              alert_msg("success", data.message);
            })
            .catch((err) => {
              $('[data-toggle="tooltip"]').tooltip("hide");
              alert_msg("info", err.message);
            })
            .finally(() => {
              $(".jconfirm-closeIcon").remove();
              componentLoading();
            });
        },
      },
      close: {
        text: "Cancelar",
      },
    },
  });
};

const remove = (id) => {
  return new Promise((resolve, reject) => {
    fetch(`${base_url}/cajaNap/remove_record/${id}`, {
      method: "post",
    })
      .then((dataJSON) => dataJSON.json())
      .then((data) => {
        if (!data.status) throw new Error(data.message);
        resolve(data);
      })
      .catch(reject);
  });
};

function initMap(tipo = "nap") {
  const latitud = $("#latitud").val();
  const longitud = $("#longitud").val();
  latLng = new google.maps.LatLng(
    latitud || -8.381723950980284,
    longitud || -74.54314678745268
  );
  map = new google.maps.Map(document.getElementById("locations"), {
    zoom: 16,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
    },
  });
  var icon = `${base_url}/Assets/images/default/${
    tipo == "mufa" ? "mufa" : "caja-nap"
  }.png`;
  if ($("#latitud").val() == "" || $("#longitud").val() == "") {
    if (navigator.geolocation) {
      var options_coord = {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0,
      };

      navigator.geolocation.getCurrentPosition(
        function (position, location_error, options_coord) {
          latLng = new google.maps.LatLng(
            latitud || position.coords.latitude,
            longitud || position.coords.longitude
          );

          marker = new google.maps.Marker({
            position: latLng,
            map: map,
            size: new google.maps.Size(20, 32),
            icon: icon,
            draggable: true,
            anchor: new google.maps.Point(0, 32),
            origin: new google.maps.Point(0, 0),
          });
          updateMarkerPosition(latLng);
          map.setCenter(latLng);
          var infowindow = new google.maps.InfoWindow({
            content:
              "<h5 class='text-center f-w-600 mb-0'>Ubicación Caja Nap</h5>",
          });
          infowindow.open(map, marker);

          google.maps.event.addListener(marker, "dragend", function () {
            updateMarkerPosition(marker.getPosition());
          });
        },
        function () {
          latLng = new google.maps.LatLng(
            -8.381723950980284,
            -74.54314678745268
          );

          marker = new google.maps.Marker({
            position: latLng,
            map: map,
            icon: icon,
            draggable: true,
          });
          updateMarkerPosition(latLng);
          map.setCenter(latLng);
          var infowindow = new google.maps.InfoWindow({
            content:
              "<h5 class='text-center f-w-600 mb-0'>Ubicación del cliente</h5>",
          });
          infowindow.open(map, marker);

          google.maps.event.addListener(marker, "dragend", function () {
            updateMarkerPosition(marker.getPosition());
          });
        }
      );
    }
  } else {
    var latituds = $("#latitud").val();
    var longituds = $("#longitud").val();
    latLng = new google.maps.LatLng(
      parseFloat(latituds),
      parseFloat(longituds)
    );
    marker = new google.maps.Marker({
      position: latLng,
      map: map,
      icon: icon,
      draggable: true,
    });
    updateMarkerPosition(latLng);
    map.setCenter(latLng);

    var infowindow = new google.maps.InfoWindow({
      content: `<h5 class='text-center f-w-600 mb-0'>Ubicación de la ${
        tipo == "mufa" ? "Mufa" : "Caja Nap"
      }</h5>`,
    });
    infowindow.open(map, marker);

    google.maps.event.addListener(marker, "dragend", function () {
      updateMarkerPosition(marker.getPosition());
    });
  }
}

function open_map() {
  document.querySelector("#text-map").innerHTML = "Google Maps";
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#modal-map").modal("show");
  initMap($("#tipo").val());
}

function updateMarkerPosition(latLng) {
  $("#coordenadas").val(`${latLng.lat()}, ${latLng.lng()}`);
  $("#longitud").val(latLng.lng());
  $("#latitud").val(latLng.lat());
}

function showInfoClient(name, puerto) {
  Swal.fire({
    title: `Información del puerto: ${puerto}`,
    text: `Cliente: ${name}`,
  });
}

// load functions
document.addEventListener("DOMContentLoaded", tableList().render, false);
window.addEventListener("load", () => $("#transactions").parsley(), false);

$("#tipo").change(function () {
  const tipo = $(this).val();
  if (tipo == "nap") {
    $("#container-puertos").attr("style", null);
    $("#puertos").attr("min", 1).attr("data-parsley-required", true);
  } else {
    $("#container-puertos").attr("style", "display: none;");
    $("#puertos").attr("min", null).attr("data-parsley-required", null);
  }
});

$("#filter-tipo").change(function () {
  tableList().refresh();
});

$("#filter-zonaId").change(function () {
  tableList().refresh();
});
