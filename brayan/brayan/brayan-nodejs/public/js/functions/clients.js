let table, table_bills;
let table_name = "list",
  table_name_bills = "list-bills-info";
const filebtn = document.querySelector("#import_clients");
const filetext = document.querySelector("#text-file");

const getColumns = () => {
  const isSimple = document.getElementById("simple").value ? true : false;
  const columns = [];
  const hideColumns = getHideColumns();

  if (!isSimple) {
    columns.push({
      data: "id",
      className: "text-center",
    });
  }

  columns.push({
    data: "client",
    render: function (data, type, full, meta) {
      var client = "";
      const styles = [];

      if (full.background) {
        styles.push(`background: ${full.background}`);
      }

      if (full.foreground) {
        styles.push(`color: ${full.foreground}`);
      }

      if (full.profile_user == 1) {
        if (full.permits_edit == 1) {
          client = `
            <a
              href="${base_url}/customers/view_client/${full.encrypt}"
              style="${styles.join("; ")}"
            >
              ${data}
            </a>
          `;
        } else {
          client = data;
        }
      } else {
        if (full.permits_edit == 1) {
          if (full.state == 1 || full.state == 2 || full.state == 5) {
            client = `
            <a
              href="${base_url}/customers/view_client/${full.encrypt}"
              style="${styles.join("; ")}"
            >
              ${data}
            </a>
          `;
          } else {
            client = data;
          }
        } else {
          client = data;
        }
      }
      return client;
    },
  });

  if (!isSimple) {
    columns.push({ data: "document", className: "text-center" });
  }

  columns.push({
    data: "mobile",
    render: (data, type, full) => {
      const component = document.createElement("div");

      const funcElementE = (value) => {
        return `
          <a href="javascript:;" onclick="modal_tools('${full.encrypt_client}', '${value}')" style="display: block;">
            <i class="fa fa-mobile mr-1"></i> ${value}
          </a>
        `;
      };

      component.innerHTML = funcElementE(full.mobile);

      if (full.mobile_optional) {
        component.innerHTML += funcElementE(full.mobile_optional);
      }
      // response
      return component.outerHTML;
    },
  });

  if (!isSimple) {
    columns.push({
      data: "net_ip",
      render: (data, type, full) => {
        if (data == "") {
          return "NO REGISTRADO";
        } else {
          const a = document.createElement("a");
          a.href = `http://${data}`;
          a.innerHTML = `<i class="fa fa-network-wired mr-1"></i> ${data}`;
          a.target = "_blank";
          return a.outerHTML;
        }
      },
    });
  }

  if (!hideColumns.includes("coordenadas")) {
    columns.push({
      data: "coordinates",
      render: function (data, type, full, meta) {
        var coordinates = "";
        if (data == "") {
          coordinates = "NO REGISTRADO";
        } else {
          coordinates =
            '<a href="geo:' +
            data +
            '" target="_blank"><i class="fa fa-mobile mr-1 text-danger"></i> App</a> | ' +
            '<a href="' +
            base_url +
            "/customers/customer_location/" +
            full.encrypt_client +
            '">' +
            '<i class="fa fa-map-marker-alt mr-1 text-danger"></i>' +
            data +
            "</a>";
        }
        return coordinates;
      },
    });
  }

  columns.push({ data: "pending_payments", className: "text-center" });
  columns.push({ data: "payday", className: "text-center" });

  if (!isSimple) {
    columns.push({ data: "last_payment" });
    columns.push({ data: "payment_date" });
  }

  columns.push({ data: "services" });

  if (!isSimple) {
    columns.push(
      ...[
        {
          data: "suspension_date",
          render: function (data, type, full, meta) {
            var suspension_date = "";
            if (data == "0000-00-00") {
              suspension_date = "00/00/0000";
            } else {
              suspension_date = moment(data).format("DD/MM/YYYY");
            }
            return suspension_date;
          },
        },
        {
          data: "finish_date",
          render: function (data, type, full, meta) {
            var finish_date = "";
            if (data == "0000-00-00") {
              finish_date = "00/00/0000";
            } else {
              finish_date = moment(data).format("DD/MM/YYYY");
            }
            return finish_date;
          },
        },
      ]
    );
  }

  if (!hideColumns.includes("direccion")) {
    columns.push({ data: "address" });
  }

  if (!isSimple) {
    columns.push({ data: "reference" });
  }

  columns.push({
    data: "state",
    render: function (data, type, full, meta) {
      var state = "";
      if (full.state == 1) {
        state = '<span class="label label-orange">INSTALACIÓN</span>';
      }
      if (full.state == 2) {
        state = '<span class="label label-success">ACTIVO</span>';
      }
      if (full.state == 3) {
        state = '<span class="label label-primary">SUSPENDIDO</span>';
      }
      if (full.state == 4) {
        state = '<span class="label label-dark">CANCELADO</span>';
      }
      if (full.state == 5) {
        state = '<span class="label label-indigo">GRATIS</span>';
      }
      return state;
    },
  });

  if (!isSimple) {
    columns.push({ data: "options", className: "text-center", sWidth: "40px" });
  }

  // response
  return columns;
};

