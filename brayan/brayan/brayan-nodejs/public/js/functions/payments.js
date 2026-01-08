let table;
let table_name = "list";
var payment_data_selected = {};
var footer =
  '<table  class="table table-bordered text-center">' +
  '<thead class="table-dark"><tr><th>ESTADO</th><th>CANTIDAD</th><th>TOTAL</th></tr></thead><tbody>' +
  '<tr><td class="received_detail f-s-12">RECIBIDAS</td>' +
  '<td class="received_amount f-s-12">0</td>' +
  '<td class="received_total f-s-12">0</td></tr>' +
  '<tr><td class="canceled_detail f-s-12">ANULADAS</td>' +
  '<td class="canceled_amount f-s-12">0</td>' +
  '<td class="canceled_total f-s-12">0</td></tr>' +
  '<tr class="table-active"><td class="total_detail f-s-12">TOTALES</td>' +
  '<td class="total_amount f-s-12">0</td>' +
  '<td class="total_total f-s-12">0</td></tr>' +
  "</tbody></table>";

function generateUrlWithParams(url = new URL()) {
  const start = $("#start").val();
  const end = $("#end").val();
  const listPayments = !!$("#listPayments").val()
    ? parseInt($("#listPayments").val())
    : null;
  const listUsers = !!$("#listUsers").val()
    ? parseInt($("#listUsers").val())
    : null;
  const listStates = parseInt($("#listStates").val());
  const listZona = $("#listZona").val();

  if (start) {
    url.searchParams.set("start", start);
  }

  if (end) {
    url.searchParams.set("end", end);
  }

  if (listPayments) {
    url.searchParams.set("type", listPayments);
  }

  if (listUsers) {
    url.searchParams.set("user", listUsers);
  }

  if (listStates) {
    url.searchParams.set("state", listStates);
  }

  if (listZona) {
    url.searchParams.set("zonaId", listZona);
  }

  return url;
}

