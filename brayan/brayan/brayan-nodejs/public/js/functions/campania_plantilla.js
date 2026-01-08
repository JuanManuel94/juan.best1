function getTituloInput() {
  return $("#titulo");
}

function getContenidoInput() {
  return $("#contenido");
}

function getDataNode(messageId) {
  const id = `planilla_${messageId}`;
  return window.document.getElementById(id);
}

function getTituloNode(messageId) {
  const id = `titulo_${messageId}`;
  return window.document.getElementById(id);
}

function getData(messageId) {
  return JSON.parse(getDataNode(messageId).innerText);
}

function modal(messageId) {
  const wsp = getData(messageId);
  document.querySelector("#text-title").innerHTML = `Plantilla WSP - ${wsp.id}`;
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#transactions").reset();
  $("#transactions").parsley().reset();
  $("#modal-action").modal("show");

  // settings
  getTituloInput().val(wsp.titulo);
  getContenidoInput().val(wsp.contenido);

  // info list
  if (["PAYMENT_PENDING", "SUSPENDED_NOTIFY"].includes(wsp.id)) {
    $(".list_debts").attr("style", null);
  } else {
    $(".list_debts").attr("style", "display: none");
  }

  if (["AVISO_INSTALL", "SUPPORT_TECNICO"].includes(wsp.id)) {
    $(".info_debt").attr("style", "display: none");
  } else {
    $("info_debt").attr("style", null);
  }

  if (["SUPPORT_TECNICO"].includes(wsp.id)) {
    $(".info_ticket").attr("style", null);
  } else {
    $(".info_ticket").attr("style", "display: none");
  }

  if (["PAGO_MASSIVE"].includes(wsp.id)) {
    $(".info_payment_massive").attr("style", null);
    $(".info_payment_total").attr("style", null);
    $(".info_payment_month").attr("style", null);
  } else {
    $(".info_payment_massive").attr("style", "display: none");
    $(".info_payment_total").attr("style", "display: none");
    $(".info_payment_month").attr("style", "display: none");
  }

  if (["PAYMENT_CONFIRMED"].includes(wsp.id)) {
    $(".info_payment").attr("style", null);
    $(".info_payment_total").attr("style", null);
    $(".info_payment_month").attr("style", null);
  } else {
    $(".info_payment").attr("style", "display: none");
    $(".info_payment_total").attr("style", "display: none");
    $(".info_payment_month").attr("style", "display: none");
  }

  if (["PROMISE_CREATED"].includes(wsp.id)) {
    $(".info_promise").attr("style", null);
  } else {
    $(".info_promise").attr("style", "display: none");
  }

  // actions
  const component = document.getElementById("transactions");
  component.onsubmit = (e) => {
    e.preventDefault();
    saveWsp(messageId);
  };
}

function saveWsp(id) {
  const payload = {
    titulo: getTituloInput().val(),
    contenido: getContenidoInput().val(),
  };

  axios
    .post(`${base_url}/campania/find_business_wsp/${id}`, payload)
    .then(({ data }) => {
      const tmpData = Object.assign(getData(id), payload);
      getDataNode(id).innerText = JSON.stringify(tmpData);
      getTituloNode(id).innerText = payload.titulo;
      alert_msg(data.status, data.message);
    })
    .catch((err) => {
      console.log(err);
      alert_msg("error", "No se pudo guardar los datos");
    });
}
