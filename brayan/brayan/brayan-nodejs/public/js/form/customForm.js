function getInputs() {
  return {
    opcion: $("#opcion"),
    ip: $("#mobileOp"),
    ap_cliente: $("#ap_cliente_value"),
    ap_cliente_id: $("#ap_cliente_id"),
    nap_cliente: $("#nap_cliente_id"),
    nap_cliente_nombre: $("#nap_cliente_nombre"),
    user: $("#ppoe_usuario"),
    password: $("#ppoe_password"),
  };
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

function checkDocument() {
  const id = $("#listTypes").val();
  const tmpData = $("#documentData").val();
  if (!id || !tmpData) return;
  const documentData = JSON.parse(tmpData || []);
  const currentType = documentData.find((d) => d.id == id);
  if (!currentType) return;
  const component = document.getElementById("document");
  // requerido
  component.setAttribute(
    "data-parsley-required",
    currentType.is_required ? true : false
  );
  // maximo
  component.setAttribute("data-parsley-maxlength", currentType.maxlength);
  component.setAttribute("maxlength", currentType.maxlength);
}

function clearInputs(inputArray = []) {
  inputArray.forEach((attr) => {
    const input = getInputs()[attr];
    if (input) input.val("");
  });
}

function is_ip_option(show = false) {
  const content = $("#content-is_ip");
  const ip = document.getElementById("mobileOp");
  if (show) {
    content.prop("style", "display: flex");
    ip_option(ip.value ? true : false, true);
  } else {
    content.prop("style", "display: none");
    ip_option(false);
  }
}

function ip_option(show = false, required = false) {
  const content = $("#content-ip");
  const label = document.getElementById("label-ip");
  const ip = document.getElementById("mobileOp");
  if (show) {
    content.prop("style", "display: flex");
    label.style = `display: ${required ? "inline" : "none"}`;
    ip.setAttribute("type", "text");
    ip.setAttribute("data-parsley-required", required ? "true" : "false");
  } else {
    content.prop("style", "display: none");
    label.style = "display: none";
    ip.setAttribute("type", "hidden");
    ip.setAttribute("data-parsley-required", "false");
  }
}

function ap_cliente_option(show = false, required = false) {
  const content = $("#content-ap_cliente_id");
  const label = document.getElementById("label-ap_cliente_id");
  const apCliente = document.getElementById("ap_cliente_id");
  if (show) {
    content.prop("style", "display: flex");
    label.style = `display: ${required ? "inline" : "none"}`;
    apCliente.setAttribute("type", "text");
    apCliente.setAttribute(
      "data-parsley-required",
      required ? "true" : "false"
    );
  } else {
    content.prop("style", "display: none");
    label.style = "display: none";
    apCliente.setAttribute("type", "hidden");
    apCliente.setAttribute("data-parsley-required", "false");
  }
}

function nap_cliente_option(show = false, required = false) {
  const content = $("#content-nap_cliente_id");
  const label = document.getElementById("label-nap_cliente_id");
  const napCliente = document.getElementById("nap_cliente_nombre");
  if (show) {
    content.prop("style", "display: flex");
    label.style = `display: ${required ? "inline" : "none"}`;
    napCliente.setAttribute("type", "text");
    napCliente.setAttribute(
      "data-parsley-required",
      required ? "true" : "false"
    );
  } else {
    content.prop("style", "display: none");
    label.style = "display: none";
    napCliente.setAttribute("type", "hidden");
    napCliente.setAttribute("data-parsley-required", "false");
  }
}

function ppoe_usuario_option(show = false, required = false) {
  const content = $("#content-ppoe_usuario");
  const label = document.getElementById("label-ppoe_usuario");
  const ppoeUsuario = document.getElementById("ppoe_usuario");
  if (show) {
    content.prop("style", "display: flex");
    label.style = `display: ${required ? "inline" : "none"}`;
    ppoeUsuario.setAttribute("type", "text");
    ppoeUsuario.setAttribute(
      "data-parsley-required",
      required ? "true" : "false"
    );
  } else {
    content.prop("style", "display: none");
    label.style = "display: none";
    ppoeUsuario.setAttribute("type", "hidden");
    ppoeUsuario.setAttribute("data-parsley-required", "false");
  }
}

function ppoe_password_option(show = false, required = false) {
  const content = $("#content-ppoe_password");
  const label = document.getElementById("label-ppoe_password");
  const ppoePassword = document.getElementById("ppoe_password");
  if (show) {
    content.prop("style", "display: flex");
    label.style = `display: ${required ? "inline" : "none"}`;
    ppoePassword.setAttribute("type", "text");
    ppoePassword.setAttribute(
      "data-parsley-required",
      required ? "true" : "false"
    );
  } else {
    content.prop("style", "display: none");
    label.style = "display: none";
    ppoePassword.setAttribute("type", "hidden");
    ppoePassword.setAttribute("data-parsley-required", "false");
  }
}

function hiddenAll() {
  is_ip_option(false);
  ip_option(false);
  ap_cliente_option(false);
  nap_cliente_option(false);
  ppoe_usuario_option(false);
  ppoe_password_option(false);
}

function changeOpcion() {
  const { opcion } = getInputs();
  if (opcion.val() === "NINGUNO") {
    hiddenAll();
    is_ip_option(true);
    clearInputs([
      "ap_cliente",
      "ap_cliente_id",
      "nap_cliente",
      "nap_cliente_nombre",
      "user",
      "password",
    ]);
  } else if (opcion.val() === "WISP") {
    hiddenAll();
    ip_option(true, true);
    ap_cliente_option(true, true);
    clearInputs(["nap_cliente", "nap_cliente_nombre", "user", "password"]);
  } else if (opcion.val() === "ISP") {
    hiddenAll();
    ip_option(true, true);
    nap_cliente_option(true, true);
    clearInputs(["ap_cliente", "ap_cliente_id", "user", "password"]);
  } else if (opcion.val() === "PPOE") {
    hiddenAll();
    nap_cliente_option(true, true);
    ppoe_usuario_option(true, true);
    ppoe_password_option(true, true);
    clearInputs(["ip", "ap_cliente", "ap_cliente_id"]);
  }
}

function changeListTypes() {
  $("#listTypes").on("change", () => {
    $("#document").val("");
    $("#document").focus();
    checkDocument();
  });
}

function getClient() {
  const dataClient = document.getElementById("dataClient");
  if (!dataClient.innerText) return null;
  return JSON.parse(dataClient.innerText);
}

function onCheckIp() {
  const is_ip = document.getElementById("is_ip");
  const isChecked = is_ip.checked;
  ip_option(isChecked, true);
}

let loadingNap = { value: false };

function search_nap_cliente() {
  searchComponent("nap_cliente_nombre", "nap", loadingNap)
    .then(
      ({
        input,
        value,
        renderNotFound,
        renderItem,
        closeContainer,
        clearBox,
        closeEvent,
      }) => {
        const url = `${base_url}/cajaNap/search_puertos?querySearch=${value}`;

        const actionItem = (input, item) => {
          input.value = item.nombre;
          $("#nap_cliente_id").val(item.id);
          closeContainer();
        };

        axios
          .get(url)
          .then(({ data }) => {
            if (!data.length) throw new Error();
            clearBox();
            data.forEach((ap) => {
              const item = renderItem(ap.nombre);
              item.id = `ap-${ap.id}`;
              item.onclick = () => actionItem(input, ap);
            });
          })
          .catch(() => renderNotFound())
          .finally(() => closeEvent());
      }
    )
    .catch((err) => alert_msg("warning", err.message));
}

document.addEventListener("DOMContentLoaded", () => {
  changeOpcion();
  list_documents();
  changeListTypes();
});
