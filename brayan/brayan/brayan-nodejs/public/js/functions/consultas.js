const iconRender = ({ title, icon, onclick }) => {
  return `<a href="javascript:;" 
    class="blue"
    data-toggle="tooltip"
    data-original-title="${title}"
    onclick="${onclick}"
    >
      <i class="${icon}"></i>
    </a>`;
};

const optionRender = ({ title, icon, onclick }) => {
  return `
    <a href="javascript:;" class="dropdown-item" onclick="${onclick}">
      <i class="${icon} mr-1"></i>${title}
    </a>          
  `;
};

const renderLoading = (state) => {
  $("#loading").prop("style", `display: ${state ? "flex" : "none"}`);
};

const renderTable = (data = []) =>
  datatableHelper("list", "Lista de Transacciones", {
    deferRender: true,
    idDataTables: "1",
    data,
    columns: [
      { data: "invoice", className: "text-center" },
      {
        data: "billing",
        render: function (data) {
          return data || "OTRO SERVICIO";
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
      { data: "payment_date" },
      { data: "waytopay" },
      { data: "observation", visible: false },
      {
        data: "state",
        render: function (data) {
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
      {
        data: "encrypt",
        className: "text-center",
        aWidth: "40px",
        render(data, type, full) {
          const options = {
            options: {
              title: "Opciones",
              icon: "far fa-sun",
              onclick: `print_options('${data}')`,
            },
            email: {
              title: "Email",
              icon: "fa fa-share-square",
              onclick: `onSendMail('${data}', '${full.encrypt_client}', '${full.count_state}')`,
            },
          };
          const config = iconRender(options.options);
          const email = iconRender(options.email);
          const configDown = optionRender(options.options);
          const emailDown = optionRender(options.email);
          return `
            <div class="hidden-sm hidden-xs action-buttons">
              ${config}
              ${email}
            </div>
            <div class="hidden-md hidden-lg"><div class="dropdown">
              <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                ${configDown}
                ${emailDown}
              </div>
              </div>
            </div>
            `;
        },
      },
    ],
  });

const onOpenTicket = () => {
  $("#btn-open-ticket").on("click", () => {
    const phone = $("#mobile").val();
    const contry = $("#contry").val();
    renderLoading(true);
    axios
      .post(`${base_url}/consultas/validation/${contry}${phone}`)
      .then(({ data }) => {
        if (!data.success) throw new Error(data.message);
        alert_msg("success", `El código se envío a ${contry}${phone}`);
        $("#ticket-modal-validation").modal("show");
      })
      .catch((err) => alert_msg("error", err.message))
      .finally(() => renderLoading(false));
  });
};

const onValidateTicket = () => {
  $("#ticket-modal-form").on("submit", (e) => {
    e.preventDefault();
    renderLoading(true);
    const code = $("#code").val();
    axios
      .post(`${base_url}/consultas/compareValidation/${code}`)
      .then(({ data }) => {
        if (!data.success) throw new Error(data.message);
        alert_msg("success", data.message);
        $("#ticket-modal").modal("show");
        $("#ticket-modal-validation").modal("hide");
        $("#attention_date").datetimepicker({ locale: "es" });
        $("#attention_date").val(moment().format("DD/MM/YYYY H:mm"));
        listIncidents();
      })
      .catch((err) => alert_msg("error", err.message))
      .finally(() => renderLoading(false));
  });
};

function onValue() {
  document.getElementById("fvalue").addEventListener("keyup", () => {
    const value = $("#fvalue").val() || "";
    const isDisabled = value?.length >= 8 ? false : true;
    $("#btn-save").prop("disabled", isDisabled);
  });
}

function onNewQuery() {
  $("#btn-new-query").on("click", () => {
    $("#content-info").prop("style", "display: none");
    $("#content-query").prop("style", "display: block");
    $("#result").prop("style", "display: none");
    $("#ftype").prop("disabled", false);
    $("#fvalue").prop("disabled", false);
    $("#fvalue").val("");
  });
}

function onSearch() {
  document.getElementById("consulta").addEventListener("submit", (e) => {
    e.preventDefault();
    renderLoading(true);
    $("#result").prop("style", "display: none");
    const value = $("#fvalue").val();
    const type = $("#ftype").val();
    axios
      .get(`${base_url}/consultas/list_bills?type=${type}&value=${value}`)
      .then(({ data }) => {
        if (!data.success) throw new Error(data.message);
        showItem(data);
      })
      .catch((err) => {
        alert_msg("error", err.message);
        $("#result").prop("style", "display: none");
      })
      .finally(() => renderLoading(false));
  });
}

function showItem(data) {
  const message = `Resultados de: ${data.client.cliente} (${data.client.document})`;
  $("#result").prop("style", "display: block");
  $("#text-result").text(message);
  $("#clientId").val(data.client.id);
  $("#mobile").val(data.client.mobile);
  $("#contry").val(data.business.country_code);
  $("#btn-open-ticket").prop("disabled", false);
  $("#ftype").prop("disabled", true);
  $("#fvalue").prop("disabled", true);
  $("#content-query").prop("style", "display: none");
  $("#content-info").prop("style", "display: block");
  renderTable(data.data).render();
}

function listIncidents() {
  axios
    .get(`${base_url}/consultas/list_incidents`)
    .then(({ data }) => {
      const root = document.getElementById("listAffairs");
      root.innerHTML = "";
      data?.forEach((item) => {
        const component = document.createElement("option");
        component.value = item.id;
        component.text = item.incident;
        root.appendChild(component);
      });
    })
    .catch(() => alert_msg("error", "No se pudo obtener las incidencias"));
}

function saveTicket() {
  $("#ticket-form").on("submit", (e) => {
    e.preventDefault();
    renderLoading(true);
    const data = new FormData(document.getElementById("ticket-form"));
    const clientId = $("#clientId").val();
    let attention_date = $("#attention_date").val() || "";
    let [date, time] = attention_date.split(" ");
    date = moment(date, "DD/MM/YYYY").format("YYYY-MM-DD");
    data.set("clientid", clientId);
    data.set("attention_date", `${date} ${time}`);
    axios
      .post(`${base_url}/consultas/save_incident`, data)
      .then(({ data }) => {
        if (!data.success) throw new Error(data.message);
        $("#ticket-modal").modal("hide");
        alert_msg("success", data.message);
      })
      .catch((err) => alert_msg("error", err.message))
      .finally(() => renderLoading(false));
  });
}

function print_options(billId) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  axios
    .get(`${base_url}/customers/view_bill/${billId}`)
    .then(({ data }) => {
      if (data.status != "success") {
        throw new Error("Error al obtener los datos.");
      }
      $("#modal-voucher").modal("show");
      const info = data.data;
      document.querySelector(
        "#text-title-voucher"
      ).innerHTML = `${info.bill.voucher} Nº ${info.bill.correlative}`;
      document.querySelector("#idbillvoucher").value = info.bill.encrypt_bill;
      document.querySelector(
        "#text_country"
      ).innerHTML = `+ ${info.business.country_code}`;
      document.querySelector("#country_code").value =
        info.business.country_code;
      document.querySelector("#bill_number_client").value = info.bill.mobile;
      document.querySelector("#msg").value = info.message_wsp;
    })
    .catch((err) => alert_msg("error", err.message));
}

function onSendWhatsapp() {
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
}

function onSendMail(idbill, idclient, type) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  loading.style.display = "flex";
  axios
    .get(`${base_url}/customers/send_email/${idbill}/${idclient}/${type}`)
    .then(({ data }) => {
      if (data.status === "error") {
        throw new Error(data.msg);
      } else if (data.status === "success") {
        alert_msg("success", data.msg);
      } else {
        alert_msg("info", data.msg);
      }
    })
    .catch((err) => {
      alert_msg("error", err.message);
    })
    .finally(() => {
      loading.style.display = "none";
    });
}

function onPrintTicket() {
  $("#btn-print_ticket").on("click", function () {
    var idbill = document.querySelector("#idbillvoucher").value;
    window.open(`${base_url}/customers/print_voucher/${idbill}`, "_blank");
  });
}

function onTicket() {
  $("#btn-ticket").on("click", function () {
    var idbill = document.querySelector("#idbillvoucher").value;
    window.open(
      `${base_url}/customers/bill_voucher/${idbill}/ticket`,
      "_blank"
    );
  });
}

function onA4() {
  $("#btn-a4").on("click", function () {
    var idbill = document.querySelector("#idbillvoucher").value;
    window.open(`${base_url}/customers/bill_voucher/${idbill}/a4"`, "_blank");
  });
}

document.addEventListener("DOMContentLoaded", () => {
  onSearch();
  onNewQuery();
  onOpenTicket();
  onValue();
  saveTicket();
  onValidateTicket();
  onSendWhatsapp();
  onPrintTicket();
  onTicket();
  onA4();
});
