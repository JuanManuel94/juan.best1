let table, table_client;
let table_name = "list";
let number_client = document.querySelector("#bill_number_client");
const filebtn = document.querySelector("#import_bills");
const filetext = document.querySelector("#text-file");
var type = document.querySelector("#listTypes");
var footer =
  '<table  class="table table-bordered text-center">' +
  '<thead class="table-dark"><tr><th>ESTADO</th><th>CANTIDAD</th><th>TOTAL</th><th>COBRADO</th></tr></thead><tbody>' +
  '<tr><td class="paid_detail f-s-12">PAGADAS</td>' +
  '<td class="paid_amount f-s-12">0</td>' +
  '<td class="paid_total f-s-12">0</td><td class="paid_subtotal f-s-12">0</td></tr>' +
  '<tr><td class="unpaid_detail f-s-12">PENDIENTES</td>' +
  '<td class="unpaid_amount f-s-12">0</td>' +
  '<td class="unpaid_total f-s-12">0</td><td class="unpaid_subtotal f-s-12">0</td></tr>' +
  '<tr><td class="overdue_detail f-s-12">VENCIDAS</td>' +
  '<td class="expired_amount f-s-12">0</td>' +
  '<td class="expired_total f-s-12">0</td><td class="expired_subtotal f-s-12">0</td></tr>' +
  '<tr><td class="canceled_detail f-s-12">ANULADAS</td>' +
  '<td class="canceled_amount f-s-12">0</td>' +
  '<td class="canceled_total f-s-12">0</td><td class="canceled_subtotal f-s-12">0</td></tr>' +
  '<tr class="table-active"><td class="totales_detail f-w-600 f-s-12">TOTALES</td>' +
  '<td class="total_amount f-w-600 f-s-12">0</td>' +
  '<td class="total_total f-w-600 f-s-12">0</td><td class="total_subtotal f-w-600 f-s-12">0</td></tr>' +
  "</tbody></table>";