const getOrderPayday = () => $("#orderPayday").val();

const getOrderDeuda = () => $("#orderDeuda").val();

const getFilterStart = () => $("#filterStart").val();

const getFilterOver = () => $("#filterOver").val();

document.addEventListener(
  "DOMContentLoaded",
  function () {
    generateListDays("filterStart");
    generateListDays("filterOver");
    table_configuration("#" + table_name, "Lista de clientes");

    const currentUrl = new URL(this.location.href);
    const state = currentUrl.searchParams.get("state") || 0;
    $("#filter_states").val(state);

    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url: `${base_url}/customers/list_records/${state}`,
          dataSrc: "",
          data: function (data) {
            const orderDeuda = getOrderDeuda();
            const orderPayday = getOrderPayday();
            const filterStart = getFilterStart();
            const filterOver = getFilterOver();
            // filter deuda
            if (orderDeuda) {
              data.orderDeuda = orderDeuda;
            }
            // filter payday
            if (orderPayday) {
              data.orderPayday = orderPayday;
            }
            // filter start
            if (filterStart) {
              data.paydayStart = filterStart;
            }
            // filter over
            if (filterOver) {
              data.paydayOver = filterOver;
            }

            // agregar background
            // getInfoBackground();
          },
        },
        deferRender: true,
        rowId: "id",
        columns: getColumns(),
        initComplete: function (oSettings, json) {
          $("#" + table_name + "_wrapper .dt-buttons").append(
            $("#" + table_name + "-btns-exportable").contents()
          );
          $("#" + table_name + "_wrapper div.container-options").append(
            $("#" + table_name + "-btns-tools").contents()
          );
        },
      })
      .on("processing.dt", function (e, settings, processing) {
        if (processing) {
          loaderin(".panel-clients");
        } else {
          loaderout(".panel-clients");
        }
      });
    if (document.querySelector("#transactions_import")) {
      var transactions_import = document.querySelector("#transactions_import");
      transactions_import.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_import").parsley().isValid()) {
          if ($("#import_clients").get(0).files.length == 0) {
            alert_msg(
              "warning",
              "Selecionar un archivo excel para realizar el proceso."
            );
            return false;
          } else {
            var allowed_extensions = [".xls", ".xlsx"];
            var file = $("#import_clients");
            var exp_reg = new RegExp(
              "([a-zA-Z0-9s_\\-.:])+(" + allowed_extensions.join("|") + ")$"
            );
            if (!exp_reg.test(file.val().toLowerCase())) {
              alert_msg(
                "warning",
                "Selecionar una extensión permitida .xls o .xlsx."
              );
              return false;
            }
            loading.style.display = "flex";
            var request = window.XMLHttpRequest
              ? new XMLHttpRequest()
              : new ActiveXObject("Microsoft.XMLHTTP");
            var ajaxUrl = base_url + "/customers/import";
            var formData = new FormData(transactions_import);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
              if (request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText);
                if (objData.status == "success") {
                  $("#modal-import").modal("hide");
                  alert_msg("success", objData.msg);
                  refresh_table();
                } else if (objData.status == "warning") {
                  $("#modal-import").modal("hide");
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
        }
      };
    }
    if (document.querySelector("#transactions_ticket")) {
      var transactions_ticket = document.querySelector("#transactions_ticket");
      transactions_ticket.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_ticket").parsley().isValid()) {
          let affairs = document.querySelector("#listAffairs").value;
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
          var ajaxUrl = base_url + "/customers/action_ticket";
          var formData = new FormData(transactions_ticket);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $("#modal-ticket").modal("hide");
                transactions_ticket.reset();
                alert_msg("success", objData.msg);
              } else if (objData.status == "exists") {
                $("#modal-ticket").modal("hide");
                transactions_ticket.reset();
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
    list_technical();
    list_incidents();
    $("#listPriority").select2({ minimumResultsForSearch: -1 });
    $("#transactions_ticket").parsley();
    $("#attention_date").datetimepicker({ locale: "es" });
    $("#attention_date").val(moment().format("DD/MM/YYYY H:mm"));
    $("#filter_states").select2({ minimumResultsForSearch: -1 });
  },
  false
);

