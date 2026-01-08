let table;
let table_name = "list";
let number_client = document.querySelector("#bill_number_client");
var footer =
  '<table  class="table table-bordered text-center">' +
  '<thead class="table-dark"><tr><th>CANTIDAD</th><th>TOTAL</th></tr></thead>' +
  "<tbody><tr>" +
  '<td class="bill_amount f-s-12">0</td>' +
  '<td class="bill_total f-s-12">0</td></tr>' +
  "</tbody></table>";
document.addEventListener(
  "DOMContentLoaded",
  function () {
    const wspApi = getWhatsappApi();
    const wspBtn = document.getElementById("whatsapp-massive");
    if (!wspApi) wspBtn.style = "display: none";
    else wspBtn.style = "displaty: block";
    table_configuration("#" + table_name, "Lista de facturas pendientes");
    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url: " " + base_url + "/bills/list_pendings",
          dataSrc: "",
        },
        deferRender: true,
        idDataTables: "1",
        columns: [
          { data: "invoice", className: "text-center" },
          { data: "billing" },
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
          {
            data: "date_issue",
            render: function (data, type, full, meta) {
              return moment(data).format("DD/MM/YYYY");
            },
          },
          {
            data: "expiration_date",
            render: function (data, type, full, meta) {
              return moment(data).format("DD/MM/YYYY");
            },
          },
          { data: "total" },
          { data: "balance" },
          { data: "subtotal", visible: false },
          { data: "discount", visible: false },
          {
            data: "type",
            render: function (data, type, full, meta) {
              var type_bill = "";
              if (data == 1) {
                type_bill =
                  '<span style="color:#4da1ff;border: 1px solid #4da1ff;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">LIBRE</span>';
              }
              if (data == 2) {
                type_bill =
                  '<span style="color:#F59C1A;border: 1px solid #F59C1A;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">SERVICIOS</span>';
              }
              return type_bill;
            },
          },
          {
            data: "sales_method",
            render: function (data, type, full, meta) {
              var sales_method = "";
              if (data == 1) {
                sales_method = "CONTADO";
              }
              if (data == 2) {
                sales_method = "CREDITO";
              }
              return sales_method;
            },
          },
          { data: "observation" },
          {
            data: "state",
            render: function (data, type, full, meta) {
              var state = "";
              if (data == 2) {
                state = '<span class="label label-warning">PENDIENTE</span>';
              }
              if (data == 3) {
                state = '<span class="label label-danger">VENCIDO</span>';
              }
              return state;
            },
            className: "text-center",
          },
          { data: "options", className: "text-center", aWidth: "40px" },
        ],
        drawCallback: function (settings) {
          var bill_amount = 0,
            bill_total = 0;
          var api = this.api().rows({ search: "applied" }).data();
          $.each(api, function (key, value) {
            bill_amount++;
            bill_total += parseFloat(formatDecimal(value["remaining_amount"]));
          });
          $(".invoice_summary").html(footer);

          $(".bill_amount").html(formatMoney(bill_amount));
          $(".bill_total").html(currency_symbol + formatMoney(bill_total));
        },
        initComplete: function (oSettings, json) {
          $("#" + table_name + "_wrapper .dt-buttons").append(
            $("#" + table_name + "-btns-exportable").contents()
          );
        },
      })
      .on("processing.dt", function (e, settings, processing) {
        if (processing) {
          loaderin(".panel-pendings");
        } else {
          loaderout(".panel-pendings");
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
          var ajaxUrl = base_url + "/bills/create_payment";
          var formData = new FormData(transactions_payments);
          formData.set(
            "total_payment",
            formatDecimal($("#total_payment").val())
          );
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                transactions_payments.reset();
                refresh_table();
                $("#modal-payment").modal("hide");
                if (objData.modal) {
                  $("#modal-payment").on("hidden.bs.modal", function () {
                    $("#modal-voucher").modal("show");
                    alert_msg("success", objData.msg);
                    document.querySelector("#text-title-voucher").innerHTML =
                      objData.voucher + " Nº " + objData.correlative;
                    document.querySelector("#idbillvoucher").value =
                      objData.idbill;
                    document.querySelector("#text_country").innerHTML =
                      "+" + objData.country;
                    document.querySelector("#country_code").value =
                      objData.country;
                    document.querySelector("#bill_number_client").value =
                      objData.mobile;
                    document.querySelector("#msg").value = objData.message_wsp;
                  });
                } else {
                  alert_msg("success", objData.msg);
                }
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
    if (document.querySelector("#transactions_facser")) {
      var transactions_facser = document.querySelector("#transactions_facser");
      transactions_facser.onsubmit = function (e) {
        e.preventDefault();
        let client = document.querySelector("#client_serv").value;
        let state_current = localStorage.getItem("state_current");
        let state = document.querySelector("#statusserv").value;
        let tableplans = document.querySelector("#table-plans");
        let filas = tableplans.getElementsByTagName("tbody")[0];
        if (filas.children.length === 0) {
          alert_msg(
            "error",
            "No hay servicios, agregué uno o mas para poder realizar la facturación."
          );
          return false;
        }
        if (client == "") {
          alert_msg(
            "error",
            "No seleccionaste ningún cliente, seleccione uno."
          );
          return false;
        }
        if (state_current !== "") {
          if (state == 1 || state == 4) {
            alert_msg(
              "warning",
              "Para editar la factura esta tiene que estar en estado pendiente o vencido."
            );
            return false;
          }
        }
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/bills/action_bill";
        var formData = new FormData(transactions_facser);
        if (state_current !== "") {
          formData.append("state_current", state_current);
        }
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              transactions_facser.reset();
              alert_msg("success", objData.msg);
              refresh_table();
              $("#modal-facser").modal("hide");
            } else if (objData.status == "exists") {
              alert_msg("info", objData.msg);
            } else if (objData.status == "warning") {
              alert_msg("warning", objData.msg);
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
  },
  false
);
window.addEventListener(
  "load",
  function () {
    list_runway();
    voucher_service();
    list_clients_contract();
    $("#transactions_payments").parsley();
    $("#date_time").datetimepicker({ locale: "es" });
    $("#date_time").val(moment().format("DD/MM/YYYY H:mm"));
    $("#total_payment").TouchSpin({
      min: 0.01,
      max: 10000000000,
      step: 0.01,
      decimals: 2,
      buttondown_class: "btn btn-default f-w-700",
      buttonup_class: "btn btn-default f-w-700",
    });
    $("#servissue,#servexpiration").datetimepicker({
      locale: "es",
      format: "L",
    });
  },
  false
);
number_client.onpaste = function (event) {
  var str = event.clipboardData.getData("text/plain");
  matches = str.match(/\d+/g);
  var v1, v2, v3, v4;
  if (!matches[0]) {
    v1 = "";
  } else {
    if (matches[0] == "51") {
      v1 = "";
    } else {
      v1 = matches[0];
    }
  }
  if (!matches[1]) {
    v2 = "";
  } else {
    v2 = matches[1];
  }
  if (!matches[2]) {
    v3 = "";
  } else {
    v3 = matches[2];
  }
  if (!matches[3]) {
    v4 = "";
  } else {
    v4 = matches[3];
  }
  var values = v1 + v2 + v3 + v4;
  $(this).val(values);
  event.preventDefault();
};
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
function list_clients_contract() {
  if (document.querySelector("#client_serv")) {
    var ajaxUrl = base_url + "/bills/list_clients_contract";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#client_serv").innerHTML = request.responseText;
        $("#client_serv").select2();
      }
    };
  }
}
function voucher_service() {
  if (document.querySelector("#vouchersserv")) {
    var ajaxUrl = base_url + "/vouchers/list_vouchers";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#vouchersserv").innerHTML =
          request.responseText;
        $("#vouchersserv").select2();
      }
    };
  }
}
$("#vouchersserv").on("click change", function () {
  serie_serv($(this).val());
});
function serie_serv(idvoucher) {
  if (document.querySelector("#serieserv")) {
    var ajaxUrl = base_url + "/vouchers/series_vocuhers/" + idvoucher;
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#serieserv").innerHTML = request.responseText;
        $("#serieserv").select2();
      }
    };
  }
}
function addItem(option) {
  if (option == "libre") {
    items = `
        <tr class="item-row">
        <td class="text-center" style="line-height:30px">0</td>
        <td><input type="hidden" name="idproducto[]" value="0"><input type="hidden" name="tipo[]" value="1"><input class="form-control text-uppercase" placeholder="Descripción" name="descripcion[]" value=""></td>
        <td><input type="text" class="form-control costo" placeholder="100" name="costo[]" value="${roundNumber(
          0,
          2
        )}"></td>
        <td><input type="number" class="form-control unidad" placeholder="1" min="1" name="unidad[]" value="1"></td>
        <td><input type="text" class="form-control total" placeholder="100"  name="totales[]" value="${roundNumber(
          0,
          2
        )}"></td>
        <td class="text-center"><button type="button" class="deletefile btn btn-white"><i class="far fa-trash-alt"></i></button></td>
        </tr>
    `;
    $("#table-free tbody").append(items);
  } else if (option == "service") {
    items = `
        <tr class="item-row2">
        <td><input type="hidden" name="idproducto[]" value="0"><input type="hidden" name="tipo[]" value="1"><input class="form-control text-uppercase" placeholder="Descripción" name="descripcion[]" value=""></td>
        <td><input type="text" class="form-control costos" placeholder="100" name="costo[]" value="${roundNumber(
          0,
          2
        )}"></td>
        <td><input type="text" class="form-control unidads" placeholder="1" min="1" name="unidad[]" value="1"></td>
        <td><input type="text" class="form-control totals" placeholder="100" name="totales[]" value="${roundNumber(
          0,
          2
        )}"></td>
        <td class="text-center"><button type="button" class="deletefile2 btn btn-white"><i class="far fa-trash-alt"></i></button></td>
        </tr>
    `;
    $("#table-plans tbody").append(items);
  }
}
$(document).on("click", ".deletefile2", function () {
  $(this).parents(".item-row2").remove();
  calculateTotals(
    ".item-row2",
    ".costos",
    ".unidads",
    ".totals",
    "#subtotalserv",
    "#discountserv",
    "#totalserv",
    "#text-sub",
    "#text-dis",
    "#text-total"
  );
});
$("#table-plans,#discountserv").on("keyup change", function () {
  calculateTotals(
    ".item-row2",
    ".costos",
    ".unidads",
    ".totals",
    "#subtotalserv",
    "#discountserv",
    "#totalserv",
    "#text-sub",
    "#text-dis",
    "#text-total"
  );
});
function calculateTotals(
  itemRow,
  price,
  qtycl,
  totalcl,
  subTotal,
  discount,
  totalNeto,
  textsub,
  textdis,
  textto
) {
  //calcular total precio por cantidad
  $(itemRow).each(function (i) {
    var row = $(this);
    var total = row.find(price).val() * row.find(qtycl).val();
    total = roundNumber(total, 2);
    row.find(totalcl).val(total);
  });
  //calcular el subtotal
  var subtotal = 0;
  $(totalcl).each(function (i) {
    var total = $(this).val();
    if (!isNaN(total)) subtotal += Number(total);
  });
  subtotal = roundNumber(subtotal, 2);
  $(subTotal).val(subtotal);
  $(textsub).text(currency_symbol + subtotal);
  //calcular el total
  var grandTotal = Number($(subTotal).val()) - Number($(discount).val());
  grandTotal = roundNumber(grandTotal, 2);
  $(totalNeto).val(grandTotal);
  $(textto).text(currency_symbol + grandTotal);
  $(textdis).text(currency_symbol + roundNumber($(discount).val(), 2));
}
function exports() {
  alert_msg("loader", "Generando excel.");
  $('[data-toggle="tooltip"]').tooltip("hide");
  setTimeout(function () {
    $("#gritter-notice-wrapper").remove();
    alert_msg("success", "Se exporto excel correctamente.");
    window.open(base_url + "/bills/export_pendings", "_blank");
  }, 1000);
}
function view_bill(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/bills/view_bill/" + idbill;
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
          objData.data.bill.correlative +
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
$("#btn-a4").on("click", function () {
  var idbill = document.querySelector("#idbillvoucher").value;
  window.open(base_url + "/bills/bill_voucher/" + idbill + "/a4", "_blank");
});
$("#btn-ticket").on("click", function () {
  var idbill = document.querySelector("#idbillvoucher").value;
  window.open(base_url + "/bills/bill_voucher/" + idbill + "/ticket", "_blank");
});
$("#btn-print_ticket").on("click", function () {
  var idbill = document.querySelector("#idbillvoucher").value;
  window.open(base_url + "/bills/print_voucher/" + idbill, "_blank");
});
/* MODAL VISTA IMPRESION & PDF*/
function print_options(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/bills/view_bill/" + idbill;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#idbillvoucher").value =
          objData.data.bill.encrypt_bill;
        document.querySelector("#text-title-voucher").innerHTML =
          objData.data.bill.voucher + " Nº " + objData.data.bill.correlative;
        document.querySelector("#text_country").innerHTML =
          "+" + objData.data.business.country_code;
        document.querySelector("#country_code").value =
          objData.data.business.country_code;
        document.querySelector("#bill_number_client").value =
          objData.data.bill.mobile;
        document.querySelector("#msg").value = objData.data.message_wsp;
        $("#modal-voucher").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function make_payment(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions_payments").parsley().reset();
  document.querySelector("#text-button-payment").innerHTML = "Guardar Pago";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/bills/select_record/" + idbill;
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
        document.querySelector("#idbill").value = objData.data.encrypt_bill;
        document.querySelector("#idclient").value = objData.data.encrypt_client;
        document.querySelector("#total_payment").value =
          objData.data.remaining_amount;
        $("#total_payment").trigger("touchspin.updatesettings", {
          max: objData.data.remaining_amount,
        });
        $("#date_time").val(moment().format("DD/MM/YYYY H:mm"));
        if (user_profile == 1) {
          document.querySelector("#date_time").readOnly = false;
        } else {
          document.querySelector("#date_time").readOnly = true;
        }
        list_runway();
        $("#modal-payment").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}

function cancel(idbill, invoice) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ANULAR COMPROBANTE " + invoice,
    content: "Esta seguro de anular este comprobante.",
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
          var ajaxUrl = base_url + "/bills/cancel";
          let formData = new FormData();
          formData.append("idbill", idbill);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
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
/* ENVIAR CORREO */
function send_email(idbill, idclient, type) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  loading.style.display = "flex";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl =
    base_url + "/bills/send_email/" + idbill + "/" + idclient + "/" + type;
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
/* EDITAR */
function update(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/bills/view_bill/" + idbill;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#text-facser").innerHTML = "Actualizar Factura";
        document.querySelector("#text-button-facser").innerHTML =
          "Guardar Cambios";
        document.querySelector("#idfacser").value =
          objData.data.bill.encrypt_bill;
        document.querySelector("#cont-client-temp-serv").style.display =
          "block";
        document.querySelector("#idclient_temp").value =
          objData.data.bill.names + " " + objData.data.bill.surnames;
        document.querySelector("#idclient_temp").readOnly = true;
        document.querySelector("#cont-client-serv").style.display = "none";
        document.querySelector("#client_serv").value =
          objData.data.bill.encrypt_client;
        $("#client_serv").select2();
        document.querySelector("#servissue").value = moment(
          objData.data.bill.date_issue
        ).format("DD/MM/YYYY");
        document.querySelector("#servexpiration").value = moment(
          objData.data.bill.expiration_date
        ).format("DD/MM/YYYY");
        document.querySelector("#billed_month").value = "";
        document.querySelector("#statusserv").value = objData.data.bill.state;
        localStorage.setItem("state_current", objData.data.bill.state);
        if (objData.data.bill.state == 1) {
          document.querySelector("#statusserv").options[0].disabled = false;
          document.querySelector("#statusserv").options[1].disabled = false;
          document.querySelector("#statusserv").options[2].disabled = false;
          document.querySelector("#statusserv").options[3].disabled = true;
        } else if (objData.data.bill.state == 2) {
          document.querySelector("#statusserv").options[0].disabled = true;
          document.querySelector("#statusserv").options[1].disabled = false;
          document.querySelector("#statusserv").options[2].disabled = false;
          document.querySelector("#statusserv").options[3].disabled = true;
        } else if (objData.data.bill.state == 3) {
          document.querySelector("#statusserv").options[0].disabled = true;
          document.querySelector("#statusserv").options[1].disabled = false;
          document.querySelector("#statusserv").options[2].disabled = false;
          document.querySelector("#statusserv").options[3].disabled = true;
        } else if (objData.data.bill.state == 4) {
          document.querySelector("#statusserv").options[0].disabled = true;
          document.querySelector("#statusserv").options[1].disabled = false;
          document.querySelector("#statusserv").options[2].disabled = false;
          document.querySelector("#statusserv").options[3].disabled = false;
        }
        $("#statusserv").select2({ minimumResultsForSearch: -1 });
        document.querySelector("#vouchersserv").value =
          objData.data.bill.encrypt_voucher;
        $("#vouchersserv").select2();
        serie_serv(objData.data.bill.encrypt_voucher);
        document.querySelector("#serieserv").value =
          objData.data.bill.encrypt_serie;
        document.querySelector("#observ").value = objData.data.bill.observation;
        document.querySelector("#subtotalserv").value =
          objData.data.bill.subtotal;
        document.querySelector("#discountserv").value =
          objData.data.bill.discount;
        document.querySelector("#totalserv").value = objData.data.bill.total;
        document.querySelector("#text-sub").innerHTML =
          objData.data.bill.subtotal;
        document.querySelector("#text-dis").innerHTML =
          objData.data.bill.discount;
        document.querySelector("#text-total").innerHTML =
          objData.data.bill.total;
        var template = "";
        if (objData.data.detail.length === 0) {
          template += `
                <tr>
                  <td class="text-center" colspan="4">No hay registros.</td>
                </tr>
                `;
        } else {
          objData.data.detail.forEach((detail) => {
            if (detail.type == 1) {
              template += `
                            <tr class="item-row2">
                              <td><input type="hidden" name="idproducto[]" value="${
                                detail.serproid
                              }"><input type="hidden" name="tipo[]" value="${
                detail.type
              }"><input class="form-control text-uppercase" placeholder="Descripción" name="descripcion[]" value="${
                detail.description
              }"></td>
                              <td><input type="text" class="form-control costos" placeholder="100" name="costo[]" value="${roundNumber(
                                detail.price,
                                2
                              )}"></td>
                              <td><input type="text" class="form-control unidads" placeholder="1" min="1" name="unidad[]" value="${
                                detail.quantity
                              }" readonly></td>
                              <td><input type="text" class="form-control totals" placeholder="100" name="totales[]" value="${roundNumber(
                                detail.total,
                                2
                              )}"></td>
                              <td class="text-center"><button type="button" class="deletefile2 btn btn-white"><i class="far fa-trash-alt"></i></button></td>
                            </tr>
                        `;
            } else {
              template += `
                            <tr class="item-row2">
                              <td><input type="hidden" name="idproducto[]" value="${
                                detail.serproid
                              }"><input type="hidden" name="tipo[]" value="${
                detail.type
              }"><input class="form-control text-uppercase" placeholder="Descripción" name="descripcion[]" value="${
                detail.description
              }"></td>
                              <td><input type="text" class="form-control costos" placeholder="100" name="costo[]" value="${roundNumber(
                                detail.price,
                                2
                              )}"></td>
                              <td><input type="text" class="form-control unidads" placeholder="1" min="1" name="unidad[]" value="${
                                detail.quantity
                              }" readonly></td>
                              <td><input type="text" class="form-control totals" placeholder="100" name="totales[]" value="${roundNumber(
                                detail.total,
                                2
                              )}"></td>
                              <td></td>
                            </tr>
                        `;
            }
          });
        }
        $("#table-plans tbody").html(template);
        document
          .querySelector("#cont-iss-serv")
          .setAttribute("class", "col-md-2 form-group");
        document
          .querySelector("#cont-exp-serv")
          .setAttribute("class", "col-md-2 form-group");
        document.querySelector("#cont-state-serv").style.display = "block";
        $("#modal-facser").modal("show");
      } else {
        alert_msg("error", "Error al obtener los datos.");
      }
    }
  };
}
/* PAGOS */
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

$("#whatsapp-massive").on("click", function () {
  loading.style.display = "flex";
  const deuda = $("#deuda_mensual").val() || "";
  axios
    .get(`${base_url}/customers/list_info_message?deuda=${deuda}`)
    .then(async ({ data }) => {
      const errors = [];
      const success = [];
      let services = [];
      // añadir servicios
      data.forEach((item) => {
        if (!item) return;
        services.push(
          sendMessageWhatsapp({
            phone: item.phone,
            message: item.message,
          })
        );
      });
      // executar servicios por chunk
      const chunk = services.chunk(2);
      await chunk.forEach(async (arrayServices) => {
        await Promise.resolve(arrayServices)
          .then((values) => {
            success.push(...values);
          })
          .catch((err) => errors.push(err));
      });
      // messages
      alert_msg("success", `Se enviaron a ${success.length} correctamente!`);
      if (errors.length) {
        alert_msg("warning", `No se pudo enviar a ${errors.length} clientes!`);
      }
      loading.style.display = "none";
    })
    .catch((err) => {
      loading.style.display = "none";
      alert_msg("error", err.message);
    });
});