document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name, "Lista de facturas");
    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url:
            " " +
            base_url +
            "/bills/list_records?start=" +
            $("#start").val() +
            "&end=" +
            $("#end").val() +
            "&state=" +
            $("#listStates").val(),
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
          { data: "zona" },
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
          { data: "payment_date" },
          { data: "waytopay" },
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
              if (data == 1) {
                state = '<span class="label label-success">PAGADO</span>';
              }
              if (data == 2) {
                state = '<span class="label label-warning">PENDIENTE</span>';
              }
              if (data == 3) {
                state = '<span class="label label-danger">VENCIDO</span>';
              }
              if (data == 4) {
                state = '<span class="label label-dark">ANULADO</span>';
              }
              return state;
            },
            className: "text-center",
          },
          { data: "options", className: "text-center", aWidth: "40px" },
        ],
        drawCallback: function (settings) {
          var paid_amount = 0,
            paid_subtotal = 0,
            paid_total = 0;
          var unpaid_amount = 0,
            unpaid_subtotal = 0,
            unpaid_total = 0;
          var expired_amount = 0,
            expired_subtotal = 0,
            expired_total = 0;
          var canceled_amount = 0,
            canceled_subtotal = 0,
            canceled_total = 0;
          var api = this.api().rows({ search: "applied" }).data();
          $.each(api, function (key, value) {
            switch (value["count_state"]) {
              case "PAGADO":
                paid_amount++;
                paid_subtotal += parseFloat(value["count_subtotal"]);
                paid_total += parseFloat(value["count_total"]);
                break;
              case "PENDIENTE":
                unpaid_amount++;
                unpaid_subtotal += parseFloat(value["count_subtotal"]);
                unpaid_total += parseFloat(value["count_total"]);
                break;
              case "VENCIDO":
                expired_amount++;
                expired_subtotal += parseFloat(value["count_subtotal"]);
                expired_total += parseFloat(value["count_total"]);
                break;
              case "ANULADO":
                canceled_amount++;
                canceled_subtotal += parseFloat(value["count_subtotal"]);
                canceled_total += parseFloat(value["count_total"]);
                break;
            }
          });
          var total_amount =
            paid_amount + unpaid_amount + expired_amount + canceled_amount;
          var total_subtotal =
            paid_subtotal +
            unpaid_subtotal +
            expired_subtotal +
            canceled_subtotal;
          var total_total =
            paid_total + unpaid_total + expired_total + canceled_total;
          $(".invoice_summary").html(footer);
          $(".paid_amount").html(formatMoney(paid_amount));
          $(".paid_subtotal").html(
            currency_symbol + formatMoney(paid_subtotal)
          );
          $(".paid_total").html(currency_symbol + formatMoney(paid_total));
          $(".unpaid_amount").html(formatMoney(unpaid_amount));
          $(".unpaid_subtotal").html(
            currency_symbol + formatMoney(unpaid_subtotal)
          );
          $(".unpaid_total").html(currency_symbol + formatMoney(unpaid_total));
          $(".expired_amount").html(formatMoney(expired_amount));
          $(".expired_subtotal").html(
            currency_symbol + formatMoney(expired_subtotal)
          );
          $(".expired_total").html(
            currency_symbol + formatMoney(expired_total)
          );
          $(".canceled_amount").html(formatMoney(canceled_amount));
          $(".canceled_subtotal").html(
            currency_symbol + formatMoney(canceled_subtotal)
          );
          $(".canceled_total").html(
            currency_symbol + formatMoney(canceled_total)
          );
          $(".total_amount").html(formatMoney(total_amount));
          $(".total_subtotal").html(
            currency_symbol + formatMoney(total_subtotal)
          );
          $(".total_total").html(currency_symbol + formatMoney(total_total));

          $("#list tbody tr td:nth-child(14):has(div a.promise-on)")
            .parent()
            .find(".label-danger")
            .text("VENCIDO (PROMESA)");
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
          loaderin(".panel-bills");
        } else {
          loaderout(".panel-bills");
        }
      });

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
    if (document.querySelector("#transactions_import")) {
      var transactions_import = document.querySelector("#transactions_import");
      transactions_import.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_import").parsley().isValid()) {
          if ($("#import_bills").get(0).files.length == 0) {
            alert_msg(
              "info",
              "Selecionar un archivo excel para realizar el proceso."
            );
            return false;
          } else {
            var allowed_extensions = [".xls", ".xlsx"];
            var file = $("#import_bills");
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
            var ajaxUrl = base_url + "/bills/import";
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
    if (document.querySelector("#transactions_free")) {
      var transactions_free = document.querySelector("#transactions_free");
      transactions_free.onsubmit = function (e) {
        e.preventDefault();
        let client = document.querySelector("#client_free").value;
        let tablefree = document.querySelector("#table-free");
        let filas = tablefree.getElementsByTagName("tbody")[0];
        if (filas.children.length === 0) {
          alert_msg(
            "error",
            "No hay productos, agregué uno o mas para poder realizar la venta."
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
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/bills/action_bill";
        var formData = new FormData(transactions_free);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              transactions_free.reset();
              refresh_table();
              $("#modal-free").modal("hide");
              if (objData.modal) {
                $("#modal-free").on("hidden.bs.modal", function () {
                  $("#modal-voucher").modal("show");
                  alert_msg("success", objData.msg);
                  document.querySelector("#idbillvoucher").value =
                    objData.idbill;
                  document.querySelector("#text-title-voucher").innerHTML =
                    objData.voucher + " Nº " + objData.correlative;
                  document.querySelector("#text_country").innerHTML =
                    "+" + objData.country;
                  document.querySelector("#country_code").value =
                    objData.country;
                  document.querySelector("#bill_number_client").value =
                    objData.mobile;
                  var url =
                    base_url +
                    "/invoice/document/" +
                    objData.idbill +
                    "/ticket";
                  if (objData.remaining_amount == 0) {
                    if (objData.type == 1) {
                      var msg = `Hola, se registro ${
                        objData.symbol + objData.amount_paid
                      } al ${objData.voucher.toLowerCase()} número ${
                        objData.serie
                      } del cliente ${
                        objData.client
                      }. Puede revisarlo en el siguiente enlace: ${url}. Muchas gracias por su pago, Atte. ${
                        objData.business_name
                      }`;
                    } else {
                      var dateAr = objData.billed_month.split("-");
                      var month = dateAr[1];
                      var year = dateAr[0];
                      var msg = `Hola, se registro ${
                        objData.symbol + objData.amount_paid
                      } al ${objData.voucher.toLowerCase()} número ${
                        objData.serie
                      } de ${month_letters(month).toUpperCase()} del cliente ${
                        objData.client
                      }. Puede revisarlo en el siguiente enlace: ${url}. Muchas gracias por su pago, Atte. ${
                        objData.business_name
                      }`;
                    }
                  } else {
                    if (objData.type == 1) {
                      if (objData.amount_paid == 0) {
                        var msg = `Hola, esta pendiente el pago de ${
                          objData.symbol + objData.remaining_amount
                        } del ${objData.voucher.toLowerCase()} número ${
                          objData.serie
                        } del cliente ${
                          objData.client
                        }. Puede revisarlo en el siguiente enlace: ${url}. Muchas gracias por su pago, Atte. ${
                          objData.business_name
                        }`;
                      } else {
                        var msg = `Hola, se registro ${
                          objData.symbol + objData.amount_paid
                        } al ${objData.voucher.toLowerCase()} número ${
                          objData.serie
                        }, quedando un saldo pendiente de ${
                          objData.symbol + objData.remaining_amount
                        } del cliente ${
                          objData.client
                        }. Puede revisarlo en el siguiente enlace: ${url}. Muchas gracias por su pago, Atte. ${
                          objData.business_name
                        }`;
                      }
                    } else {
                      var dateAr = objData.billed_month.split("-");
                      var month = dateAr[1];
                      var year = dateAr[0];
                      if (objData.amount_paid == 0) {
                        var msg = `Hola, esta pendiente el pago de ${
                          objData.symbol + objData.remaining_amount
                        } del ${objData.voucher.toLowerCase()} número ${
                          objData.serie
                        } de ${month_letters(
                          month
                        ).toUpperCase()} del cliente ${
                          objData.client
                        }. Puede revisarlo en el siguiente enlace: ${url}. Muchas gracias por su pago, Atte. ${
                          objData.business_name
                        }`;
                      } else {
                        var msg = `Hola, se registro ${
                          objData.symbol + objData.amount_paid
                        } al ${objData.voucher.toLowerCase()} número ${
                          objData.serie
                        } de ${month_letters(
                          month
                        ).toUpperCase()}, quedando un saldo pendiente de ${
                          objData.symbol + objData.remaining_amount
                        } del cliente ${
                          objData.client
                        }. Puede revisarlo en el siguiente enlace: ${url}. Muchas gracias por su pago, Atte. ${
                          objData.data.business.business_name
                        }`;
                      }
                    }
                  }
                  document.querySelector("#msg").value = msg;
                });
              } else {
                alert_msg("success", objData.msg);
              }
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
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
    voucher_free();
    voucher_service();
    list_clients_free();
    list_clients_contract();
    list_documents();
    $("#transactions_payments").parsley();
    $("#listStates").select2({ minimumResultsForSearch: -1 });
    $("#listMethod").select2({ minimumResultsForSearch: -1 });
    $("#date_time").datetimepicker({ locale: "es" });
    $("#period")
      .datetimepicker({
        locale: "es",
        format: "MM/YYYY",
      })
      .on("dp.change", function (e) {
        var params = e.date.format(e.date._f);
        var current = moment().format("MM/YYYY");
        if (current !== params) {
          document.querySelector("#btn-massive").disabled = true;
        } else {
          document.querySelector("#btn-massive").disabled = false;
        }
        var formatted_date = params.split("/");
        var month = formatted_date[0];
        var year = formatted_date[1];
        var new_date = month + "-" + year;
        debt_opening(new_date);
        table_detail_opening(new_date);
      });
    $("#period").val(moment().format("MM/YYYY"));
    $("#total_payment").TouchSpin({
      min: 0.01,
      max: 10000000000,
      step: 0.01,
      decimals: 2,
      buttondown_class: "btn btn-default f-w-700",
      buttonup_class: "btn btn-default f-w-700",
    });
    $(
      "#start,#end,#freeissue,#freeexpiration,#servissue,#servexpiration"
    ).datetimepicker({
      locale: "es",
      format: "L",
    });
    $("#start").val(moment().startOf("month").format("DD/MM/YYYY"));
    $("#end").val(moment().endOf("month").format("DD/MM/YYYY"));
    $("#freeissue,#freeexpiration,#servissue,#servexpiration").val(
      moment().format("DD/MM/YYYY")
    );
    $("#date_time").val(moment().format("DD/MM/YYYY H:mm"));
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
function list_documents() {
  const id = $("#currentDocumentId").val() || "";
  axios
    .get(`${base_url}/providers/list_documents?&isjson=true&selected=${id}`)
    .then(({ data }) => {
      const defaultId = $("#currentDocumentId").val();
      const component = document.querySelector("#listTypes");
      const arrayHtml = [];
      data.forEach((item) => {
        const opt = document.createElement("option");
        opt.value = item.id;
        opt.text = item.document;
        if (defaultId === item.id) opt.selected = true;
        arrayHtml.push(opt.outerHTML);
      });
      $("#documentData").val(JSON.stringify(data));
      component.innerHTML = arrayHtml.join("");
      const numberDocument = $("#document").val();
      if (defaultId) $("#listTypes").val(defaultId).trigger("change");
      $("#document").val(numberDocument);
      checkDocument();
    })
    .catch(console.log);
}

$("#listTypes").on("change", function () {
  $("#doc_search").val("").focus();
  type_document($(this).val());
});
function type_document(value) {
  switch (value) {
    case "2":
      document.querySelector("#doc_search").setAttribute("maxlength", "8");
      document
        .querySelector("#doc_search")
        .setAttribute("placeholder", "99999999");
      break;
    case "3":
      document.querySelector("#doc_search").setAttribute("maxlength", "11");
      document
        .querySelector("#doc_search")
        .setAttribute("placeholder", "99999999999");
      break;
  }
}
function list_clients_free() {
  if (document.querySelector("#client_free")) {
    var ajaxUrl = base_url + "/bills/list_clients_free";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#client_free").innerHTML = request.responseText;
        $("#client_free").select2();
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
$("#client_serv").on("click change", function () {
  select_client_contract($(this).val());
});
function voucher_free() {
  if (document.querySelector("#vouchersfree")) {
    var ajaxUrl = base_url + "/vouchers/list_vouchers";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#vouchersfree").innerHTML =
          request.responseText;
        $("#vouchersfree").select2();
      }
    };
  }
}
$("#vouchersfree").on("click change", function () {
  serie_free($(this).val());
});
function serie_free(idvoucher) {
  if (document.querySelector("#seriefree")) {
    var ajaxUrl = base_url + "/vouchers/series_vocuhers/" + idvoucher;
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#seriefree").innerHTML = request.responseText;
        $("#seriefree").select2();
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
        $("#vouchersserv").select2({ width: "100%" });
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
        $("#serieserv").select2({ width: "100%" });
      }
    };
  }
}
$("#btn-search").on("click", function () {
  table.ajax
    .url(
      base_url +
        "/bills/list_records?start=" +
        $("#start").val() +
        "&end=" +
        $("#end").val() +
        "&state=" +
        $("#listStates").val()
    )
    .load();
  //table.columns.adjust().responsive.recalc();
});
$("#import_bills").on("change", function () {
  document.querySelector("#text-file").value = this.files.item(0).name;
});
filetext.addEventListener("click", function () {
  filebtn.click();
});
function bill_services() {
  document.querySelector("#text-facser").innerHTML = "Nueva Factura Servicio";
  document.querySelector("#text-button-facser").innerHTML = "Generar Factura";
  document.querySelector("#idfacser").value = "";
  document.querySelector("#billed_month").value = "";
  document.querySelector("#transactions_facser").reset();
  $("#servissue,#servexpiration").val(moment().format("DD/MM/YYYY"));
  localStorage.setItem("state_current", "");
  list_clients_contract();
  voucher_service();
  serie_serv(vouchersserv.value);
  $("#table-plans tbody").html("");
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
  document
    .querySelector("#cont-iss-serv")
    .setAttribute("class", "col-md-3 form-group");
  document
    .querySelector("#cont-exp-serv")
    .setAttribute("class", "col-md-3 form-group");
  document.querySelector("#cont-state-serv").style.display = "none";
  document.querySelector("#cont-client-serv").style.display = "block";
  document.querySelector("#cont-client-temp-serv").style.display = "none";
  document.querySelector("#idclient_temp").value = "";
  $("#modal-facser").modal("show");
}
function select_client_contract(idclient) {
  if (idclient == "") {
    var current = new Date();
    var day = current.getDate();
    var month = current.getMonth() + 1;
    var fullYear = current.getFullYear();
    var date = day + "/" + month + "/" + fullYear;
    document.querySelector("#servexpiration").value = date;
    document.querySelector("#billed_month").value = "";
    document.querySelector("#discountserv").value = 0;
    $("#table-plans tbody").html("");
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
  } else {
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    var ajaxUrl = base_url + "/bills/select_client_contract/" + idclient;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        var objData = JSON.parse(request.responseText);
        if (objData.status == "success") {
          if (objData.data.service) {
            if (objData.data.invoice.status == 4) {
              alert_msg("info", "El servicio está cancelado.");
            } else if (objData.data.invoice.status == 5) {
              alert_msg("info", "El servicio es gratis.");
            } else if (objData.data.invoice.status == 3) {
              alert_msg("info", "El servicio está suspendido.");
            } else if (objData.data.invoice.status == 1) {
              alert_msg("info", "El servicio no completo la instalación.");
            } else {
              document.querySelector("#servexpiration").value =
                objData.data.invoice.expiration;
              document.querySelector("#billed_month").value =
                objData.data.invoice.billed_month;
              document.querySelector("#discountserv").value =
                objData.data.invoice.discount;
              var template = "";
              objData.data.detail.forEach((detail) => {
                template += `
                                <tr class="item-row2">
                                <td><input type="hidden" name="idproducto[]" value="${
                                  detail.id
                                }"><input type="hidden" name="tipo[]" value="2"><input class="form-control text-uppercase" placeholder="Descripción" name="descripcion[]" value="${
                  detail.service
                }"></td>
                                <td><input type="text" class="form-control costos" placeholder="100" name="costo[]" value="${roundNumber(
                                  detail.price,
                                  2
                                )}"></td>
                                <td><input type="text" class="form-control unidads" placeholder="1" min="1" name="unidad[]" value="1" readonly></td>
                                <td><input type="text" class="form-control totals" placeholder="100" name="totales[]" value="${roundNumber(
                                  detail.price,
                                  2
                                )}"></td>
                                <td></td>
                                </tr>
                            `;
              });
              $("#table-plans tbody").html(template);
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
            }
          } else {
            alert_msg("error", "El cliente no cuenta con servicios.");
          }
        } else {
          alert_msg("error", objData.msg);
        }
      }
    };
  }
}
function bill_free() {
  document.querySelector("#text-free").innerHTML = "Nueva Factura";
  document.querySelector("#text-button-free").innerHTML = "Generar Factura";
  document.querySelector("#idfree").value = "";
  document.querySelector("#transactions_free").reset();
  $("#freeissue,#freeexpiration").val(moment().format("DD/MM/YYYY"));
  list_clients_free();
  voucher_free();
  serie_free(vouchersfree.value);
  $("#listMethod").select2({ minimumResultsForSearch: -1 });
  document
    .querySelector("#cont-iss-free")
    .setAttribute("class", "col-md-3 form-group");
  document
    .querySelector("#cont-exp-free")
    .setAttribute("class", "col-md-3 form-group");
  document.querySelector("#cont-state-free").style.display = "none";
  document.querySelector(".search-input").classList.remove("active");
  document.querySelector("#box-search").innerHTML = "";
  document.querySelector("#search_products").value = "";
  $(".deletefile").click();
  $("#modal-free").modal("show");
}
$("#search_products").keyup(function () {
  let search = $(this).val();
  if (search.length > 0) {
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    var ajaxUrl = base_url + "/products/search_products";
    var strData = "search=" + search;
    request.open("POST", ajaxUrl, true);
    request.setRequestHeader(
      "Content-type",
      "application/x-www-form-urlencoded"
    );
    request.send(strData);
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector(".search-input").classList.add("active");
        document.querySelector("#box-search").innerHTML = request.responseText;
      }
    };
  } else {
    document.querySelector(".search-input").classList.remove("active");
    document.querySelector("#box-search").innerHTML = "";
  }
});
function add_product(id, product, code, price, stock) {
  var previous_code = new Array();
  var rows = 0;
  $("#table-free tr").each(function (n) {
    if (n > 0) {
      var cell;
      $(this)
        .children("td")
        .each(function (index) {
          switch (index) {
            case 0:
              cell = $(this).text();
              if (cell != undefined || cell != "") {
                previous_code.push(cell);
                //console.log(previous_code);
              }
              break;
            case 1:
              break;
            case 2:
              break;
            case 3:
              break;
            case 4:
              break;
            case 5:
              break;
          }
        });
      rows = rows + 1;
    }
  });
  var quantity = 1;
  if (id !== "") {
    if (stock >= 1) {
      var subtotal = quantity * price;
      var items =
        '<tr class="item-row">' +
        '<td class="text-center" style="line-height:30px">' +
        code +
        "</td>" +
        '<td><input type="hidden" name="idproducto[]" value="' +
        id +
        '"><input type="hidden" name="tipo[]" value="1"><input class="form-control text-uppercase" placeholder="Descripción" name="descripcion[]" value="' +
        product +
        '"></td>' +
        '<td><input type="text" class="form-control costo" placeholder="100" name="costo[]" value="' +
        roundNumber(price, 2) +
        '"></td>' +
        '<td><input type="number" class="form-control unidad" placeholder="1" min="1" max="' +
        stock +
        '" onchange="validate_stock(' +
        id +
        "," +
        stock +
        ');" id="quantity_temp-' +
        id +
        '" name="unidad[]" value="' +
        quantity +
        '"></td>' +
        '<td><input type="text" class="form-control total" placeholder="100" name="totales[]" value="' +
        roundNumber(subtotal, 2) +
        '"></td>' +
        '<td class="text-center"><button type="button" class="deletefile btn btn-white"><i class="far fa-trash-alt"></i></button></td>' +
        "</tr>";
      var existence = false;
      var position = 0;
      $.each(previous_code, function (i, product_code) {
        if (code == product_code) {
          existence = true;
          position = i;
        }
      });
      if (existence == false) {
        $("#table-free tbody").append(items);
        calculateTotals(
          ".item-row",
          ".costo",
          ".unidad",
          ".total",
          "#subtotalfree",
          "#discountfree",
          "#totalfree",
          "#text-sub-f",
          "#text-dis-f",
          "#text-total-f"
        );
        document.querySelector(".search-input").classList.remove("active");
        document.querySelector("#box-search").innerHTML = "";
        document.querySelector("#search_products").value = "";
      } else if (existence == true) {
        position = position + 1;
        modify_quantity(id, position, stock);
        document.querySelector(".search-input").classList.remove("active");
        document.querySelector("#box-search").innerHTML = "";
        document.querySelector("#search_products").value = "";
      }
    } else {
      alert_msg("warning", "El producto no tiene stock disponible.");
    }
  } else {
    alert_msg("error", "El producto no existe elige otro.");
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
function modify_quantity(id, rowid, stock) {
  var previous_amount = $("#table-free tr:nth-child(" + rowid + ")")
    .find("td:eq(3)")
    .find(".unidad")
    .val();
  if (stock > previous_amount) {
    var new_quantity = parseInt(previous_amount) + 1;
    $("#table-free tr:nth-child(" + rowid + ")")
      .find("td:eq(3)")
      .find(".unidad")
      .val(new_quantity);
    calculateTotals(
      ".item-row",
      ".costo",
      ".unidad",
      ".total",
      "#subtotalfree",
      "#discountfree",
      "#totalfree",
      "#text-sub-f",
      "#text-dis-f",
      "#text-total-f"
    );
  }
  if (stock <= previous_amount) {
    alert_msg("warning", "Ha sobrepasado el stock.");
    document.getElementById("quantity_temp-" + id).value = 1;
    calculateTotals(
      ".item-row",
      ".costo",
      ".unidad",
      ".total",
      "#subtotalfree",
      "#discountfree",
      "#totalfree",
      "#text-sub-f",
      "#text-dis-f",
      "#text-total-f"
    );
  }
  if (previous_amount <= 0) {
    alert_msg("error", "No puede ser negativo.");
    document.getElementById("quantity_temp-" + id).value = 1;
    calculateTotals(
      ".item-row",
      ".costo",
      ".unidad",
      ".total",
      "#subtotalfree",
      "#discountfree",
      "#totalfree",
      "#text-sub-f",
      "#text-dis-f",
      "#text-total-f"
    );
  }
}
$(document).on("click", ".deletefile", function () {
  $(this).parents(".item-row").remove();
  calculateTotals(
    ".item-row",
    ".costo",
    ".unidad",
    ".total",
    "#subtotalfree",
    "#discountfree",
    "#totalfree",
    "#text-sub-f",
    "#text-dis-f",
    "#text-total-f"
  );
});
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
$("#table-free,#discountfree").on("keyup change", function () {
  calculateTotals(
    ".item-row",
    ".costo",
    ".unidad",
    ".total",
    "#subtotalfree",
    "#discountfree",
    "#totalfree",
    "#text-sub-f",
    "#text-dis-f",
    "#text-total-f"
  );
});
function validate_stock(id, stock) {
  var quantity = $("#quantity_temp-" + id).val();
  if (stock < quantity) {
    alert_msg("warning", "La cantidad supera el stock.");
    document.querySelector("#quantity_temp-" + id).value = 1;
    calculateTotals(
      ".item-row",
      ".costo",
      ".unidad",
      ".total",
      "#subtotalfree",
      "#discountfree",
      "#totalfree",
      "#text-sub-f",
      "#text-dis-f",
      "#text-total-f"
    );
  }
  if (quantity <= 0) {
    alert_msg("error", "No puede ser negativo.");
    document.querySelector("#quantity_temp-" + id).value = 1;
    calculateTotals(
      ".item-row",
      ".costo",
      ".unidad",
      ".total",
      "#subtotalfree",
      "#discountfree",
      "#totalfree",
      "#text-sub-f",
      "#text-dis-f",
      "#text-total-f"
    );
  }
}
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
/* MODA VISTA FACTURA */
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
                          </tr>
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
                            </tr>
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
/* FORMAS DE IMRPESION */
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
/* CANCELAR */
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
/* VISTA PAGOS */
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
        list_runway();
        $("#modal-payment").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function make_promise(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions_promise").parsley().reset();

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
          "#transactions_promise"
        );
        transactions_payments.reset();
        document.querySelector("#text-promise").innerHTML =
          "Agregar Promesa de Pago al " +
          objData.data.voucher +
          " Nº " +
          objData.data.invoice;
        document.querySelector("#idbillpromise").value =
          objData.data.encrypt_bill;
        document.querySelector("#idclientpromise").value =
          objData.data.encrypt_client;
        document.querySelector("#date_promise").value =
          objData.data.promise_date ??
          moment().add(1, "days").format("YYYY-MM-DD");
        document.querySelector("#comment_promise").value =
          objData.data.promise_comment ?? "";

        if (objData.data.promise_enabled == 1) {
          $("#promiseEnabled").show();
        } else {
          $("#promiseEnabled").hide();
        }
        $("#date_promise").attr(
          "min",
          moment().add(1, "days").format("YYYY-MM-DD")
        );
        $("#date_promise").attr(
          "max",
          moment().add(30, "days").format("YYYY-MM-DD")
        );
        list_runway();
        $("#modal-promise").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
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
/* FACTURAS MASIVAS */
function modal_debtOpening() {
  $('[data-toggle="tooltip"]').tooltip("hide");
  document.querySelector("#btn-massive").innerHTML =
    '<i class="fas fa-save mr-2"></i>Emitir Facturas';
  debt_opening(moment().format("MM-YYYY"));
  table_detail_opening(moment().format("MM-YYYY"));
  document.querySelector("#text-title-massive").innerHTML =
    "Facturar servicios de " +
    month_letters(moment().format("MM")) +
    ", " +
    moment().format("YYYY");
  $("#modal-massive").modal("show");
}
function debt_opening(params) {
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/bills/debt_opening/" + params;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        var dateAr = params.split("-");
        var month = dateAr[0];
        var year = dateAr[1];
        document.querySelector("#text-title-massive").innerHTML =
          "Facturar servicios de " + month_letters(month) + ", " + year;
        document.querySelector("#total_clients").innerHTML =
          "(" + objData.data.total_clients + ")";
        document.querySelector("#total_collection").innerHTML =
          "(" + objData.data.issued_invoices + ")";
      } else {
        var dateAr = params.split("-");
        var month = dateAr[0];
        var year = dateAr[1];
        document.querySelector("#text-title-massive").innerHTML =
          "Facturar servicios de " + month_letters(month) + ", " + year;
        document.querySelector("#total_clients").innerHTML = "(0)";
        document.querySelector("#total_collection").innerHTML = "(0)";
        alert_msg("error", objData.msg);
      }
    }
  };
}
function table_detail_opening(params) {
  var dateAr = params.split("-");
  var month = dateAr[0];
  var year = dateAr[1];
  table_configuration(
    "#list-client",
    "Facturar servicios de " + month_letters(month) + ", " + year
  );
  table_client = $("#list-client")
    .DataTable({
      ajax: {
        url: " " + base_url + "/bills/detail_opening/" + params,
        dataSrc: "",
      },
      deferRender: true,
      idDataTables: "1",
      columns: [
        { data: "n", className: "text-center", aWidth: "10px" },
        { data: "client" },
        { data: "services" },
        { data: "total", className: "text-center", aWidth: "20px" },
      ],
    })
    .on("processing.dt", function (e, settings, processing) {
      if (processing) {
        loaderin(".panel-months");
      } else {
        loaderout(".panel-months");
      }
    });
}