document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name, "Lista de transacciones");
    const url = generateUrlWithParams(
      new URL(`${base_url}/payments/list_records`)
    );
    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url: url.toString(),
          dataSrc: "",
        },
        stripeClasses: ["stripe1", "stripe2"],
        deferRender: true,
        rowId: "id",
        columns: [
          {
            data: "id",
            render: function (data, type, full, meta) {
              return '<input type="checkbox" class="checkbox-select" style="font-size: 50px;">';
            },
            className: "text-center",
          },
          { data: "internal_code", className: "text-center" },
          {
            data: "client",
            render: function (data, type, full, meta) {
              var client = "";
              if (full.encrypt_contract == "") {
                var client = data;
              } else {
                var client =
                  '<a href="' +
                  base_url +
                  "/customers/view_client/" +
                  full.encrypt_contract +
                  '">' +
                  data +
                  "</a>";
              }
              return client;
            },
          },
          { data: "zona" },
          {
            data: "invoice",
            render: function (data, type, full, meta) {
              return (
                '<a href="javascript:;" onclick="view_bill(\'' +
                full.encrypt_bill +
                "')\">" +
                data +
                "</a>"
              );
            },
            className: "text-center",
          },
          {
            data: "payment_date",
            render: function (data, type, full, meta) {
              return moment(data).format("DD/MM/YYYY H:mm");
            },
          },
          { data: "bill_total" },
          { data: "amount_paid" },
          { data: "payment_type" },
          { data: "user" },
          { data: "comment" },
          {
            data: "state",
            render: function (data, type, full, meta) {
              var state = "";
              if (data == 1) {
                state = '<span class="label label-success">RECIBIDO</span>';
              }
              if (data == 2) {
                state = '<span class="label label-dark">ANULADO</span>';
              }
              return state;
            },
            className: "text-center",
          },
          { data: "options", className: "text-center", aWidth: "40px" },
        ],
        createdRow: function (row, data, index) {
          if (array_key_exists(data.id, payment_data_selected)) {
            $(row).addClass("selected");
            $(row).find(".checkbox-select").attr("checked", true);
          }
        },
        infoCallback: function (settings, start, end, max, total, pre) {
          updateAmountSelected();
          return pre;
        },
        drawCallback: function (settings) {
          var received_amount = 0,
            received_total = 0;
          var canceled_amount = 0,
            canceled_total = 0;
          var api = this.api().rows({ search: "applied" }).data();
          $.each(api, function (key, value) {
            switch (value["count_state"]) {
              case "RECIBIDA":
                received_amount++;
                received_total += parseFloat(value["count_total"]);
                break;
              case "ANULADO":
                canceled_amount++;
                canceled_total += parseFloat(value["count_total"]);
                break;
            }
          });
          var total_amount = received_amount + canceled_amount;
          var total_total = received_total + canceled_total;
          $(".invoice_summary").html(footer);
          $(".received_amount").html(received_amount);
          $(".received_total").html(
            currency_symbol + formatMoney(received_total)
          );
          $(".canceled_amount").html(canceled_amount);
          $(".canceled_total").html(
            currency_symbol + formatMoney(canceled_total)
          );
          $(".total_amount").html(total_amount);
          $(".total_total").html(currency_symbol + formatMoney(total_total));
        },
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
          loaderin(".panel-payments");
        } else {
          loaderout(".panel-payments");
        }
      })
      .on("draw", function () {});
    if (document.querySelector("#transactions_payments")) {
      var transactions_payments = document.querySelector(
        "#transactions_payments"
      );
      transactions_payments.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_payments").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/payments/modify_payment";
          var formData = new FormData(transactions_payments);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                transactions_payments.reset();
                refresh_table();
                $("#modal-payment").modal("hide");
                alert_msg("success", objData.msg);
              } else if (objData.status == "warning") {
                alert_msg("warning", objData.msg);
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
    list_users();
    filter_runway();
    list_runway();
    $("#date_time").datetimepicker({
      locale: "es",
    });
    $("#total_payment").TouchSpin({
      min: 0.01,
      max: 10000000000,
      step: 0.01,
      decimals: 2,
      buttondown_class: "btn btn-default f-w-700",
      buttonup_class: "btn btn-default f-w-700",
    });
    $("#start,#end").datetimepicker({
      locale: "es",
      format: "L",
    });
    $("#start").val(moment().startOf("month").format("DD/MM/YYYY"));
    $("#end").val(moment().endOf("month").format("DD/MM/YYYY"));
    $("#listStates").select2({ minimumResultsForSearch: -1 });
  },
  false
);
$("#" + table_name + " tbody").on("click", ".checkbox-select", function (e) {
  let data = table.row($(this).closest("tr")).data();
  if ($(this).is(":checked")) {
    $(this).closest("tr").addClass("selected");
    payment_data_selected[data.id] = data;
  } else {
    $(this).closest("tr").removeClass("selected");
    delete payment_data_selected[data.id];
  }
  updateAmountSelected();
});
function updateAmountSelected() {
  $("." + table_name + "_amount_selected").html(
    Object.keys(payment_data_selected).length
  );
}
$("#btn-select-all-" + table_name + "_wrapper").click(function () {
  $('[data-toggle="tooltip"]').tooltip("hide");
  table.rows().every(function (rowIdx, tableLoop, rowLoop) {
    let data = this.data();
    if (!array_key_exists(data.id, payment_data_selected)) {
      payment_data_selected[data.id] = data;
    }
  });
  updateAmountSelected();
  refresh_table();
});
$("#btn-unselect-all-" + table_name + "_wrapper").click(function () {
  if (Object.keys(payment_data_selected).length > 0) {
    payment_data_selected = [];
    refresh_table();
    $('[data-toggle="tooltip"]').tooltip("hide");
  }
});
function export_excel() {
  alert_msg("loader", "Generando excel.");
  setTimeout(function () {
    $("#gritter-notice-wrapper").remove();
    alert_msg("success", "El excel se ha generado correctamente.");
    const url = generateUrlWithParams(
      new URL(`${base_url}/payments/export_excel`)
    );
    window.open(url.toString(), "_blank");
  }, 1000);
}
function export_pdf() {
  alert_msg("loader", "Generando pdf.");
  setTimeout(function () {
    $("#gritter-notice-wrapper").remove();
    alert_msg("success", "El pdf se ha generado correctamente.");
    const url = generateUrlWithParams(
      new URL(`${base_url}/payments/export_pdf`)
    );
    window.open(url.toString(), "_blank");
  }, 1000);
}
function list_runway() {
  if (document.querySelector("#listTypePay")) {
    var ajaxUrl = base_url + "/runway/list_runway";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listTypePay").innerHTML = request.responseText;
        $("#listTypePay").select2();
      }
    };
  }
}
function filter_runway() {
  if (document.querySelector("#listPayments")) {
    var ajaxUrl = base_url + "/runway/filter_runway";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listPayments").innerHTML =
          request.responseText;
        $("#listPayments").select2();
      }
    };
  }
}
$("#btn-search").on("click", function () {
  const url = generateUrlWithParams(
    new URL(`${base_url}/payments/list_records`)
  );
  table.ajax.url(url.toString()).load();
});

