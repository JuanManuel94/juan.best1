let table_pending;
let table_name_pending = "list-bills-pendings";
let bills = [];
let payments = [];
let number_client = document.querySelector("#massive_bill_number_client");

$(document).on("click", "body", function (e) {
  document.querySelector(".search-input").classList.remove("active");
  document.querySelector("#box-search").innerHTML = "";
  $("#date_compromiso")
    .datetimepicker({ locale: "es" })
    .on("dp.change", function () {
      $("#btn_pay").prop("disabled", false);
    });
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

function massive_runway() {
  if (document.querySelector("#typepay")) {
    var ajaxUrl = base_url + "/runway/list_runway";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#typepay").innerHTML = request.responseText;
        $("#typepay").select2();
      }
    };
  }
}

$("#search_client").keyup(function () {
  let search = $(this).val();
  if (search.length > 0) {
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    var ajaxUrl = base_url + "/payments/search_clients";
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

function pending_invoices(idclient) {
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/payments/pending_invoices/" + idclient;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector(".search-input").classList.remove("active");
        document.querySelector("#box-search").innerHTML = "";
        document.querySelector("#search_client").value = "";
        $("#pending_invoices").slideUp().html(objData.data.views).slideDown();
        massive_runway();
        table_pendings(objData.data.client.id);
        $("#date_time_mass").datetimepicker({ locale: "es" });
        $("#date_time_mass").val(moment().format("DD/MM/YYYY H:mm"));
        document
          .querySelector("#transactions")
          .addEventListener("submit", save_payment, false);
      } else {
        alert_msg("error", objData.msg);
        $("#pending_invoices").slideUp().empty();
        document.querySelector(".search-input").classList.remove("active");
        document.querySelector("#box-search").innerHTML = "";
        document.querySelector("#search_client").value = "";
      }
    }
  };
}

function table_pendings(idclient) {
  table_configuration("#" + table_name_pending, "Facturas pendientes");
  table_pending = $("#" + table_name_pending)
    .DataTable({
      ajax: {
        url: " " + base_url + "/payments/list_pendings/" + idclient,
        dataSrc: "",
      },
      stripeClasses: ["stripe1", "stripe2"],
      deferRender: true,
      rowId: "id",
      columns: [
        { data: "invoice", className: "text-center" },
        { data: "billing" },
        {
          data: "balance",
          render: function (data, type, full, meta) {
            return (state =
              '<span class="text-danger f-w-700">' + data + "</span>");
          },
          className: "text-center",
        },
        { data: "total", className: "text-center" },
        {
          data: "date_issue",
          render: function (data, type, full, meta) {
            return moment(data).format("DD/MM/YYYY");
          },
          className: "text-center",
        },
        {
          data: "expiration_date",
          render: function (data, type, full, meta) {
            return moment(data).format("DD/MM/YYYY");
          },
          className: "text-center",
        },
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
      ],
      initComplete: function (oSettings, json) {
        $("#" + table_name_pending + "_wrapper .dt-buttons").append(
          $("#" + table_name_pending + "-btns-exportable").contents()
        );
      },
    })
    .on("processing.dt", function (e, settings, processing) {
      if (processing) {
        loaderin(".panel-bills-pendings");
      } else {
        loaderout(".panel-bills-pendings");
      }
    })
    .on("draw", function () {});
}

function save_payment(e) {
  e.preventDefault();
  let total = parseInt(document.querySelector("#total_pay").value);
  if (total < 0) {
    alert_msg("error", "El monto a pagar debe ser mayor a 0");
    return false;
  }
  loading.style.display = "flex";

  const transactions = document.querySelector("#transactions");
  const formData = new FormData(transactions);
  formData.set("remaining_amount", formatDecimal($("#remaining_amount").val()));
  formData.set("total_pay", formatDecimal($("#total_pay").val()));
  formData.set("total_discount", formatDecimal($("#total_discount").val()));

  axios
    .post(`${base_url}/payments/mass_payments`, formData)
    .then(({ data }) => {
      if (data.status == "success") {
        alert_msg("success", data.msg);
        $("#pending_invoices").slideUp().empty();
        $("#modal-massive-voucher").modal("show");
        document.querySelector("#text-title-massive-voucher").innerHTML =
          "Opciones de impresión";
        document.querySelector(
          "#massive_text_country"
        ).innerHTML = `+${data.country}`;
        document.querySelector("#massive_country_code").value = data.country;
        document.querySelector("#massive_bill_number_client").value =
          data.mobile;
        document.querySelector("#massive_client").value = data.client;
        document.querySelector("#massive_current_paid").value =
          data.current_paid;
        bills = data.bills;
        payments = data.arrayPaymentId;
      } else if ((data.status = "warning")) {
        alert_msg("warning", data.msg);
      } else {
        alert_msg("error", data.msg);
      }
    })
    .catch((err) => {
      console.log(err);
      alert_msg("error", "No se pudo generar el pago");
    })
    .finally(() => (loading.style.display = "none"));
}

function calcTotal() {
  const total = parseFloat(formatDecimal($("#total").val()));
  const discount = parseFloat(formatDecimal($("#total_discount").val()));
  return total - discount;
}

function changeDiscount(evt) {
  const discount = parseFloat(formatDecimal($("#total_discount").val() || "0"));
  const total = $("#total").val();
  const neto = total - discount;

  if (neto <= 0) {
    $("#total_discount").val(formatMoney(0));
    $("#total_pay").val(formatMoney(total));
    $("#remaining_amount").val(formatMoney(calcTotal()));
    alert_msg("warning", "El descuento debe ser menor a la deuda");
  } else {
    $("#remaining_amount").val(formatMoney(neto));
    $("#total_pay").val(formatMoney(neto));
    $("#total_discount").val(formatMoney(discount));
  }
}

