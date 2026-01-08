document.write(
  `<script src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=${key_google}"></script>`
);

const getTabNetwork = () => {
  return $(`[data-view="network"]`);
};

let table_ticket,
  table_internet,
  table_personalized,
  table_transactions,
  table_bills;
let table_name_ticket = "list-ticket",
  table_name_inter = "list-internet",
  table_name_person = "list-personalized",
  table_name_bills = "list-bills",
  table_name_trans = "list-transactions";
var client = document.querySelector("#idclients");
var contract = document.querySelector("#idcontract");
var vouchersfree = document.querySelector("#vouchersfree");
var vouchersserv = document.querySelector("#vouchersserv");
var list_int = document.querySelector("#listInternet");
var list_perz = document.querySelector("#listPersonalized");
var plan = document.querySelector("#listPlan");
let btnadd = document.querySelector(".btn-add");
let number_client = document.querySelector("#bill_number_client");
var marker, map;

document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name_ticket, "Lista de tickets");
    table_ticket = $("#" + table_name_ticket).DataTable({
      ajax: {
        url: " " + base_url + "/customers/list_ticket/" + client.value,
        dataSrc: "",
      },
      deferRender: true,
      idDataTables: "1",
      columns: [
        { data: "id", className: "text-center" },
        { data: "incident" },
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
        { data: "assigned" },
        {
          data: "state",
          render: function (data, type, full, meta) {
            var state = "";
            if (data == 1) {
              state = '<span class="label label-success">RESUELTO</span>';
            }
            if (data == 2) {
              state = '<span class="label label-warning">PENDIENTE</span>';
            }
            if (data == 3) {
              state = '<span class="label label-primary">EN PROCESO</span>';
            }
            if (data == 4) {
              state = '<span class="label label-secondary">NO RESUELTO</span>';
            }
            if (data == 5) {
              state = '<span class="label label-danger">VENCIDO</span>';
            }
            if (data == 6) {
              state = '<span class="label label-dark">CANCELADO</span>';
            }
            return state;
          },
          className: "text-center",
        },
        { data: "options", className: "text-center", sWidth: "40px" },
      ],
      initComplete: function (oSettings, json) {
        $("#" + table_name_ticket + "_wrapper div.container-options").append(
          $("#" + table_name_ticket + "-btns-tools").contents()
        );
      },
    });
    table_configuration("#" + table_name_inter, "Lista de servicios");
    table_internet = $("#" + table_name_inter).DataTable({
      ajax: {
        url: " " + base_url + "/customers/list_internet/" + contract.value,
        dataSrc: "",
      },
      deferRender: true,
      idDataTables: "1",
      columns: [
        { data: "internal_code", className: "text-center" },
        { data: "service" },
        { data: "price" },
        { data: "max_rise" },
        { data: "max_descent" },
        {
          data: "registration_date",
          render: function (data, type, full, meta) {
            return moment(data).format("DD/MM/YYYY");
          },
        },
        {
          data: "state",
          render: function (data, type, full, meta) {
            var state = "";
            if (data == 1) {
              state = '<span class="label label-success">ACTIVO</span>';
            }
            if (data == 2) {
              state = '<span class="label label-dark">SUSPENDIDO</span>';
            }
            if (data == 3) {
              state = '<span class="label label-danger">CANCELADO</span>';
            }
            return state;
          },
          className: "text-center",
        },
        { data: "options", className: "text-center", sWidth: "40px" },
      ],
      initComplete: function (oSettings, json) {
        $("#" + table_name_inter + "_wrapper div.container-options").append(
          $("#" + table_name_inter + "-btns-tools").contents()
        );
      },
    });
    table_configuration("#" + table_name_person, "Lista de servicios");
    table_personalized = $("#" + table_name_person).DataTable({
      ajax: {
        url: " " + base_url + "/customers/list_personalized/" + contract.value,
        dataSrc: "",
      },
      deferRender: true,
      idDataTables: "1",
      columns: [
        { data: "internal_code", className: "text-center" },
        { data: "service" },
        { data: "price" },
        {
          data: "registration_date",
          render: function (data, type, full, meta) {
            return moment(data).format("DD/MM/YYYY");
          },
        },
        {
          data: "state",
          render: function (data, type, full, meta) {
            var state = "";
            if (data == 1) {
              state = '<span class="label label-success">ACTIVO</span>';
            }
            if (data == 2) {
              state = '<span class="label label-dark">SUSPENDIDO</span>';
            }
            if (data == 3) {
              state = '<span class="label label-danger">CANCELADO</span>';
            }
            return state;
          },
          className: "text-center",
        },
        { data: "options", className: "text-center", sWidth: "40px" },
      ],
      initComplete: function (oSettings, json) {
        $("#" + table_name_person + "_wrapper div.container-options").append(
          $("#" + table_name_person + "-btns-tools").contents()
        );
      },
    });
    table_configuration("#" + table_name_trans, "Lista de transacciones");
    table_transactions = $("#" + table_name_trans).DataTable({
      ajax: {
        url: " " + base_url + "/customers/list_payments/" + client.value,
        dataSrc: "",
      },
      deferRender: true,
      idDataTables: "1",
      columns: [
        { data: "internal_code", className: "text-center" },
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
        { data: "amount", className: "text-center" },
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
        { data: "options", className: "text-center", sWidth: "40px" },
      ],
    });
    table_configuration("#" + table_name_bills, "Lista de facturas");
    table_bills = $("#" + table_name_bills).DataTable({
      ajax: {
        url: " " + base_url + "/customers/list_bills/" + client.value,
        dataSrc: "",
      },
      deferRender: true,
      idDataTables: "1",
      columns: [
        { data: "invoice", className: "text-center" },
        { data: "billing" },
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
          visible: false,
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
        { data: "observation", visible: false },
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
      initComplete: function (oSettings, json) {
        $("#" + table_name_bills + "_wrapper div.container-options").append(
          $("#" + table_name_bills + "-btns-tools").contents()
        );
      },
    });
    if (document.querySelector("#transactions_client")) {
      var transactions_client = document.querySelector("#transactions_client");
      transactions_client.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_client").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/customers/modify_client";
          var formData = new FormData(transactions_client);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                alert_msg("success", objData.msg);
              } else if (objData.status == "exists") {
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
                table_ticket.ajax.reload(null, false);
                if (objData.modal) {
                  alert_msg("success", objData.msg);
                  $("#modal-message").modal("show");
                  document.querySelector("#text-title-message").innerHTML =
                    "Ticket Nº " + objData.code;
                  document.querySelector("#message_text_country").innerHTML =
                    "+" + objData.country_code;
                  document.querySelector("#message_country_code").value =
                    objData.country_code;
                  document.querySelector("#message_number_client").value =
                    objData.mobile;
                  document.querySelector("#idpdfticket").value =
                    objData.encrypt;
                  var msg = `Buen dia, se genero el ticket Nº ${objData.code}, al cliente ${objData.client}, el tecnico se estara comunicando para solucionar el inconveniente con su servicio. Muchas gracias, Atte. ${objData.business}`;
                  document.querySelector("#message_msg").value = msg;
                } else {
                  alert_msg("success", objData.msg);
                }
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
    if (document.querySelector("#transactions_internet")) {
      var transactions_internet = document.querySelector(
        "#transactions_internet"
      );
      transactions_internet.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_internet").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/customers/action_service";
          var formData = new FormData(transactions_internet);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $("#modal-internet").modal("hide");
                transactions_internet.reset();
                alert_msg("success", objData.msg);
                table_internet.ajax.reload(null, false);
              } else if (objData.status == "exists") {
                $("#modal-internet").modal("hide");
                transactions_internet.reset();
                alert_msg("info", objData.msg);
                table_internet.ajax.reload(null, false);
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
    if (document.querySelector("#transactions_personalized")) {
      var transactions_personalized = document.querySelector(
        "#transactions_personalized"
      );
      transactions_personalized.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_personalized").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/customers/action_service";
          var formData = new FormData(transactions_personalized);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $("#modal-personalized").modal("hide");
                transactions_personalized.reset();
                alert_msg("success", objData.msg);
                table_personalized.ajax.reload(null, false);
              } else if (objData.status == "exists") {
                $("#modal-personalized").modal("hide");
                transactions_personalized.reset();
                alert_msg("info", objData.msg);
                table_personalized.ajax.reload(null, false);
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
    if (document.querySelector("#transactions_contract")) {
      var transactions_contract = document.querySelector(
        "#transactions_contract"
      );
      transactions_contract.onsubmit = function (e) {
        e.preventDefault();
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/customers/modify_contract";
        var formData = new FormData(transactions_contract);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              alert_msg("success", objData.msg);
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_free")) {
      var transactions_free = document.querySelector("#transactions_free");
      transactions_free.onsubmit = function (e) {
        e.preventDefault();
        let tablefree = document.querySelector("#table-free");
        let filas = tablefree.getElementsByTagName("tbody")[0];
        if (filas.children.length === 0) {
          alert_msg(
            "error",
            "No hay productos, agregué uno o mas para poder realizar la venta."
          );
          return false;
        }
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/customers/action_bill";
        var formData = new FormData(transactions_free);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              transactions_free.reset();
              table_bills.ajax.reload(null, false);
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
                  var url = base_url + "/invoice/document/" + objData.idbill;
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
        var ajaxUrl = base_url + "/customers/action_bill";
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
              table_bills.ajax.reload(null, false);
              $("#modal-facser").modal("hide");
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
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
          var ajaxUrl = base_url + "/customers/action_payment";
          var formData = new FormData(transactions_payments);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                transactions_payments.reset();
                table_bills.ajax.reload(null, false);
                table_transactions.ajax.reload(null, false);
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
                    var url = base_url + "/invoice/document/" + objData.idbill;
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
                        var msg = `Hola, se registro ${
                          objData.symbol + objData.amount_paid
                        } al ${objData.voucher.toLowerCase()} número ${
                          objData.serie
                        } de ${month_letters(
                          month
                        ).toUpperCase()} del cliente ${
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
                            objData.business_name
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
        }
      };
    }
    if (document.querySelector("#transactions_finalize")) {
      var transactions_finalize = document.querySelector(
        "#transactions_finalize"
      );
      transactions_finalize.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_finalize").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/customers/complete_ticket";
          var formData = new FormData(transactions_finalize);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $("#modal-finalize").modal("hide");
                transactions_finalize.reset();
                table_ticket.ajax.reload(null, false);
                alert_msg("success", objData.msg);
              } else if (objData.status == "info") {
                $("#modal-finalize").modal("hide");
                transactions_finalize.reset();
                table_ticket.ajax.reload(null, false);
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
    list_internet();
    list_personalized();
    showDiscount();
    list_runway();
    type_plan(plan.value);
    voucher_free();
    voucher_service();
    $("#transactions_client").parsley();
    $("#transactions_ticket").parsley();
    $("#transactions_internet").parsley();
    $("#transactions_personalized").parsley();
    $("#transactions_free").parsley();
    $("#transactions_payments").parsley();
    $("#transactions_finalize").parsley();
    $("#listMethod").select2({ minimumResultsForSearch: -1 });
    $("#listPriority").select2({ minimumResultsForSearch: -1 });
    $("#listServices").select2();
    $("#listTypePay").select2();
    $("#date_time,#attention_date,#scheduled_date").datetimepicker({
      locale: "es",
    });
    $("#image-user").initial({
      height: 45,
      width: 45,
      charCount: 2,
      fontSize: 21,
      fontWeight: 600,
    });
    $("#freeissue,#freeexpiration,#servissue,#servexpiration").datetimepicker({
      locale: "es",
      format: "L",
    });
    show_images(client.value);
    uploadImage();
    $("#total_payment").TouchSpin({
      min: 0.01,
      max: 10000000000,
      step: 0.01,
      decimals: 2,
      buttondown_class: "btn btn-default f-w-700",
      buttonup_class: "btn btn-default f-w-700",
    });
    if (document.querySelector(".btn-add")) {
      btnadd.onclick = function (e) {
        let key = Date.now();
        let newElement = document.createElement("div");
        newElement.id = "div" + key;
        newElement.classList.add("item-image");
        newElement.innerHTML += `
                  <div class="content-image text-center">
                    <div class="container-image"></div>
                    <div class="tools-image">
                      <a href="" class="btn btn-inverse btn-view-image m-b-5" data-lightbox="example-set"><i class="fas fa-image mr-1"></i>Ver imagen</a>
                      <button type="button" class="btn btn-info m-b-5 btn-download"><i class="fa fa-download mr-1"></i>Descargar</button>
                      <button type="button" class="btn btn-danger m-b-5 btn-delete notblock" onclick="removeImage('#div${key}')"><i class="fa fa-trash-alt mr-1"></i>Eliminar</button>
                      <input type="file" name="photo" id="img${key}" key="${key}" class="upload-file">
                      <label for="img${key}" class="btn btn-success m-b-5 btn-upload"><i class="fas fa-upload mr-1"></i>Elegir imagen</label>
                    </div>
                  </div>`;
        document.querySelector("#gallery").appendChild(newElement);
        document.querySelector("#div" + key + " .btn-upload").click();
        uploadImage();
      };
    }
    $("#freeissue,#freeexpiration,#servissue").val(
      moment().format("DD/MM/YYYY")
    );
    $("#attention_date,#date_time").val(moment().format("DD/MM/YYYY H:mm"));
  },
  false
);

document.querySelector("#radio_yes").addEventListener("click", function (e) {
  $("#cont-attention").hide("fast");
});

document.querySelector("#radio_not").addEventListener("click", function (e) {
  $("#cont-attention").hide("fast");
});

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
        $("#listTypePay").select2({ width: "100%" });
      }
    };
  }
}

function search_document() {
  var type = document.querySelector("#listTypes").value;
  var doc = document.querySelector("#document").value;
  if (doc != "") {
    $(".btn-search").html('<i class="fa fa-spinner fa-sm fa-spin"></i>');
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    var ajaxUrl = base_url + "/customers/search_document/" + type + "/" + doc;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        var objData = JSON.parse(request.responseText);
        if (objData.status == "success") {
          document.querySelector("#names").value = objData.data.names;
          document.querySelector("#surnames").value = objData.data.surnames;
          document.querySelector("#address").value = objData.data.address;
        } else if (objData.status == "info") {
          alert_msg("info", objData.msg);
          document.querySelector("#document").value = "";
          document.querySelector("#names").value = "";
          document.querySelector("#surnames").value = "";
          document.querySelector("#address").value = "";
        } else {
          alert_msg("error", objData.msg);
          document.querySelector("#document").value = "";
          document.querySelector("#names").value = "";
          document.querySelector("#surnames").value = "";
          document.querySelector("#address").value = "";
        }
      }
      $(".btn-search").html('<i class="fa fa-search"></i>');
      return false;
    };
  } else {
    if (type == 2) {
      alert_msg("error", "Ingrese el numero de dni.");
    }
    if (type == 3) {
      alert_msg("error", "Ingrese el numero de ruc.");
    }
    if (type == 4) {
      alert_msg("error", "Ingrese el numero de carnet de extranjeria.");
    }
  }
}
/* NAV */
$(document).on("click", ".nav-ajax [data-toggle=tab]", function (e) {
  var $this = $(this),
    loadurl = $this.attr("data-view");
  if (loadurl !== "") {
    table_internet.ajax.reload(null, false);
    table_personalized.ajax.reload(null, false);
    table_bills.ajax.reload(null, false);
    table_transactions.ajax.reload(null, false);
    table_ticket.ajax.reload(null, false);
    table_transactions.ajax.reload(null, false);
  } else {
  }
});
/* MODULO CLIENTES Y CONTRATOS */
function get_location(position) {
  var coordinates = position.coords;
  $("#latitud").val(coordinates.latitude);
  $("#longitud").val(coordinates.longitude);
  $(".btn-coordinates").html('<i class="fas fa-location-arrow"></i>');
  $('[data-toggle="tooltip"]').tooltip("hide");
}

function location_error(error) {
  alert_msg(
    "warning",
    "No se pudo obtener su ubicaciòn actual (" + error.message + ")"
  );
  $(".btn-coordinates").html('<i class="fas fa-location-arrow"></i>');
  $('[data-toggle="tooltip"]').tooltip("hide");
}

function current_location() {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $(".btn-coordinates").html('<i class="fa fa-spinner fa-xs fa-spin"></i>');
  var options = {
    enableHighAccuracy: true,
    timeout: 15000,
    maximumAge: 0,
  };
  navigator.geolocation.getCurrentPosition(
    get_location,
    location_error,
    options
  );
}

function open_map() {
  document.querySelector("#text-map").innerHTML = "Google Maps";
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#modal-map").modal("show");
  initMap();
}

function initMap() {
  latLng = new google.maps.LatLng(-8.381723950980284, -74.54314678745268);
  map = new google.maps.Map(document.getElementById("locations"), {
    zoom: 16,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
    },
  });
  var icon = base_url + "/Assets/images/default/client.png";
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
            position.coords.latitude,
            position.coords.longitude
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
      content:
        "<h5 class='text-center f-w-600 mb-0'>Ubicación del cliente</h5>",
    });
    infowindow.open(map, marker);

    google.maps.event.addListener(marker, "dragend", function () {
      updateMarkerPosition(marker.getPosition());
    });
  }
}

function updateMarkerPosition(latLng) {
  $("#latitud").val(latLng.lat());
  $("#longitud").val(latLng.lng());
}

$("#listTypes").on("change", function () {
  $("#document").focus();
  type_document($(this).val());
});

function type_document(value) {
  switch (value) {
    case "2":
      document.querySelector("#document").setAttribute("maxlength", "8");
      document
        .querySelector("#document")
        .setAttribute("placeholder", "99999999");
      break;
    case "3":
      document.querySelector("#document").setAttribute("maxlength", "11");
      document
        .querySelector("#document")
        .setAttribute("placeholder", "99999999999");
      break;
  }
}

$("#listPlan").on("click change", function () {
  type_plan($(this).val());
});

function type_plan(value) {
  switch (value) {
    case "1":
      $(".cont-day").show("fast");
      $(".cont-create").show("fast");
      $(".cont-gracia").show("fast");
      $(".cont-chk").show("fast");
      break;
    case "2":
      $(".cont-day").show("fast");
      $(".cont-create").show("fast");
      $(".cont-gracia").show("fast");
      $(".cont-chk").show("fast");
      break;
    case "3":
      $(".cont-day").show("fast");
      $(".cont-create").show("fast");
      $(".cont-gracia").show("fast");
      $(".cont-chk").show("fast");
      break;
    case "4":
      $(".cont-day").show("fast");
      $(".cont-create").show("fast");
      $(".cont-gracia").show("fast");
      $(".cont-chk").show("fast");
      break;
    case "5":
      $(".cont-day").hide("fast");
      $(".cont-create").hide("fast");
      $(".cont-gracia").hide("fast");
      $(".cont-chk").hide("fast");
      check = document.querySelector("#chkDiscount");
      if (check.checked) {
        $(".cont-dis").hide("fast");
        $(".cont-month").hide("fast");
        check.checked = false;
      } else {
        $(".cont-dis").hide("fast");
        $(".cont-month").hide("fast");
      }
      break;
  }
}

function showDiscount() {
  check = document.querySelector("#chkDiscount");
  if (check.checked) {
    $(".cont-dis").show("fast");
    $(".cont-month").show("fast");
  } else {
    $(".cont-dis").hide("fast");
    $(".cont-month").hide("fast");
  }
}

$("#chkDiscount").on("click", function () {
  showDiscount();
});
/* MODULO FACTURAS */
function bill_free() {
  document.querySelector("#text-free").innerHTML = "Nueva Factura";
  document.querySelector("#text-button-free").innerHTML = "Generar Factura";
  document.querySelector("#idfree").value = "";
  document.querySelector("#transactions_free").reset();
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_client/" + client.value;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#idclifree").value = objData.data.encrypt_id;
        document.querySelector("#client_free").value =
          objData.data.names + " " + objData.data.surnames;
        $("#freeissue,#freeexpiration").val(moment().format("DD/MM/YYYY"));
        voucher_free();
        serie_free(vouchersfree.value);
        $("#listMethod").select2({ minimumResultsForSearch: -1 });
        document.querySelector(".search-input").classList.remove("active");
        document.querySelector("#box-search").innerHTML = "";
        document.querySelector("#search_products").value = "";
        $(".deletefile").click();
        $("#modal-free").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}

function bill_services() {
  document.querySelector("#text-facser").innerHTML = "Nueva Factura Servicio";
  document.querySelector("#text-button-facser").innerHTML = "Generar Factura";
  document.querySelector("#idfacser").value = "";
  document.querySelector("#transactions_facser").reset();
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_invoice/" + client.value;
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
            document
              .querySelector("#cont-iss-serv")
              .setAttribute("class", "col-md-3 form-group");
            document
              .querySelector("#cont-exp-serv")
              .setAttribute("class", "col-md-3 form-group");
            document.querySelector("#cont-state-serv").style.display = "none";
            localStorage.setItem("state_current", "");
            document.querySelector("#idcliser").value =
              objData.data.invoice.encrypt_client;
            document.querySelector("#client_serv").value =
              objData.data.invoice.client;
            document.querySelector("#servexpiration").value =
              objData.data.invoice.expiration;
            document.querySelector("#billed_month").value =
              objData.data.invoice.billed_month;
            document.querySelector("#discountserv").value =
              objData.data.invoice.discount;
            $("#servissue").val(objData.data.invoice.isset);
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
            voucher_service();
            serie_serv(vouchersserv.value);
            $(".deletefile2").click();
            $("#modal-facser").modal("show");
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

function make_payment(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions_payments").parsley().reset();
  document.querySelector("#text-button-payment").innerHTML = "Guardar Pago";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_bill/" + idbill;
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
        document.querySelector("#text-alert").innerHTML = "";
        document.querySelector("#idpayment").value = "";
        document.querySelector("#idbillpayment").value =
          objData.data.encrypt_bill;
        document.querySelector("#idbillclient").value =
          objData.data.encrypt_client;
        document.querySelector("#total_payment").value =
          objData.data.remaining_amount;
        $("#date_time").val(moment().format("DD/MM/YYYY H:mm"));
        $("#total_payment").trigger("touchspin.updatesettings", {
          max: objData.data.remaining_amount,
        });
        document.querySelector("#total_payment").readOnly = false;
        list_runway();
        $("#modal-payment").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
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
            case 6:
              break;
            case 7:
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
        '<td><input type="text" class="form-control total" placeholder="100"  name="totales[]" value="' +
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
  if (quantity <= 0) {
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

function cancel_invoice(idbill, invoice) {
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
          var ajaxUrl = base_url + "/customers/cancel_bill";
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
                table_bills.ajax.reload(null, false);
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

function view_bill(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/view_bill/" + idbill;
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
  window.open(base_url + "/customers/bill_voucher/" + idbill + "/a4", "_blank");
});

$("#btn-ticket").on("click", function () {
  var idbill = document.querySelector("#idbillvoucher").value;
  window.open(
    base_url + "/customers/bill_voucher/" + idbill + "/ticket",
    "_blank"
  );
});

$("#btn-print_ticket").on("click", function () {
  var idbill = document.querySelector("#idbillvoucher").value;
  window.open(base_url + "/customers/print_voucher/" + idbill, "_blank");
});
/* EDITAR */
function update_invoice(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/view_bill/" + idbill;
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
        document.querySelector("#idcliser").value =
          objData.data.bill.encrypt_client;
        document.querySelector("#client_serv").value =
          objData.data.bill.names + " " + objData.data.bill.surnames;
        document.querySelector("#client_serv").readOnly = true;
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
        alert_msg("error", objData.msg);
      }
    }
  };
}
/* MODAL VISTA IMPRESION & PDF*/
function print_options(idbill) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/view_bill/" + idbill;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status) {
        document.querySelector("#text-title-voucher").innerHTML =
          objData.data.bill.voucher + " Nº " + objData.data.bill.correlative;
        document.querySelector("#idbillvoucher").value =
          objData.data.bill.encrypt_bill;
        document.querySelector("#text_country").innerHTML =
          "+" + objData.data.business.country_code;
        document.querySelector("#country_code").value =
          objData.data.business.country_code;
        document.querySelector("#bill_number_client").value =
          objData.data.bill.mobile;
        var url =
          base_url + "/invoice/document/" + objData.data.bill.encrypt_bill;
        document.querySelector("#msg").value = objData.data.message_wsp;
        $("#modal-voucher").modal("show");
      } else {
        alert_msg("error", "Error al obtener los datos.");
      }
    }
  };
}

function send_email(idbill, idclient, type) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  loading.style.display = "flex";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl =
    base_url + "/customers/send_email/" + idbill + "/" + idclient + "/" + type;
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
        $("#vouchersfree").select2({ width: "100%" });
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
        $("#seriefree").select2({ width: "100%" });
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
/* MODULO PAGOS Y BALANCE */
function cancel_payment(idpayment) {
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
          var ajaxUrl = base_url + "/customers/cancel_payment";
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
                table_bills.ajax.reload(null, false);
                table_transactions.ajax.reload(null, false);
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

function update_payment(idpayment) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions_payments").parsley().reset();
  document.querySelector("#text-button-payment").innerHTML = "Guardar Cambios";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_payment/" + idpayment;
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
        document.querySelector("#idbillpayment").value =
          objData.data.encrypt_bill;
        document.querySelector("#date_time").value = objData.data.date;
        document.querySelector("#idbillclient").value =
          objData.data.encrypt_client;
        document.querySelector("#total_payment").value =
          objData.data.amount_paid;
        $("#total_payment").trigger("touchspin.updatesettings", {
          max: objData.data.amount_paid,
        });
        document.querySelector("#total_payment").readOnly = true;
        document.querySelector("#comment").value = objData.data.comment;
        document.querySelector("#listTypePay").value = objData.data.paytypeid;
        $("#listTypePay").select2({ width: "100%" });
        $("#modal-payment").modal("show");
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
/* MODULO TICKETS */
function add_ticket() {
  document.querySelector("#text-ticket").innerHTML = "Nuevo Ticket";
  document.querySelector("#text-button-ticket").innerHTML = "Guardar Registro";
  document.querySelector("#idticket").value = "";
  document.querySelector("#transactions_ticket").reset();
  $("#transactions_ticket").parsley().reset();
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_client/" + client.value;
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

function update_ticket(idticket) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions_ticket").parsley().reset();
  document.querySelector("#text-ticket").innerHTML = "Actualizar Ticket";
  document.querySelector("#text-button-ticket").innerHTML = "Guardar Cambios";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_ticket/" + idticket;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#idticket").value = objData.data.encrypt;
        document.querySelector("#idticketclient").value =
          objData.data.encrypt_client;
        document.querySelector("#client_ticket").value =
          objData.data.names + " " + objData.data.surnames;
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
        $("#modal-ticket").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}

function finalize_ticket(idticket) {
  document.querySelector("#text-button-finalize").innerHTML = "Guardar Cambios";
  document.querySelector("#transactions_finalize").reset();
  $("#transactions_finalize").parsley().reset();
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/finalize/" + idticket;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        $('[data-toggle="tooltip"]').tooltip("hide");
        document.querySelector("#text-finalize").innerHTML =
          "Finalizar Ticket #" + objData.data.code;
        document.querySelector("#idticketfinalize").value =
          objData.data.encrypt_ticket;
        $("#modal-finalize").modal("show");
        table_ticket.ajax.reload(null, false);
      } else if (objData.status == "info") {
        $('[data-toggle="tooltip"]').tooltip("hide");
        table_ticket.ajax.reload(null, false);
        alert_msg("info", objData.msg);
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}

function cancel_ticket(idticket) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "CANCELAR",
    content: "Esta seguro de cancelar este ticket.",
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
          var ajaxUrl = base_url + "/customers/cancel_ticket";
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
                table_ticket.ajax.reload(null, false);
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

function view_ticket(idticket) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/view_ticket/" + idticket;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#text-view-ticket").innerHTML =
          "Ticket Nº " + objData.data.ticket.code;
        document.querySelector("#view-client-ticket").innerHTML =
          objData.data.ticket.client;
        document.querySelector("#view-celdoc-ticket").innerHTML =
          "<b>" +
          objData.data.ticket.type_doc +
          ":</b> " +
          objData.data.ticket.document +
          " <b>CEL:</b> " +
          objData.data.ticket.mobile;
        document.querySelector("#view-address-ticket").innerHTML =
          objData.data.ticket.address;
        document.querySelector("#view-image-ticket").src = generateAvatar(
          objData.data.ticket.client
        );
        document.querySelector("#view-created-ticket").innerHTML =
          "Creado " +
          moment(objData.data.ticket.registration_date).format(
            "DD/MM/YYYY H:mm"
          );
        document.querySelector("#view-visit-ticket").innerHTML =
          "Programado " +
          moment(objData.data.ticket.attention_date).format("DD/MM/YYYY H:mm");
        var star = "";
        if (objData.data.ticket.priority == 1) {
          document.querySelector("#view-priority-ticket").innerHTML =
            '<span style="color:#00acac;border: 1px solid #00acac;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">BAJA</span>';
          document.querySelector("#view-star").innerHTML =
            '<i class="fas fa-star"></i>';
        } else if (objData.data.ticket.priority == 2) {
          document.querySelector("#view-priority-ticket").innerHTML =
            '<span style="color:#4da1ff;border: 1px solid #4da1ff;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">MEDIA</span>';
          document.querySelector("#view-star").innerHTML =
            '<i class="fas fa-star"></i><i class="fas fa-star"></i>';
        } else if (objData.data.ticket.priority == 3) {
          document.querySelector("#view-priority-ticket").innerHTML =
            '<span style="color:#F59C1A;border: 1px solid #F59C1A;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">ALTA</span>';
          document.querySelector("#view-star").innerHTML =
            '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';
        } else if (objData.data.ticket.priority == 4) {
          document.querySelector("#view-priority-ticket").innerHTML =
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
        var data_detail = "";
        if (objData.data.detail.length === 0) {
          data_detail += `
                 <li class="comment">NO HAY RESPUESTAS</li>`;
        } else {
          objData.data.detail.forEach((detail) => {
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
        $("#post-comment").html(data_detail);
        $("#modal-view-ticket").modal("show");
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
  var ajaxUrl = base_url + "/customers/select_ticket/" + idticket;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#text-title-message").innerHTML =
          "Ticket Nº " + objData.data.code;
        document.querySelector("#message_text_country").innerHTML =
          "+" + objData.data.country_code;
        document.querySelector("#message_country_code").value =
          objData.data.country_code;
        document.querySelector("#message_number_client").value =
          objData.data.mobile;
        document.querySelector("#idpdfticket").value = objData.data.encrypt;
        var msg = `Buen dia, se genero el ticket Nº ${objData.data.code}, al cliente ${objData.data.client}, el tecnico se estara comunicando para solucionar el inconveniente con su servicio. Muchas gracias, Atte. ${objData.data.business}`;
        document.querySelector("#message_msg").value = msg;
        $("#modal-message").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}

$("#btn-msg-message").on("click", function () {
  const country = document.querySelector("#message_country_code").value;
  const phone = document.querySelector("#message_number_client").value;
  const numberPhone = `${country}${phone}`;
  const message = document.querySelector("#message_msg").value;
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

$("#btn-ticket-message").on("click", function () {
  var idticket = document.querySelector("#idpdfticket").value;
  window.open(base_url + "/customers/view_pdf/" + idticket, "_blank");
});
/* MODULO DETALLE DE CONTRATOS Y SERVICIOS */
function list_internet() {
  if (document.querySelector("#listInternet")) {
    var ajaxUrl = base_url + "/internet/list_internet";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listInternet").innerHTML =
          request.responseText;
        $("#listInternet").select2({ width: "100%" });
      }
    };
  }
}

function list_personalized() {
  if (document.querySelector("#listPersonalized")) {
    var ajaxUrl = base_url + "/personalized/list_personalized";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listPersonalized").innerHTML =
          request.responseText;
        $("#listPersonalized").select2({ width: "100%" });
      }
    };
  }
}

function add_internet() {
  if (list_int.value === "") {
    alert_msg("info", "No hay servicios registrados.");
  } else {
    document.querySelector("#text-internet").innerHTML =
      "Añadir Servicio de Internet";
    document.querySelector("#text-button-internet").innerHTML =
      "Guardar Registro";
    document.querySelector("#idinternet").value = "";
    document.querySelector("#idconin").value = contract.value;
    document.querySelector("#transactions_internet").reset();
    $("#modal-internet").modal("show");
    list_internet();
    select_internet(list_int.value);
  }
}

$("#listInternet").on("click change", function () {
  select_internet($(this).val());
});

function select_internet(idservices) {
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_service/" + idservices;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status) {
        document.querySelector("#price_internet").value = objData.data.price;
        document.querySelector("#rise").value = objData.data.rise;
        document.querySelector("#descent").value = objData.data.descent;
        document.querySelector("#details_internet").value =
          objData.data.details;
      } else {
        alert_msg("info", objData.msg);
      }
    }
  };
}

function add_personalized() {
  if (list_perz.value === "") {
    alert_msg("info", "No hay servicios registrados.");
  } else {
    document.querySelector("#text-personalized").innerHTML =
      "Añadir Servicio Personalizado";
    document.querySelector("#text-button-personalized").innerHTML =
      "Guardar Registro";
    document.querySelector("#idpersonalized").value = "";
    document.querySelector("#idconper").value = contract.value;
    document.querySelector("#transactions_personalized").reset();
    $("#modal-personalized").modal("show");
    list_personalized();
    select_personalized(list_perz.value);
  }
}

$("#listPersonalized").on("click change", function () {
  select_personalized($(this).val());
});

function select_personalized(idservices) {
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_service/" + idservices;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#price_personalized").value =
          objData.data.price;
        document.querySelector("#details_personalized").value =
          objData.data.details;
      } else {
        alert_msg("info", objData.msg);
      }
    }
  };
}

function remove_detail(idservices) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ELIMINAR",
    content: "Esta seguro de eliminar este servicio.",
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
          var ajaxUrl = base_url + "/customers/remove_detail";
          var strData = "idservices=" + idservices;
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
                table_internet.ajax.reload(null, false);
                table_personalized.ajax.reload(null, false);
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

function update_service(iddetail) {
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_detail/" + iddetail;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        if (objData.data.type == 1) {
          document.querySelector("#text-internet").innerHTML =
            "Actualizar Servicio";
          document.querySelector("#text-button-internet").innerHTML =
            "Guardar Cambios";
          document.querySelector("#idinternet").value = objData.data.encrypt_id;
          document.querySelector("#idconin").value =
            objData.data.encrypt_contract;
          document.querySelector("#listInternet").value =
            objData.data.encrypt_service;
          $("#listInternet").select2();
          document.querySelector("#price_internet").value = objData.data.price;
          document.querySelector("#descent").value = objData.data.descent;
          document.querySelector("#rise").value = objData.data.rise;
          document.querySelector("#details_internet").value =
            objData.data.details;
          $("#modal-internet").modal("show");
        }
        if (objData.data.type == 2) {
          document.querySelector("#text-personalized").innerHTML =
            "Actualizar Servicio";
          document.querySelector("#text-button-personalized").innerHTML =
            "Guardar Cambios";
          document.querySelector("#idpersonalized").value =
            objData.data.encrypt_id;
          document.querySelector("#idconper").value =
            objData.data.encrypt_contract;
          document.querySelector("#listPersonalized").value =
            objData.data.encrypt_service;
          $("#listPersonalized").select2();
          document.querySelector("#price_personalized").value =
            objData.data.price;
          document.querySelector("#details_personalized").value =
            objData.data.details;
          $("#modal-personalized").modal("show");
        }
      } else {
        alert_msg("info", objData.msg);
        $("#modal-personalized").modal("hide");
      }
    }
  };
}
/* MODULO GALERIA */
function show_images(idclient) {
  let request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  let ajaxUrl = base_url + "/customers/open_gallery/" + idclient;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      let objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        let htmlImage = "";
        let objImages = objData.data;
        for (let p = 0; p < objImages.length; p++) {
          let key = Date.now() + p;
          htmlImage += `
                    <div id="div${key}" class="item-image">
                      <div class="content-image text-center">
                        <div class="container-image">
                          <img src="${objImages[p].url_image}" class="image-upload">
                        </div>
                        <div class="tools-image">
                          <a href="${objImages[p].url_image}" class="btn btn-inverse m-b-5" data-lightbox="example-set"><i class="fas fa-image mr-1"></i>Ver imagen</a>
                          <button type="button" class="btn btn-info m-b-5 btn-download" onclick="download_files('${objImages[p].url_image}','${objImages[p].image}')"><i class="fa fa-download mr-1"></i>Descargar</button>
                    `;
          if (permission_remove === "1") {
            htmlImage += `<button type="button" class="btn btn-danger m-b-5 btn-delete" onclick="removeImage('#div${key}')" image="${objImages[p].image}"><i class="fa fa-trash-alt mr-1"></i>Eliminar</button>`;
          }
          htmlImage += `
                        </div>
                      </div>
                    </div>
                    `;
        }
        document.querySelector("#gallery").innerHTML = htmlImage;
      }
    }
  };
}

function uploadImage() {
  let uploadfile = document.querySelectorAll(".upload-file");
  uploadfile.forEach(function (uploadfile) {
    uploadfile.addEventListener("change", function () {
      let parentId = this.getAttribute("key");
      let idFile = this.getAttribute("id");
      let uploadFoto = document.querySelector("#" + idFile).value;
      let fileimg = document.querySelector("#" + idFile).files;
      let prevImg = document.querySelector(
        "#div" + parentId + " .content-image .container-image"
      );
      let nav = window.URL || window.webkitURL;
      if (uploadFoto != "") {
        let type = fileimg[0].type;
        if (
          type != "image/jpeg" &&
          type != "image/jpg" &&
          type != "image/png"
        ) {
          alert_msg(
            "info",
            "¡La imagen debe estar en formato PNG, JPG o JPEG!"
          );
          prevImg.innerHTML = "Archivo no válido";
          uploadFoto.value = "";
          return false;
        } else {
          let objeto_url = nav.createObjectURL(this.files[0]);
          prevImg.innerHTML = `<img class="img-responsive image-loading" src="${base_url}/Assets/images/default/loading.gif">`;
          let request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          let ajaxUrl = base_url + "/customers/register_image";
          let formData = new FormData();
          formData.append("idclient", client.value);
          formData.append("photo", this.files[0]);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState != 4) return;
            if (request.status == 200) {
              let objData = JSON.parse(request.responseText);
              if (objData.status) {
                prevImg.innerHTML = `<img class="img-responsive image-upload" src="${objData.url_image}">`;
                document
                  .querySelector(
                    "#div" +
                      parentId +
                      " .content-image .tools-image .btn-delete"
                  )
                  .setAttribute("image", objData.image);
                if (permission_remove === "1") {
                  document
                    .querySelector(
                      "#div" +
                        parentId +
                        " .content-image .tools-image .btn-delete"
                    )
                    .classList.remove("notblock");
                }
                document
                  .querySelector(
                    "#div" +
                      parentId +
                      " .content-image .tools-image .btn-download"
                  )
                  .setAttribute(
                    "onclick",
                    "download_files('" +
                      objData.url_image +
                      "','" +
                      objData.image +
                      "')"
                  );
                document
                  .querySelector(
                    "#div" +
                      parentId +
                      " .content-image .tools-image .btn-upload"
                  )
                  .classList.add("notblock");
                document
                  .querySelector(
                    "#div" +
                      parentId +
                      " .content-image .tools-image .btn-view-image"
                  )
                  .setAttribute("href", objData.url_image);
                alert_msg("success", objData.msg);
              } else {
                alert_msg("error", objData.msg);
              }
            }
          };
        }
      }
    });
  });
}

function removeImage(element) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ELIMINAR",
    content: "Esta imagen será eliminada permanentemente.",
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
          let nameImg = document
            .querySelector(element + " .btn-delete")
            .getAttribute("image");
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/customers/remove_image";
          let formData = new FormData();
          formData.append("idclient", client.value);
          formData.append("file", nameImg);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              alsup.close();
              var objData = JSON.parse(request.responseText);
              if (objData.status) {
                alert_msg("success", objData.msg);
                let itemRemove = document.querySelector(element);
                itemRemove.parentNode.removeChild(itemRemove);
              } else {
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

function loadDataNetwork() {
  const client = JSON.parse(document.getElementById("clientData").innerText);
  const password = $("#clientNetPassword");
  getZonaNameInput().val(client.net_name);
  getRouterSelect().val(client.net_router).trigger("change");

  function loadClient(modeId) {
    const mode = findNetworkMode(modeId);
    if (!mode) return;
    getIpInput().val(client.net_ip);
    getPasswordInput().val(password.val());
    if (mode.type == "nap_client") {
      getLocalAddressInput().val(client.net_localaddress);
      getNapClientLabel().val(client.nap_cliente_nombre);
      getNapClientValue().val(client.nap_cliente_id);
    } else {
      getApClientLabel().val(client.ap_cliente_nombre);
      getApClientValue().val(client.ap_cliente_id);
    }
  }

  loadClient(getNetNameId().val());
  getNetNameId().change(function () {
    loadClient($(this).val());
  });
}

function loadConnectNetwork() {
  // remove
  $("#netBlocked .widget-list-item").removeClass("bg-success");
  $("#netBlocked .widget-list-item").removeClass("bg-warning");
  $("#netBlocked .widget-list-item").removeClass("bg-dange");
  $("#netBlocked .widget-list-media i").removeClass("icon-unlock");
  $("#netBlocked .widget-list-media i").removeClass("icon-lock");
  $("#netBlocked .widget-list-media i").removeClass("fa-unlock");
  $("#netBlocked .widget-list-media i").removeClass("fa-times");
  $("#netBlocked .widget-list-media i").removeClass("fa-lock");

  // loader
  $("#netBlocked .widget-list-media i").addClass("fa-refresh");
  $("#netBlocked .widget-list-media i").addClass("icon-refresh");
  $("#netBlocked .widget-list-item").addClass("bg-dark");

  const data = JSON.parse(document.getElementById("clientData").innerText);
  axios
    .post(`${base_url}/Customers/blocked_network/${data.id}`)
    .then(({ data }) => {
      $("#netBlocked .widget-list-media i").removeClass("fa-refresh");
      $("#netBlocked .widget-list-media i").removeClass("icon-refresh");
      $("#netBlocked .widget-list-item").removeClass("bg-dark");

      if (data.status == "success") {
        $("#netBlocked .widget-list-media i").addClass("fa-unlock");
        $("#netBlocked .widget-list-media i").addClass("icon-unlock");
        $("#netBlocked h4").text("Desbloqueado");
        $("#netBlocked .widget-list-item").addClass("bg-success");
        d;
      } else if (data.status == "disconnected") {
        $("#netBlocked .widget-list-media i").addClass("fa-times");
        $("#netBlocked .widget-list-media i").addClass("icon-times");
        $("#netBlocked h4").text("Sin conexión");
        $("#netBlocked .widget-list-item").addClass("bg-danger");
      } else if (data.status == "blocked") {
        $("#netBlocked .widget-list-media i").addClass("fa-lock");
        $("#netBlocked .widget-list-media i").addClass("icon-lock");
        $("#netBlocked h4").text("Bloqueado");
        $("#netBlocked .widget-list-item").addClass("bg-warning");
      }
    });
}

loadComponentNetwork().then(loadDataNetwork);
loadComponentIP();

$(function () {
  loadConnectNetwork();

  // refrescar
  $(document).on("click", '[data-view="abstract"]', function () {
    loadConnectNetwork();
  });
});
