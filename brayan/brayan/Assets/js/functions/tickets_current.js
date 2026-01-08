let table, table_bills;
let table_name = "list",
  table_name_bills = "list-bills";
document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name, "Tickets pendientes");
    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url:
            " " +
            base_url +
            "/tickets/list_current?ticket=" +
            $("#filter_tickets").val(),
          dataSrc: "",
        },
        deferRender: true,
        idDataTables: "1",
        columns: [
          { data: "id", className: "text-center" },
          {
            data: "client",
            render: function (data, type, full, meta) {
              return (
                '<a href="' +
                base_url +
                "/customers/view_client/" +
                full.encrypt_contract +
                '">' +
                data +
                "</a>"
              );
            },
          },
          { data: "document", className: "text-center" },
          { data: "cellphones" },
          {
            data: "coordinates",
            render: function (data, type, full, meta) {
              var coordinates = "";
              if (data == "") {
                coordinates = "";
              } else {
                coordinates =
                  '<a href="' +
                  base_url +
                  "/tickets/client_location/" +
                  full.encrypt_client +
                  '"><i class="fa fa-map-marker-alt mr-1 text-danger"></i>' +
                  data +
                  "</a>";
              }
              return coordinates;
            },
          },
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
            data: "priority",
            render: function (data, type, full, meta) {
              var priority = "";
              if (data == 1) {
                priority =
                  '<span style="color:#00acac;border: 1px solid #00acac;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">BAJA</span>';
              }
              if (data == 2) {
                priority =
                  '<span style="color:#4da1ff;border: 1px solid #4da1ff;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">MEDIA</span>';
              }
              if (data == 3) {
                priority =
                  '<span style="color:#F59C1A;border: 1px solid #F59C1A;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">ALTA</span>';
              }
              if (data == 4) {
                priority =
                  '<span style="color:#ff5959;border: 1px solid #ff5959;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">URGENTE</span>';
              }
              return priority;
            },
            className: "text-center",
          },
          { data: "incident" },
          { data: "assigned" },
          { data: "user" },
          { data: "address" },
          { data: "reference" },
          {
            data: "registration_date",
            render: function (data, type, full, meta) {
              return moment(data).format("DD/MM/YYYY");
            },
            className: "text-center",
          },
          {
            data: "state",
            render: function (data, type, full, meta) {
              var state = "";
              if (data == 2) {
                state = '<span class="label label-warning">PENDIENTE</span>';
              }
              if (data == 3) {
                state = '<span class="label label-primary">EN PROCESO</span>';
              }
              return state;
            },
            className: "text-center",
          },
          { data: "options", className: "text-center", Width: "40px" },
        ],
        initComplete: function (oSettings, json) {
          $("#" + table_name + "_wrapper div.container-options").append(
            $("#" + table_name + "-btns-tools").contents()
          );
        },
      })
      .on("processing.dt", function (e, settings, processing) {
        if (processing) {
          loaderin(".panel-pendings");
        } else {
          loaderout(".panel-pendings");
        }
      });
    if (document.querySelector("#transactions")) {
      var transactions = document.querySelector("#transactions");
      transactions.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions").parsley().isValid()) {
          let client = document.querySelector("#listClients").value;
          let affairs = document.querySelector("#listAffairs").value;
          if (client == "") {
            alert_msg(
              "info",
              "No seleccionaste ningún cliente, seleccione uno."
            );
            return false;
          }
          if (affairs == "") {
            alert_msg(
              "info",
              "No seleccionaste ningún asunto, seleccione uno."
            );
            return false;
          }
          loading.style.display = "flex";
          tinyMCE.triggerSave();
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/tickets/action";
          var formData = new FormData(transactions);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $("#modal-action").modal("hide");
                transactions.reset();
                refresh_table();
                if (objData.modal) {
                  alert_msg("success", objData.msg);
                  $("#modal-message").modal("show");
                  document.querySelector("#text-title-message").innerHTML =
                    "Ticket Nº " + objData.code;
                  document.querySelector("#text_country").innerHTML =
                    "+" + objData.country_code;
                  document.querySelector("#country_code").value =
                    objData.country_code;
                  document.querySelector("#bill_number_client").value =
                    objData.mobile;
                  document.querySelector("#idpdfticket").value =
                    objData.encrypt;
                  document.querySelector("#msg").value = objData.message_wsp;
                } else {
                  alert_msg("success", objData.msg);
                }
              } else if (objData.status == "exists") {
                $("#modal-action").modal("hide");
                transactions.reset();
                alert_msg("info", objData.msg);
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
    list_clients();
    list_technical();
    list_incidents();
    $("#attention_date").datetimepicker({ locale: "es" });
    $("#attention_date").val(moment().format("DD/MM/YYYY H:mm"));
    $("#transactions").parsley();
    $("#listPriority,#filter_tickets").select2({
      minimumResultsForSearch: -1,
    });
  },
  false
);
function filter_tickets() {
  table.ajax
    .url(
      base_url + "/tickets/list_current?ticket=" + $("#filter_tickets").val()
    )
    .load();
}
function view(idticket) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/tickets/view_ticket/" + idticket;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#text-view").innerHTML =
          "Ticket Nº " + objData.data.ticket.code;
        document.querySelector("#view-client").innerHTML =
          objData.data.ticket.client;
        document.querySelector("#view-celdoc").innerHTML =
          "<b>" +
          objData.data.ticket.type_doc +
          ":</b> " +
          objData.data.ticket.document +
          " <b>CEL:</b> " +
          objData.data.ticket.mobile;
        document.querySelector("#view-address").innerHTML =
          objData.data.ticket.address;
        document.querySelector("#view-image-client").src = generateAvatar(
          objData.data.ticket.client
        );
        document.querySelector("#view-created").innerHTML =
          "Creado " +
          moment(objData.data.ticket.registration_date).format(
            "DD/MM/YYYY H:mm"
          );
        document.querySelector("#view-visit").innerHTML =
          "Programado " +
          moment(objData.data.ticket.attention_date).format("DD/MM/YYYY H:mm");
        var star = "";
        if (objData.data.ticket.priority == 1) {
          document.querySelector("#view-priority").innerHTML =
            '<span style="color:#00acac;border: 1px solid #00acac;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">BAJA</span>';
          document.querySelector("#view-star").innerHTML =
            '<i class="fas fa-star"></i>';
        } else if (objData.data.ticket.priority == 2) {
          document.querySelector("#view-priority").innerHTML =
            '<span style="color:#4da1ff;border: 1px solid #4da1ff;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">MEDIA</span>';
          document.querySelector("#view-star").innerHTML =
            '<i class="fas fa-star"></i><i class="fas fa-star"></i>';
        } else if (objData.data.ticket.priority == 3) {
          document.querySelector("#view-priority").innerHTML =
            '<span style="color:#F59C1A;border: 1px solid #F59C1A;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">ALTA</span>';
          document.querySelector("#view-star").innerHTML =
            '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';
        } else if (objData.data.ticket.priority == 4) {
          document.querySelector("#view-priority").innerHTML =
            '<span style="color:#ff5959;border: 1px solid #ff5959;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">URGENTE</span>';
          document.querySelector("#view-star").innerHTML =
            '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';
        }
        if (objData.data.ticket.state == 1) {
          document.querySelector("#view-state").innerHTML = "RESUELTO";
        } else if (objData.data.ticket.state == 2) {
          document.querySelector("#view-state").innerHTML = "PENDIENTE";
        } else if (objData.data.ticket.state == 3) {
          document.querySelector("#view-state").innerHTML = "EN PROCESO";
        } else if (objData.data.ticket.state == 4) {
          document.querySelector("#view-state").innerHTML = "NO RESUELTO";
        } else if (objData.data.ticket.state == 5) {
          document.querySelector("#view-state").innerHTML = "VENCIDO";
        } else if (objData.data.ticket.state == 6) {
          document.querySelector("#view-state").innerHTML = "CANCELADO";
        }
        document.querySelector("#view-description").innerHTML =
          objData.data.ticket.description;
        document.querySelector("#view-incident").innerHTML =
          objData.data.ticket.incident;
        if (objData.data.ticket.user_image == "user_default.png") {
          user_image = base_url + "/Assets/images/default/user_default.png";
        } else {
          user_image =
            base_url +
            "/Assets/uploads/users/" +
            objData.data.ticket.user_image;
        }
        document.querySelector("#view-user-post").src = user_image;
        document.querySelector("#view-user").innerHTML =
          "<b>" + objData.data.ticket.user + "</b>";
        var comments = 0;
        var data_detail = "";
        if (objData.data.detail.length === 0) {
          data_detail += `
                 <li class="comment">NO HAY RESPUESTAS</li>`;
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
        $("#post-comment").html(data_detail);
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
function options_print(idticket) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/tickets/select_record/" + idticket;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#text-title-message").innerHTML =
          "Ticket Nº " + objData.data.code;
        document.querySelector("#text_country").innerHTML =
          "+" + objData.data.country_code;
        document.querySelector("#country_code").value =
          objData.data.country_code;
        document.querySelector("#bill_number_client").value =
          objData.data.mobile;
        document.querySelector("#idpdfticket").value = objData.data.encrypt;
        document.querySelector("#msg").value = objData.data.message_wsp;
        $("#modal-message").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}