function changePay(evt) {
  const btn = $("#btn_pay");
  const inputPay = $("#total_pay");
  const inputDiscount = $("#total_discount");
  const inputDeuda = $("#remaining_amount");
  const deuda = parseFloat(formatDecimal(inputDeuda.val()));
  const current = parseFloat(formatDecimal(inputPay.val()));
  if (deuda < current) {
    alert_msg("warning", "El monto supera la deuda.");
    inputPay.val(formatMoney(deuda));
  } else if (current < 0) {
    alert_msg("error", "El monto no puede ser menor a cero");
    inputPay.val(formatMoney(deuda));
  } else if (current == 0) {
    inputPay.val(formatMoney(current));
    inputDiscount.val(formatMoney(0));
    inputDiscount.prop("disabled", true);
    inputDeuda.val(formatMoney(calcTotal()));
    btn.prop("disabled", true);
  } else {
    btn.prop("disabled", false);
    inputDiscount.prop("disabled", false);
    inputPay.val(formatMoney(current));
  }
}

/* FORMAS DE IMPRESION */
$("#btn-massive-ticket").on("click", function () {
  redirect_by_post(base_url + "/payments/massive_pdfs", bills, true);
});

$("#btn-massive-print_ticket").on("click", function () {
  redirect_by_post(base_url + "/payments/massive_impressions", bills, true);
});

function redirect_by_post(route, array, in_new_tab) {
  array = typeof array == "undefined" ? {} : array;
  in_new_tab = typeof in_new_tab == "undefined" ? true : in_new_tab;
  var form = document.createElement("form");
  $(form)
    .attr("id", "transactions")
    .attr("name", "transactions")
    .attr("action", route)
    .attr("method", "post")
    .attr("enctype", "multipart/form-data");
  if (in_new_tab) {
    $(form).attr("target", "_blank");
  }
  array.forEach((bill) => {
    $(form).append('<input type="text" name="ids[]" value="' + bill + '"/>');
  });
  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
  return false;
}

$(".btn-close").on("click", function () {
  bills = [];
  payments = [];
});
/* MSJ whatsapp */
$("#btn-massive-msg").on("click", function () {
  let cell = document.querySelector("#massive_bill_number_client").value;
  if (cell !== "") {
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    var ajaxUrl = base_url + "/payments/massive_msj";
    var formData = new FormData();
    formData.append("ids", payments);
    request.open("POST", ajaxUrl, true);
    request.send(formData);
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        var objData = JSON.parse(request.responseText);
        if (objData.status == "success") {
          const country = document.querySelector("#massive_country_code").value;
          const phone = document.querySelector(
            "#massive_bill_number_client"
          ).value;
          const numberPhone = `${country}${phone}`;
          // validar numero
          if (!phone) return alert_msg("info", "El número es obligatorio.");
          const wspApi = getWhatsappApi();
          if (wspApi) {
            sendMessageWhatsapp({
              phone: numberPhone,
              message: objData.message,
            })
              .then(() => alert_msg("success", "Mensaje enviado"))
              .catch(() => alert_msg("error", "No se pudo enviar el mensaje"));
          } else {
            const url = `https://api.whatsapp.com/send/?phone=${numberPhone}&text=${objData.message}`;
            window.open(url);
          }
        } else {
          alert_msg("error", objData.msg);
        }
      }
    };
  } else {
    alert_msg("info", "El número es obligatorio.");
  }
});

function onMoreBill() {
  document.querySelector("#text-title").innerHTML = "Generar Facturas";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#bill-transactions").reset();
  $("#bill-transactions").parsley().reset();
  $("#bill-modal-action").modal("show");
  const component = document.getElementById("bill-transactions");

  voucher_service();
  find_bills();

  component.onsubmit = (e) => {
    e.preventDefault();
    generateBills();
  };
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
        $("#vouchersserv").select2({ width: "100%" }).trigger("change");
      }
    };

    $("#vouchersserv").on("click change", function () {
      listSeries($(this).val());
    });
  }
}

function listSeries(idvoucher) {
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

function find_bills() {
  const tmpClient = document.getElementById("clientData");
  const client = JSON.parse(tmpClient.innerText);
  axios
    .get(`${base_url}/customers/select_invoice/${client.clientIdEncrypt}`)
    .then(({ data }) => {
      if (data.status !== "success") {
        throw new Error(data.msg);
      }

      const invoice = data.data.invoice;
      const [dia, mes, anio] = invoice.billed_month.split("/");
      const date = `${anio}-${mes.padStart(2, "0")}-${dia.padStart(2, "0")}`;
      $("#fecha").val(date);
      $("#months").val(1);
    })
    .catch((err) => {
      alert_msg(
        "error",
        err.message || "No se pudo obtener los datos de factura"
      );
    });
}

function generateBills() {
  const tmpClient = document.getElementById("clientData");
  const client = JSON.parse(tmpClient.innerText);
  const form = new FormData(document.getElementById("bill-transactions"));
  form.set("clientId", client.id);
  form.set("date", $("#fecha").val());

  Swal.fire({
    title: "Cargando...",
    html: "Por favor espera",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  axios
    .post(`${base_url}/payments/generate_bills`, form)
    .then(() => {
      $("#bill-modal-action").modal("hide");
      alert_msg("success", "Facturas generadas");

      Swal.fire({
        title: "Recargando datos...",
        html: "Por favor espera",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      setTimeout(() => {
        Promise.all([
          pending_invoices(client.clientIdEncrypt),
          table_pendings(client.id),
        ]).finally(() => Swal.close());
      }, 500);
    })
    .catch((err) => {
      alert_msg(
        "warning",
        err?.response?.data?.message || "No se pudo generar los recibos"
      );
    })
    .finally(() => Swal.close());
}