$("#btn-massive").click(function () {
  let period = document.querySelector("#period").value;
  if (period !== "") {
    loading.style.display = "flex";
    let request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    let ajaxUrl = base_url + "/bills/mass_registration";
    let formData = new FormData();
    formData.append("period", period);
    request.open("POST", ajaxUrl, true);
    request.send(formData);
    request.onreadystatechange = function () {
      if (request.readyState != 4) return;
      if (request.status == 200) {
        console.log(request);
        let objData = JSON.parse(request.responseText);
        if (objData.status == "success") {
          $("#modal-massive").modal("hide");
          alert_msg("success", objData.msg);
          refresh_table();
        } else if (objData.status == "warning") {
          $("#modal-massive").modal("hide");
          alert_msg("warning", objData.msg);
        } else {
          alert_msg("error", objData.msg);
        }
      }
      loading.style.display = "none";
      return false;
    };
  } else {
    alert_msg("error", "El mes tiene que estar definido.");
  }
});
/* IMPORTAR Y EXPORTAR */
function modal_import() {
  $('[data-toggle="tooltip"]').tooltip("hide");
  document.querySelector("#text-title-import").innerHTML =
    "Importar Facturas - Servicios";
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
    window.open(base_url + "/bills/export", "_blank");
  }, 1000);
}