function list_users() {
  if (document.querySelector("#listUsers")) {
    var ajaxUrl = base_url + "/users/list_users";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listUsers").innerHTML = request.responseText;
        $("#listUsers").select2();
      }
    };
  }
}

function view_bill(idbill) {
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/payments/view_bill/" + idbill;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#text-view").innerHTML =
          "Detalle de factura (" +
          objData.data.bill.voucher +
          " Nº " +
          objData.data.bill.serie +
          ")";
        document.querySelector("#view-client").innerHTML =
          objData.data.bill.names + " " + objData.data.bill.surnames;
        document.querySelector("#view-typedoc").innerHTML =
          objData.data.bill.type_doc;
        document.querySelector("#view-doc").innerHTML =
          objData.data.bill.document;
        document.querySelector("#view-address").innerHTML =
          objData.data.bill.address;
        document.querySelector("#view-mobile").innerHTML =
          objData.data.bill.mobile;
        document.querySelector("#view-issue").innerHTML =
          "Emisión " +
          moment(objData.data.bill.date_issue).format("DD/MM/YYYY");
        document.querySelector("#view-expiration").innerHTML =
          "Vencimiento " +
          moment(objData.data.bill.expiration_date).format("DD/MM/YYYY");
        document.querySelector("#view-sub").innerHTML =
          currency_symbol + objData.data.bill.subtotal;
        document.querySelector("#view-dis").innerHTML =
          currency_symbol + objData.data.bill.discount;
        document.querySelector("#view-total").innerHTML =
          currency_symbol + objData.data.bill.total;
        document.querySelector("#view-observation").innerHTML =
          objData.data.bill.observation;
        if (objData.data.bill.state == 1) {
          document.querySelector("#view-state").innerHTML = "PAGADO";
        } else if (objData.data.bill.state == 2) {
          document.querySelector("#view-state").innerHTML = "PENDIENTE";
        } else if (objData.data.bill.state == 3) {
          document.querySelector("#view-state").innerHTML = "VENCIDO";
        } else if (objData.data.bill.state == 4) {
          document.querySelector("#view-state").innerHTML = "ANULADO";
        }
        if (objData.data.bill.sales_method == 1) {
          document.querySelector("#view-method").innerHTML = "CONTADO";
        } else if (objData.data.bill.sales_method == 2) {
          document.querySelector("#view-method").innerHTML = "CREDITO";
        }

        var template_trans = "";
        if (objData.data.payments.length === 0) {
          template_trans += `
            			<tr>
            				<td class="text-center" colspan="5">No hay pagos registrados.</td>
            			</tr>
            			`;
        } else {
          objData.data.payments.forEach((payment) => {
            template_trans += `
                          <tr>
                              <td class="text-center">${
                                payment.internal_code
                              }</td>
                              <td class="text-center">${moment(
                                payment.payment_date
                              ).format("DD/MM/YYYY H:mm")}</td>
                              <td class="text-center">${currency_symbol}${
              payment.amount_paid
            }</td>
                              <td>${payment.payment_type}</td>
                              <td>${payment.names}</td>
                          </tr>;
                      `;
          });
        }
        $("#view-table-payments tbody").html(template_trans);

        var template = "";
        if (objData.data.detail.length === 0) {
          template += `
            			<tr>
            				<td class="text-center" colspan="4">No hay registros.</td>
            			</tr>
            			`;
        } else {
          objData.data.detail.forEach((detail) => {
            template += `
                            <tr>
                                <td>${detail.description}</td>
                                <td class="text-center">${currency_symbol}${detail.price}</td>
                                <td class="text-center">${detail.quantity}</td>
                                <td class="text-center">${currency_symbol}${detail.total}</td>
                            </tr>;
                        `;
          });
        }
        $("#view-table tbody").html(template);
        $("#modal-view").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function cancel(idpayment) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ANULAR PAGO",
    content: "Esta seguro de anular esta transacción.",
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
          var ajaxUrl = base_url + "/payments/cancel";
          var strData = "idpayment=" + idpayment;
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
function update(idpayment) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions_payments").parsley().reset();
  document.querySelector("#text-button-payment").innerHTML = "Guardar Cambios";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/payments/select_record/" + idpayment;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        var transactions_payments = document.querySelector(
          "#transactions_payments"
        );
        transactions_payments.reset();
        document.querySelector("#text-payment").innerHTML =
          "Agregar Pago " +
          objData.data.voucher +
          " Nº " +
          objData.data.invoice;
        document.querySelector("#client_name").innerHTML = objData.data.client;
        document.querySelector("#text-alert").innerHTML =
          '<small class="text-success text-uppercase">El total pagado no es editable</small>';
        document.querySelector("#idpayment").value = objData.data.encrypt_id;
        document.querySelector("#idbill").value = objData.data.encrypt_bill;
        document.querySelector("#date_time").value = moment(
          objData.data.payment_date
        ).format("DD/MM/YYYY H:mm");
        document.querySelector("#idclient").value = objData.data.encrypt_client;
        document.querySelector("#total_payment").value =
          objData.data.amount_paid;
        $("#total_payment").trigger("touchspin.updatesettings", {
          max: objData.data.amount_paid,
        });
        document.querySelector("#total_payment").readOnly = true;
        document.querySelector("#comment").value = objData.data.comment;
        document.querySelector("#listTypePay").value = objData.data.paytypeid;
        $("#listTypePay").select2();
        $("#modal-payment").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function cancel_massive() {
  let send_data = Object.keys(payment_data_selected);
  let num_pay = $("." + table_name + "_amount_selected").text();
  if (send_data.length >= 1) {
    var alsup = $.confirm({
      theme: "modern",
      draggable: false,
      closeIcon: true,
      animationBounce: 2.5,
      escapeKey: false,
      type: "info",
      icon: "far fa-money-bill-alt",
      title: "CONFIRMACIÓN",
      content:
        'Cancelar (<b class="text-warning">' +
        num_pay +
        "</b>) pagos seleccionados",
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
            let request = window.XMLHttpRequest
              ? new XMLHttpRequest()
              : new ActiveXObject("Microsoft.XMLHTTP");
            let ajaxUrl = base_url + "/payments/cancel_massive";
            let formData = new FormData();
            formData.append("ids", send_data);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
              if (request.readyState != 4) return;
              if (request.status == 200) {
                alsup.close();
                let objData = JSON.parse(request.responseText);
                if (objData.status == "success") {
                  $('[data-toggle="tooltip"]').tooltip("hide");
                  alert_msg("success", objData.msg);
                  payment_data_selected = [];
                  refresh_table();
                } else if (objData.status == "info") {
                  $('[data-toggle="tooltip"]').tooltip("hide");
                  alert_msg("info", objData.msg);
                  payment_data_selected = [];
                  refresh_table();
                } else {
                  $('[data-toggle="tooltip"]').tooltip("hide");
                  alert_msg("error", objData.msg);
                  payment_data_selected = [];
                  refresh_table();
                }
              }
            };
          },
        },
        close: {
          text: "Cancelar",
        },
      },
    });
  } else {
    alert_msg("info", "Seleccionar mínimo un registro.");
  }
}