$("#btn-msg").on("click", function () {
  const country = document.querySelector("#country_code").value;
  const phone = document.querySelector("#bill_number_client").value;
  const numberPhone = `${country}${phone}`;
  const message = document.querySelector("#msg").value;
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
/* FORMAS DE IMRPESION */
$("#btn-ticket").on("click", function () {
  var idticket = document.querySelector("#idpdfticket").value;
  window.open(base_url + "/tickets/view_pdf/" + idticket, "_blank");
});
function finalize(idticket) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "success",
    icon: "far fa-question-circle",
    title: "ADVERTENCIA",
    content: "¿Desea ingresar al ticket?",
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
          var ajaxUrl = base_url + "/tickets/select_record/" + idticket;
          request.open("GET", ajaxUrl, true);
          request.send();
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              alsup.close();
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                if (objData.data.state == 1) {
                  if (user_profile == 1) {
                    $(location).attr(
                      "href",
                      base_url + "/tickets/finalize/" + idticket
                    );
                  } else {
                    $('[data-toggle="tooltip"]').tooltip("hide");
                    alert_msg("info", "El ticket ya esta resuelto.");
                    refresh_table();
                  }
                } else if (objData.data.state == 6) {
                  $('[data-toggle="tooltip"]').tooltip("hide");
                  alert_msg("error", "El ticket esta cancelado.");
                  refresh_table();
                } else if (
                  objData.data.state == 2 ||
                  objData.data.state == 3 ||
                  objData.data.state == 4 ||
                  objData.data.state == 5
                ) {
                  $(location).attr(
                    "href",
                    base_url + "/tickets/finalize/" + idticket
                  );
                } else {
                  $('[data-toggle="tooltip"]').tooltip("hide");
                  alert_msg("error", "Ticket no encontrado.");
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
function update(idticket) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions").parsley().reset();
  document.querySelector("#text-title").innerHTML = "Actualizar Ticket";
  document.querySelector("#text-button").innerHTML = "Guardar Cambios";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/tickets/select_record/" + idticket;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status) {
        document.querySelector("#idticket").value = objData.data.encrypt;
        document.querySelector("#listClients").value =
          objData.data.encrypt_client;
        $("#listClients").select2();
        document.querySelector("#listTechnical").value =
          objData.data.encrypt_technical;
        $("#listTechnical").select2();
        document.querySelector("#listAffairs").value =
          objData.data.encrypt_incident;
        $("#listAffairs").select2();
        document.querySelector("#attention_date").value = moment(
          objData.data.attention_date
        ).format("DD/MM/YYYY H:mm");
        document.querySelector("#listPriority").value = objData.data.priority;
        $("#listPriority").select2({ minimumResultsForSearch: -1 });
        document.querySelector("#description").value = objData.data.description;
        $("#modal-action").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function list_clients() {
  if (document.querySelector("#listClients")) {
    var ajaxUrl = base_url + "/tickets/list_clients";
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
function list_technical() {
  if (document.querySelector("#listTechnical")) {
    var ajaxUrl = base_url + "/tickets/list_technical";
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
function list_incidents() {
  if (document.querySelector("#listAffairs")) {
    var ajaxUrl = base_url + "/incidents/list_incidents";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listAffairs").innerHTML = request.responseText;
        $("#listAffairs").select2();
      }
    };
  }
}
function cancel(idticket) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "CANCELAR",
    content: "Esta seguro que desea cancelar este ticket.",
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
          var ajaxUrl = base_url + "/tickets/cancel";
          var strData = "idticket=" + idticket;
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
              } else if (objData.status == "exists") {
                $('[data-toggle="tooltip"]').tooltip("hide");
                alert_msg("info", objData.msg);
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
function modal() {
  document.querySelector("#text-title").innerHTML = "Nuevo Ticket";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#idticket").value = "";
  document.querySelector("#transactions").reset();
  $("#transactions").parsley().reset();
  $("#attention_date").val(moment().format("DD/MM/YYYY H:mm"));
  $("#listPriority").select2({ minimumResultsForSearch: -1 });
  $("#modal-action").modal("show");
  list_clients();
  list_technical();
  list_incidents();
}
