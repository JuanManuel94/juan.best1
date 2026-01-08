let table;
let table_name = "list";
document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name, "Lista de instalaciones");
    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url: " " + base_url + "/installations/list_records/0",
          dataSrc: "",
        },
        deferRender: true,
        idDataTables: "1",
        columns: [
          { data: "n", className: "text-center" },
          {
            data: "client",
            render: function (data, type, full, meta) {
              var client;
              if (full.profile_user == 1) {
                client =
                  '<a href="' +
                  base_url +
                  "/customers/view_client/" +
                  full.encrypt_contract +
                  '">' +
                  data +
                  "</a>";
              } else {
                client = data;
              }
              return client;
            },
          },
          { data: "document", className: "text-center" },
          { data: "cellphones" },
          {
            data: "attention_date",
            render: function (data, type, full, meta) {
              return moment(data).format("DD/MM/YYYY H:mm");
            },
          },
          {
            data: "opening_date",
            render: function (data, type, full, meta) {
              var opening_date = "";
              if (data == "0000-00-00 00:00:00") {
                opening_date = "00/00/0000";
              } else {
                opening_date = moment(data).format("DD/MM/YYYY H:mm");
              }
              return opening_date;
            },
          },
          {
            data: "closing_date",
            render: function (data, type, full, meta) {
              var closing_date = "";
              if (data == "0000-00-00 00:00:00") {
                closing_date = "00/00/0000";
              } else {
                closing_date = moment(data).format("DD/MM/YYYY H:mm");
              }
              return closing_date;
            },
          },
          { data: "duration", className: "text-center" },
          { data: "total", className: "text-center" },
          { data: "assigned" },
          { data: "user" },
          { data: "address" },
          { data: "reference" },
          {
            data: "state",
            render: function (data, type, full, meta) {
              var state = "";
              if (data == 1) {
                state = '<span class="label label-success">INSTALADO</span>';
              }
              if (data == 2) {
                state = '<span class="label label-warning">PENDIENTE</span>';
              }
              if (data == 3) {
                state = '<span class="label label-primary">EN PROCESO</span>';
              }
              if (data == 4) {
                state =
                  '<span class="label label-secondary">NO INSTALADO</span>';
              }
              if (data == 5) {
                state = '<span class="label label-dark">CANCELADO</span>';
              }
              return state;
            },
            className: "text-center",
          },
          { data: "options", className: "text-center", sWidth: "40px" },
        ],
        initComplete: function (oSettings, json) {
          $("#" + table_name + "_wrapper div.container-options").append(
            $("#" + table_name + "-btns-tools").contents()
          );
        },
      })
      .on("processing.dt", function (e, settings, processing) {
        if (processing) {
          loaderin(".panel-installations");
        } else {
          loaderout(".panel-installations");
        }
      });
    if (document.querySelector("#transactions")) {
      var transactions = document.querySelector("#transactions");
      transactions.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/installations/action";
          var formData = new FormData(transactions);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $("#modal-action").modal("hide");
                transactions.reset();
                alert_msg("success", objData.msg);
                refresh_table();
              } else if (objData.status == "exists") {
                $("#modal-action").modal("hide");
                transactions.reset();
                alert_msg("warning", objData.msg);
                refresh_table();
              } else {
                alert_msg("error", objData.msg);
              }
            }
            loading.style.display = "none";
            return false;
          };
        }
      };
    }
  },
  false
);
window.addEventListener(
  "load",
  function () {
    list_technical();
    list_clients();
    $("#transactions").parsley();
    $("#insDate").datetimepicker({ locale: "es" });
    $("#insDate").val(moment().format("DD/MM/YYYY H:mm"));
    $("#filter_states").select2({ minimumResultsForSearch: -1 });
  },
  false
);
function filter_states() {
  table.ajax
    .url(base_url + "/installations/list_records/" + $("#filter_states").val())
    .load();
  //table.columns.adjust().responsive.recalc();
}
function tools(idfacility) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/installations/select_record/" + idfacility;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        if (objData.data.state == 5) {
          alert_msg(
            "info",
            "No se puedo arregar materiales a esta instalación por que ya fue cancelada."
          );
          refresh_table();
        } else {
          $(location).attr(
            "href",
            base_url + "/installations/tools/" + idfacility
          );
        }
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function attend(idfacility) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "success",
    icon: "far fa-question-circle",
    title: "ADVERTENCIA",
    content: "¿Desea ingresar a la instalación?",
    buttons: {
      cancel: {
        text: "Si,Entrar",
        btnClass: "btn-success",
        action: function () {
          this.buttons.cancel.setText(
            '<i class="fas fa-spinner fa-spin icodialog"></i> Procesando...'
          );
          this.buttons.cancel.disable();
          $(".jconfirm-closeIcon").remove();
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/installations/select_record/" + idfacility;
          request.open("GET", ajaxUrl, true);
          request.send();
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              alsup.close();
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                if (objData.data.state == 1) {
                  $('[data-toggle="tooltip"]').tooltip("hide");
                  alert_msg("info", "La instalación ya esta completada.");
                  refresh_table();
                } else if (objData.data.state == 5) {
                  $('[data-toggle="tooltip"]').tooltip("hide");
                  alert_msg("error", "La instalación esta cancelada.");
                  refresh_table();
                } else if (
                  objData.data.state == 2 ||
                  objData.data.state == 3 ||
                  objData.data.state == 4
                ) {
                  $(location).attr(
                    "href",
                    base_url + "/installations/attend/" + idfacility
                  );
                } else {
                  $('[data-toggle="tooltip"]').tooltip("hide");
                  alert_msg("error", "Instalacion no encontrada.");
                }
              } else {
                $('[data-toggle="tooltip"]').tooltip("hide");
                alert_msg("error", objData.msg);
              }
            }
            return false;
          };
        },
      },
      close: {
        text: "Cancelar",
      },
    },
  });
}
function view(idfacility) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/installations/view_installation/" + idfacility;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#text-view").innerHTML =
          "Instalación Nº " + objData.data.facility.code;
        document.querySelector("#view-client").innerHTML =
          objData.data.facility.client;
        document.querySelector("#view-celdoc").innerHTML =
          "<b>" +
          objData.data.facility.type_doc +
          ":</b> " +
          objData.data.facility.document +
          " <b>CEL:</b> " +
          objData.data.facility.mobile;
        document.querySelector("#view-address").innerHTML =
          objData.data.facility.address;
        document.querySelector("#view-image-client").src = generateAvatar(
          objData.data.facility.client
        );
        document.querySelector("#view-created").innerHTML =
          "Creado " +
          moment(objData.data.facility.registration_date).format(
            "DD/MM/YYYY H:mm"
          );
        document.querySelector("#view-visit").innerHTML =
          "Programado " +
          moment(objData.data.facility.attention_date).format(
            "DD/MM/YYYY H:mm"
          );
        if (objData.data.facility.state == 1) {
          document.querySelector("#view-state").innerHTML = "INSTALADO";
        } else if (objData.data.facility.state == 2) {
          document.querySelector("#view-state").innerHTML = "PENDIENTE";
        } else if (objData.data.facility.state == 3) {
          document.querySelector("#view-state").innerHTML = "EN PROCESO";
        } else if (objData.data.facility.state == 4) {
          document.querySelector("#view-state").innerHTML = "NO INSTALADO";
        } else if (objData.data.facility.state == 5) {
          document.querySelector("#view-state").innerHTML = "CANCELADO";
        }
        document.querySelector("#view-description").innerHTML =
          objData.data.facility.detail;
        var data_service = "";
        if (objData.data.services.length === 0) {
          data_service += `NO HAY SERVICIOS`;
        } else {
          data_service += `INSTALACIÓN DE `;
          objData.data.services.forEach((service) => {
            data_service += `${service.service}, `;
          });
        }
        document.querySelector("#view-services").innerHTML = data_service;
        if (objData.data.facility.user_image == "user_default.png") {
          user_image = base_url + "/Assets/images/default/user_default.png";
        } else {
          user_image =
            base_url +
            "/Assets/uploads/users/" +
            objData.data.facility.user_image;
        }
        document.querySelector("#view-user-post").src = user_image;
        document.querySelector("#view-user").innerHTML =
          "<b>" + objData.data.facility.user + "</b>";
        var comments = 0;
        var data_detail = "";
        if (objData.data.detail.length === 0) {
          data_detail += `<li class="comment">NO HAY RESPUESTAS</li>`;
        } else {
          objData.data.detail.forEach((detail) => {
            comments++;
            if (detail.image == "user_default.png") {
              image_url = base_url + "/Assets/images/default/user_default.png";
            } else {
              image_url = base_url + "/Assets/uploads/users/" + detail.image;
            }
            if (detail.state == 1) {
              state = '<i class="fas fa-check-circle text-success"></i>';
            } else {
              state = '<i class="fas fa-times-circle text-danger"></i>';
            }
            data_detail += `
                   <li class="comment">
                     <a class="pull-left" href="#">
                       <img class="avatar" src="${image_url}">
                     </a>
                     <div class="comment-body">
                       <div class="comment-heading">
                         <h4 class="user">${detail.names} ${state}</h4><br>
                         <h5 class="time"><b>INICIO:</b> ${moment(
                           detail.opening_date
                         ).format(
                           "DD/MM/YYYY H:mm"
                         )}</h5> | <h5 class="time"><b>FIN:</b> ${moment(
              detail.closing_date
            ).format("DD/MM/YYYY H:mm")}</h5>
                       </div>
                       <p>${detail.comment}</p>
                     </div>
                   </li>`;
          });
        }
        document.querySelector("#view-comments").innerHTML = comments;
        document.querySelector("#post-comment").innerHTML = data_detail;
        var htmlImage = "";
        var images = 0;
        if (objData.data.images.length === 0) {
          htmlImage += `SIN IMAGENES`;
        } else {
          objData.data.images.forEach((image) => {
            images++;
            htmlImage += `<div class="image">
                    <div class="image-inner">
                      <a href="${
                        image.url_image
                      }" data-lightbox="gallery-group-1">
                        <div class="img" style="background-image: url(${
                          image.url_image
                        })"></div>
                      </a>
                    </div>
                    <div class="image-info p-2">
                        <div class="pull-right"><strong>${moment(
                          image.registration_date
                        ).format("DD/MM/YYYY")}</strong> ${moment(
              image.registration_date
            ).format("h:mm")} <strong>${moment(image.registration_date).format(
              "a"
            )}</strong></div>
                        <div class="rating"><small class="mr-1">por</small><strong>${
                          image.names
                        }</strong></div>
                     </div>
                  </div>`;
          });
        }
        document.querySelector("#containerImages").innerHTML = htmlImage;
        document.querySelector("#view-images").innerHTML = images;
        $("#modal-view").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function send_email(idfacility) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  loading.style.display = "flex";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/installations/send_email/" + idfacility;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        alert_msg("success", objData.msg);
      } else if (objData.status == "not_exist") {
        alert_msg("info", objData.msg);
      } else {
        alert_msg("error", objData.msg);
      }
    }
    loading.style.display = "none";
    return false;
  };
}
function installation_sheet(idfacility) {
  alert_msg("loader", "Generando pdf.");
  $('[data-toggle="tooltip"]').tooltip("hide");
  setTimeout(function () {
    $("#gritter-notice-wrapper").remove();
    alert_msg("success", "El pdf se ha generado correctamente.");
    window.open(base_url + "/installations/view_pdf/" + idfacility, "_blank");
  }, 1000);
}
function list_technical() {
  if (document.querySelector("#listTechnical")) {
    var ajaxUrl = base_url + "/installations/list_technical";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listTechnical").innerHTML =
          request.responseText;
        $("#listTechnical").select2();
      }
    };
  }
}
function list_clients() {
  if (document.querySelector("#listClients")) {
    var ajaxUrl = base_url + "/installations/list_clients";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listClients").innerHTML = request.responseText;
        $("#listClients").select2();
      }
    };
  }
}
function update(idfacility) {
  $("#transactions").parsley().reset();
  document.querySelector("#text-title").innerHTML = "Actualizar Instalación";
  document.querySelector("#text-button").innerHTML = "Guardar Cambios";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/installations/select_record/" + idfacility;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#idfacility").value = objData.data.encrypt_id;
        document.querySelector("#insDate").value = moment(
          objData.data.attention_date
        ).format("DD/MM/YYYY H:mm");
        document.querySelector("#instPrice").value = objData.data.cost;
        document.querySelector("#detail").value = objData.data.detail;
        document.querySelector("#listTechnical").value =
          objData.data.encrypt_technical;
        $("#listTechnical").select2();
        document.querySelector("#listClients").value =
          objData.data.encrypt_client;
        $("#listClients").select2();
        $("#modal-action").modal("show");
        $('[data-toggle="tooltip"]').tooltip("hide");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function cancel(idfacility) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "CANCELAR",
    content: "Esta seguro que desea cancelar esta instalación.",
    buttons: {
      cancel: {
        text: "Aceptar",
        btnClass: "btn-info",
        action: function () {
          this.buttons.cancel.setText(
            '<i class="fas fa-spinner fa-spin icodialog"></i> Procesando...'
          );
          this.buttons.cancel.disable();
          $(".jconfirm-closeIcon").remove();
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/installations/cancel";
          var strData = "idfacility=" + idfacility;
          request.open("POST", ajaxUrl, true);
          request.setRequestHeader(
            "Content-type",
            "application/x-www-form-urlencoded"
          );
          request.send(strData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              alsup.close();
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $('[data-toggle="tooltip"]').tooltip("hide");
                alert_msg("success", objData.msg);
                refresh_table();
              } else {
                $('[data-toggle="tooltip"]').tooltip("hide");
                alert_msg("error", objData.msg);
              }
            }
            return false;
          };
        },
      },
      close: {
        text: "Cancelar",
      },
    },
  });
}
function modal_tools(country, number, client) {
  $("#modal-tools").modal("show");
  document.querySelector("#text-title-tools").innerHTML =
    client + " - " + number;
  document.querySelector("#tools_country").value = country;
  document.querySelector("#tools_number").value = number;
}
$("#btn-sms").on("click", function () {
  var country = document.querySelector("#tools_country").value;
  var number = document.querySelector("#tools_number").value;
  let url = "sms:+" + country + number;
  $(location).attr("href", url);
});
$("#btn-tocall").on("click", function () {
  var country = document.querySelector("#tools_country").value;
  var number = document.querySelector("#tools_number").value;
  let url = "tel:+" + country + number;
  $(location).attr("href", url);
});
$("#btn-whatsapp").on("click", function () {
  const country = document.querySelector("#tools_country").value;
  const phone = document.querySelector("#tools_number").value;
  const numberPhone = `${country}${phone}`;
  const message = prompt("Mensaje");
  // validar mensaje
  if (!message) return;
  // validar numero
  if (!phone) return alert_msg("info", "El número es obligatorio.");
  const wspApi = getWhatsappApi();
  if (wspApi) {
    sendMessageWhatsapp({ phone: numberPhone, message })
      .then(() => alert_msg("success", "Mensaje enviado"))
      .catch(() => alert_msg("error", "No se pudo enviar el mensaje"));
  } else {
    const url = `https://api.whatsapp.com/send/?phone=${numberPhone}&text=${message}`;
    window.open(url);
  }
});
function modal() {
  document.querySelector("#text-title").innerHTML = "Nueva Instalación";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#idfacility").value = "";
  document.querySelector("#transactions").reset();
  $("#transactions").parsley().reset();
  $("#insDate").val(moment().format("DD/MM/YYYY H:mm"));
  $("#modal-action").modal("show");
  list_clients();
  list_technical();
}