function list_technical() {
  if (document.querySelector("#listTechnical")) {
    var ajaxUrl = base_url + "/customers/list_technical";
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

function changeOrderDeuda() {
  $(`#${table_name}`).DataTable().ajax.reload();
}

function filter_states() {
  table.ajax
    .url(base_url + "/customers/list_records/" + $("#filter_states").val())
    .load();
}

$("#import_clients").on("change", function () {
  document.querySelector("#text-file").value = this.files.item(0).name;
});

filetext.addEventListener("click", function () {
  filebtn.click();
});

function add() {
  $(location).attr("href", base_url + "/customers/add");
}

function view(idcontract) {
  $(location).attr("href", base_url + "/customers/view_client/" + idcontract);
}

function getMessageDeudaTotal(idclient) {
  axios
    .get(`${base_url}/customers/select_info_message/${idclient}`)
    .then(({ data }) => {
      if (!data.success) throw new Error(data.message);
      $("#btn-whatsapp-massive").prop("disabled", false);
      $("#message-whatsapp-massive").val(data.message);
    })
    .catch((err) => {
      $("#btn-whatsapp-massive").prop("disabled", true);
      alert_msg("warning", err);
    });
}

function modal_tools(idclient, number) {
  $("#btn-whatsapp-massive").prop("disabled", true);
  getMessageDeudaTotal(idclient);
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_client/" + idclient;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#text-title-tools").innerHTML =
          objData.data.names + " " + objData.data.surnames + " - " + number;
        document.querySelector("#tools_country").value =
          objData.data.country_code;
        document.querySelector("#tools_number").value = number;
        $("#modal-tools").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
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

$("#btn-whatsapp-massive").on("click", function () {
  const country = document.querySelector("#tools_country").value;
  const phone = document.querySelector("#tools_number").value;
  const numberPhone = `${country}${phone}`;
  const message = $("#message-whatsapp-massive").val();
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

function getHideColumns() {
  const hideColumns = document.getElementById("hideColumns").innerText;
  if (!hideColumns) return [];
  return JSON.parse(hideColumns);
}

function cancel(idcontract, client) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "CANCELAR",
    content:
      'Esta seguro que desea cancelar el servicio al cliente <b class="text-warning">' +
      client +
      "</b>.",
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
          var ajaxUrl = base_url + "/customers/cancel";
          var strData = "idcontract=" + idcontract;
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

function layoff(idcontract, client) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "SUSPENDER",
    content:
      'Esta seguro que desea suspender el servicio al cliente <b class="text-warning">' +
      client +
      "</b>.",
    buttons: {
      layoff: {
        text: "Aceptar",
        btnClass: "btn-info",
        action: function () {
          this.buttons.layoff.setText(
            '<i class="fas fa-spinner fa-spin icodialog"></i> Procesando...'
          );
          this.buttons.layoff.disable();
          $(".jconfirm-closeIcon").remove();
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/customers/layoff";
          var strData = "idcontract=" + idcontract;
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

function activate(idcontract, client) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ACTIVAR",
    content:
      'Esta seguro que desea activar al cliente <b class="text-warning">' +
      client +
      "</b>.",
    buttons: {
      layoff: {
        text: "Aceptar",
        btnClass: "btn-info",
        action: function () {
          this.buttons.layoff.setText(
            '<i class="fas fa-spinner fa-spin"></i> Procesando...'
          );
          this.buttons.layoff.disable();
          $(".jconfirm-closeIcon").remove();
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/customers/activate";
          var strData = "idcontract=" + idcontract;
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

function delete_client(idclient, client) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ELIMINAR",
    content:
      '¿Está seguro que desea eliminar al cliente <b class="text-warning">' +
      client +
      "</b>?",
    buttons: {
      layoff: {
        text: "Aceptar",
        btnClass: "btn-info",
        action: function () {
          this.buttons.layoff.setText(
            '<i class="fas fa-spinner fa-spin icodialog"></i> Eliminando...'
          );
          this.buttons.layoff.disable();
          $(".jconfirm-closeIcon").remove();

          axios
            .post(`${base_url}/customers/delete/${idclient}`)
            .then((response) => {
              $('[data-toggle="tooltip"]').tooltip("hide");
              alert_msg("success", response.data.message);
              refresh_table();
            })
            .catch((err) => {
              $('[data-toggle="tooltip"]').tooltip("hide");
              const errorMsg =
                err?.response?.data?.message ||
                err?.message ||
                "No se pudo eliminar el cliente";
              alert_msg("error", errorMsg);
            });
        },
      },
      close: {
        text: "Cancelar",
      },
    },
  });
}

function ticket(idclient) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  document.querySelector("#text-ticket").innerHTML = "Nuevo Ticket";
  document.querySelector("#text-button-ticket").innerHTML = "Guardar Registro";
  document.querySelector("#idticket").value = "";
  document.querySelector("#transactions_ticket").reset();
  $("#transactions_ticket").parsley().reset();
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_client/" + idclient;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#idticketclient").value =
          objData.data.encrypt_id;
        document.querySelector("#client_ticket").value =
          objData.data.names + " " + objData.data.surnames;
        $("#attention_date").val(moment().format("DD/MM/YYYY H:mm"));
        $("#modal-ticket").modal("show");
        $("#listPriority").select2({ minimumResultsForSearch: -1 });
        list_technical();
        list_incidents();
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}

function modal_import() {
  $('[data-toggle="tooltip"]').tooltip("hide");
  document.querySelector("#text-title-import").innerHTML = "Importar Clientes";
  document.querySelector("#text-button-import").innerHTML = "Importar";
  document.querySelector("#transactions_import").reset();
  $("#modal-import").modal("show");
}

function exports() {
  alert_msg("loader", "Generando excel.");
  $('[data-toggle="tooltip"]').tooltip("hide");
  setTimeout(function () {
    $("#gritter-notice-wrapper").remove();
    alert_msg("success", "Se exporto excel correctamente.");
    window.open(base_url + "/customers/export", "_blank");
  }, 1000);
}

function generateListDays(selectId) {
  const select = document.getElementById(selectId);
  if (!select) return;
  select.innerHTML = "";
  const currentDay = moment().format("D");
  for (let i = 1; i < 31; i++) {
    const option = document.createElement("option");
    const text = `${i}`.length == 2 ? i : `0${i}`;
    option.value = i;
    option.text = text;
    option.selected = i == currentDay;
    select.appendChild(option);
  }
}

function changeFilterDays(selectId) {
  const select = $(`#${selectId}`);
  if (!select) return;
  $(`#${table_name}`).DataTable().ajax.reload();
}

function getInfoBackground() {
  const params = new URLSearchParams();
  const orderDeuda = getOrderDeuda();
  const filterStart = getFilterStart();
  const filterOver = getFilterOver();
  // filter deuda
  if (orderDeuda) {
    params.set("orderDeuda", orderDeuda);
  }
  // filter start
  if (filterStart) {
    params.set("paydayStart", filterStart);
  }
  // filter over
  if (filterOver) {
    params.set("paydayOver", filterOver);
  }
  // request
  axios
    .get(
      `${base_url}/customers/info_conexion_background/0?${params.toString()}`
    )
    .then(({ data }) => {
      const content = document.getElementById("info_background");
      if (!content) return;
      content.innerHTML = "";
      data?.forEach((item) => {
        const info = document.createElement("span");
        const styles = [
          `background: ${item.background}`,
          `color: ${item.foreground}`,
        ];
        // add info component
        info.className = "badge badge-warning ml-1";
        info.style = styles.join("; ");
        info.innerText = `${item.nombre}: ${item.counter}`;
        content.appendChild(info);
      });
    })
    .catch((err) => console.log(err));
}
